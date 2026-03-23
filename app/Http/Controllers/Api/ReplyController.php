<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReplyRequest;
use App\Http\Resources\ReplyResource;
use App\Models\Reply;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ReplyAddedNotification;
use Illuminate\Support\Facades\Cache;

class ReplyController extends Controller
{
    public function index(Request $request, Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $replies = $ticket->replies()
            ->with(['user', 'attachments'])
            ->latest()
            ->paginate(10);

        return ReplyResource::collection($replies);
    }

    public function store(StoreReplyRequest $request, Ticket $ticket)
    {
        $this->authorize('reply', $ticket);

        if ($ticket->status?->name === 'closed' && $request->user()->role !== 'admin') {
            abort(403, 'Replies are not allowed on closed tickets.');
        }

        Cache::flush();
        $reply = $ticket->replies()->create([
            'user_id' => $request->user()->id,
            'body' => $request->input('body'),
        ]);

        $this->storeAttachments($reply, $request);
        $reply->load(['user', 'attachments']);

        $notifiables = collect([$ticket->user, $ticket->assignedAgent])
            ->filter(fn ($user) => $user && $user->id !== $request->user()->id);
        Notification::send($notifiables, new ReplyAddedNotification($reply));

        return response()->json([
            'status' => true,
            'message' => 'Reply added successfully',
            'data' => new ReplyResource($reply),
        ], 201);
    }

    private function storeAttachments(Reply $reply, Request $request): void
    {
        if (! $request->hasFile('attachments')) {
            return;
        }

        foreach ($request->file('attachments') as $file) {
            $path = $file->store('attachments');
            $reply->attachments()->create([
                'user_id' => $request->user()->id,
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
            ]);
        }
    }
}
