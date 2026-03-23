<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Status;
use App\Models\User;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Http\Resources\TicketResource;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TicketCreatedNotification;
use App\Notifications\StatusChangedNotification;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->integer('per_page', 10);
        $cacheKey = 'tickets:' . $request->user()->id . ':' . md5($request->fullUrl());

        $tickets = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($request, $perPage) {
            $query = Ticket::query()
                ->with(['category', 'priority', 'status', 'user', 'assignedAgent'])
                ->latest();

            if ($request->user()->role === 'customer') {
                $query->where('user_id', $request->user()->id);
            }

            if ($request->filled('status_id')) {
                $query->where('status_id', $request->get('status_id'));
            }

            if ($request->filled('priority_id')) {
                $query->where('priority_id', $request->get('priority_id'));
            }

            if ($request->filled('category_id')) {
                $query->where('category_id', $request->get('category_id'));
            }

            if ($request->filled('assigned_to')) {
                $query->where('assigned_to', $request->get('assigned_to'));
            }

            if ($search = $request->get('search')) {
                $query->where(function ($q) use ($search) {
                    $q->where('subject', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            return $query->paginate($perPage);
        });

        return TicketResource::collection($tickets);
    }

    public function store(StoreTicketRequest $request)
    {
        $this->authorize('create', Ticket::class);
        Cache::flush();

        $openStatus = Status::where('name', 'open')->firstOrFail();
        $agentId = User::where('role', 'agent')->inRandomOrder()->value('id');

        $ticket = Ticket::create([
            'subject' => $request->subject,
            'description' => $request->description,
            'user_id' => $request->user()->id,
            'assigned_to' => $agentId,
            'category_id' => $request->category_id,
            'priority_id' => $request->priority_id,
            'status_id' => $openStatus->id,
        ]);

        $this->storeAttachments($ticket, $request);
        $ticket->load(['category', 'priority', 'status', 'user', 'assignedAgent']);

        // notify assigned agent and admins
        $notifiables = User::whereIn('role', ['admin'])
            ->when($agentId, fn ($q) => $q->orWhere('id', $agentId))
            ->get();
        Notification::send($notifiables, new TicketCreatedNotification($ticket));

        return response()->json([
            'status' => true,
            'message' => 'Ticket created successfully',
            'data' => new TicketResource($ticket),
        ], 201);
    }

    public function show(Request $request, Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $ticket->load(['category', 'priority', 'status', 'user', 'assignedAgent', 'replies.user', 'replies.attachments', 'attachments']);

        return response()->json([
            'status' => true,
            'message' => 'Ticket fetched successfully',
            'data' => new TicketResource($ticket),
        ], 200);
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);
        Cache::flush();
        $originalStatus = $ticket->status_id;
        $ticket->fill($request->validated());
        $ticket->save();
        $ticket->load(['category', 'priority', 'status', 'user', 'assignedAgent']);

        if ($request->filled('status_id') && $ticket->status_id !== $originalStatus) {
            $notifiables = User::whereIn('id', array_filter([
                $ticket->user_id,
                $ticket->assigned_to,
            ]))->get();
            Notification::send($notifiables, new StatusChangedNotification($ticket));
        }

        return response()->json([
            'status' => true,
            'message' => 'Ticket updated successfully',
            'data' => new TicketResource($ticket),
        ], 200);
    }

    public function destroy(Request $request, Ticket $ticket)
    {
        $this->authorize('delete', $ticket);
        Cache::flush();

        $ticket->delete();

        return response()->json([
            'status' => true,
            'message' => 'Ticket deleted successfully',
        ], 200);
    }

    public function restore(Request $request, int $ticketId)
    {
        $ticket = Ticket::withTrashed()->findOrFail($ticketId);
        $this->authorize('restore', $ticket);
        Cache::flush();

        $ticket->restore();

        return response()->json([
            'status' => true,
            'message' => 'Ticket restored successfully',
        ], 200);
    }

    public function forceDelete(Request $request, int $ticketId)
    {
        $ticket = Ticket::withTrashed()->findOrFail($ticketId);
        $this->authorize('forceDelete', $ticket);
        Cache::flush();

        $ticket->forceDelete();

        return response()->json([
            'status' => true,
            'message' => 'Ticket permanently deleted',
        ], 200);
    }

    private function storeAttachments(Ticket $ticket, Request $request): void
    {
        if (! $request->hasFile('attachments')) {
            return;
        }

        foreach ($request->file('attachments') as $file) {
            $path = $file->store('attachments');
            $ticket->attachments()->create([
                'user_id' => $request->user()->id,
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
            ]);
        }
    }
}
