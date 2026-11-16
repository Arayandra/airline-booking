<?php

namespace Tests\Unit;

use App\Services\FlightSeatAllocator;
use PHPUnit\Framework\TestCase;

class FlightSeatAllocatorTest extends TestCase
{
    public function test_it_returns_the_first_unoccupied_seat(): void
    {
        $allocator = new FlightSeatAllocator();

        $this->assertSame('2', $allocator->nextAvailableSeatNumber(4, ['1', '3']));
    }

    public function test_it_returns_null_when_all_seats_are_occupied(): void
    {
        $allocator = new FlightSeatAllocator();

        $this->assertNull($allocator->nextAvailableSeatNumber(3, ['1', '2', '3']));
    }

    public function test_it_handles_an_empty_or_invalid_capacity(): void
    {
        $allocator = new FlightSeatAllocator();

        $this->assertNull($allocator->nextAvailableSeatNumber(0, []));
        $this->assertNull($allocator->nextAvailableSeatNumber(-1, []));
    }

    public function test_it_accepts_numeric_and_string_seat_numbers(): void
    {
        $allocator = new FlightSeatAllocator();

        $this->assertSame('3', $allocator->nextAvailableSeatNumber(3, [1, '2']));
    }
}
