<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    //
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'subject',
        'description',
        'user_id',
        'assigned_to',
        'category_id',
        'priority_id',
        'status_id',
        'resolved_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedAgent()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachmentable');
    }

    protected static function booted(): void
    {
        static::forceDeleted(function (Ticket $ticket) {
            $ticket->attachments()->each->delete();
        });
    }
}
