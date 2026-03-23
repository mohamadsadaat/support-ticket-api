<?php

namespace Tests\Feature;

use App\Models\Priority;
use App\Models\Status;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReplyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Status::factory()->create(['name' => 'open']);
        Priority::factory()->create();
    }

    public function test_customer_can_reply_to_own_ticket(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $ticket = Ticket::factory()->for($user)->create([
            'status_id' => Status::first()->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')->postJson("/api/tickets/{$ticket->id}/replies", [
            'body' => 'First reply',
        ]);

        $response->assertCreated()->assertJsonPath('data.body', 'First reply');
    }
}
