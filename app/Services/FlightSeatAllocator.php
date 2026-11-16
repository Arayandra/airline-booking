<?php

namespace App\Services;

class FlightSeatAllocator
{
    /**
     * Return the first free numeric seat number for a flight.
     *
     * Existing ticket rows reserve seats unless their status is cancelled.
     * A null result means every seat in the configured capacity is occupied.
     */
    public function nextAvailableSeatNumber(int $capacity, array $occupiedSeatNumbers): ?string
    {
        if ($capacity < 1) {
            return null;
        }

        $occupied = [];
        foreach ($occupiedSeatNumbers as $seatNumber) {
            $occupied[(string) $seatNumber] = true;
        }

        for ($seat = 1; $seat <= $capacity; $seat++) {
            $candidate = (string) $seat;
            if (!isset($occupied[$candidate])) {
                return $candidate;
            }
        }

        return null;
    }
}
