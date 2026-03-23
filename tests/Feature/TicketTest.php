<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Priority;
use App\Models\Status;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Status::factory()->create(['name' => 'open']);
        Category::factory()->create();
        Priority::factory()->create();
    }

    public function test_customer_creates_ticket(): void
    {
        $user = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/tickets', [
            'subject' => 'Need help',
            'description' => 'Problem description',
        ]);

        $response->assertCreated()->assertJsonPath('data.subject', 'Need help');
    }

    public function test_customer_sees_only_own_tickets(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $other = User::factory()->create(['role' => 'customer']);

        $this->actingAs($user, 'sanctum')->postJson('/api/tickets', [
            'subject' => 'Mine',
            'description' => 'Desc',
        ]);

        $this->actingAs($other, 'sanctum')->postJson('/api/tickets', [
            'subject' => 'Other',
            'description' => 'Desc',
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/tickets');

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }
}
