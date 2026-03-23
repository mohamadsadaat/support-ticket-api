<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function view(User $user, Ticket $ticket): bool
    {
        if ($user->role === 'admin' || $user->role === 'agent') {
            return true;
        }

        return $ticket->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'agent', 'customer'], true);
    }

    public function update(User $user, Ticket $ticket): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'agent') {
            return $ticket->assigned_to === $user->id || $ticket->status?->name !== 'closed';
        }

        return $ticket->user_id === $user->id;
    }

    public function delete(User $user, Ticket $ticket): bool
    {
        return $user->role === 'admin' || $ticket->user_id === $user->id;
    }

    public function restore(User $user, Ticket $ticket): bool
    {
        return $user->role === 'admin';
    }

    public function forceDelete(User $user, Ticket $ticket): bool
    {
        return $user->role === 'admin';
    }

    public function reply(User $user, Ticket $ticket): bool
    {
        if ($user->role === 'admin' || $user->role === 'agent') {
            return true;
        }

        return $ticket->user_id === $user->id;
    }
}
