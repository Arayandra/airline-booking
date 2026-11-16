<?php

namespace Tests\Feature;

use App\Models\Airline;
use App\Models\Airport;
use App\Models\Flight;
use App\Models\Plane;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class TicketSeatLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_reserves_exactly_one_unique_seat_and_rejects_multiple_seats(): void
    {
        $user = $this->createUser();
        $flight = $this->createFlight();

        $this->actingAs($user)
            ->postJson('/dashboard/tickets/book', [
                'flight_id' => $flight->id,
                'seats' => 2,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('seats');

        $this->assertDatabaseCount('tickets', 0);
        $this->assertDatabaseHas('flights', [
            'id' => $flight->id,
            'remain_seats' => 2,
        ]);

        $this->postJson('/dashboard/tickets/book', [
            'flight_id' => $flight->id,
            'seats' => 1,
        ])->assertStatus(201);

        $this->assertDatabaseHas('tickets', [
            'flight_id' => $flight->id,
            'user_id' => $user->id,
            'seat_number' => '1',
            'status' => 0,
        ]);
        $this->assertDatabaseHas('flights', [
            'id' => $flight->id,
            'remain_seats' => 1,
        ]);
    }

    public function test_admin_reapproval_allocates_a_new_seat_when_the_old_one_was_reused(): void
    {
        $firstUser = $this->createUser();
        $secondUser = $this->createUser();
        $admin = $this->createUser(true);
        $flight = $this->createFlight();

        $this->actingAs($firstUser)
            ->postJson('/dashboard/tickets/book', [
                'flight_id' => $flight->id,
                'seats' => 1,
            ])->assertStatus(201);

        $firstTicket = Ticket::where('user_id', $firstUser->id)
            ->where('flight_id', $flight->id)
            ->firstOrFail();

        $this->actingAs($admin)
            ->postJson('/dashboard/tickets/change-status/'.$firstTicket->id, [
                'status' => 'canceled',
            ])->assertOk();

        $this->actingAs($secondUser)
            ->postJson('/dashboard/tickets/book', [
                'flight_id' => $flight->id,
                'seats' => 1,
            ])->assertStatus(201);

        $secondTicket = Ticket::where('user_id', $secondUser->id)
            ->where('flight_id', $flight->id)
            ->firstOrFail();

        $this->actingAs($admin)
            ->postJson('/dashboard/tickets/change-status/'.$firstTicket->id, [
                'status' => 'approve',
            ])->assertOk();

        $this->assertDatabaseHas('tickets', [
            'id' => $firstTicket->id,
            'seat_number' => '2',
            'status' => 1,
        ]);
        $this->assertDatabaseHas('tickets', [
            'id' => $secondTicket->id,
            'seat_number' => '1',
            'status' => 0,
        ]);
        $this->assertDatabaseHas('flights', [
            'id' => $flight->id,
            'remain_seats' => 0,
        ]);
        $this->assertSame(
            2,
            Ticket::where('flight_id', $flight->id)
                ->where('status', '<>', 2)
                ->distinct()
                ->count('seat_number')
        );
    }

    private function createUser(bool $admin = false): User
    {
        return User::create([
            'name' => $admin ? 'Test Admin' : 'Test Passenger',
            'email' => Str::uuid().'@example.test',
            'phone' => '08'.Str::replace('-', '', (string) Str::uuid()),
            'password' => Hash::make('test-password'),
            'is_admin' => $admin,
            'is_accepted' => true,
        ]);
    }

    private function createFlight(): Flight
    {
        $airline = Airline::create([
            'name' => 'Test Airline',
            'code' => 'TA',
        ]);

        $plane = Plane::create([
            'airline_id' => $airline->id,
            'name' => 'Test Aircraft',
            'code' => 'TA-001',
            'capacity' => 2,
        ]);

        $origin = Airport::create(['name' => 'Test Origin']);
        $destination = Airport::create(['name' => 'Test Destination']);

        return Flight::create([
            'flight_number' => 'TA-100',
            'airline_id' => $airline->id,
            'plane_id' => $plane->id,
            'origin_id' => $origin->id,
            'destination_id' => $destination->id,
            'departure' => now()->addDays(3),
            'arrival' => now()->addDays(3)->addHours(2),
            'seats' => 2,
            'remain_seats' => 2,
            'status' => true,
            'price' => 250000,
        ]);
    }
}
