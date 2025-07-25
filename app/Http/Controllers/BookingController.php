<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name'  => 'required|string|max:255',
            'customer_email' => 'required|email',
            'booking_date'   => 'required|date',
            'booking_type'   => 'required|in:full_day,half_day,custom',
            'booking_slot'   => 'nullable|required_if:booking_type,half_day|in:first_half,second_half',
            'booking_from'   => 'nullable|required_if:booking_type,custom|date_format:H:i',
            'booking_to'     => 'nullable|required_if:booking_type,custom|date_format:H:i|after:booking_from',
        ]);

        if ($this->isBookingRepeat($validated)) {
            return back()->withErrors(['error' => 'This booking overlaps with an existing booking!']);
        }

        Booking::create([
            'user_id'        => Auth::user()->id,
            'customer_name'  => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'booking_date'   => $validated['booking_date'],
            'booking_type'   => $validated['booking_type'],
            'booking_slot'   => $validated['booking_slot'] ?? null,
            'booking_from'   => $validated['booking_from'] ?? null,
            'booking_to'     => $validated['booking_to'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Booking successfully created!');
    }

    private function isBookingRepeat($data)
    {
        $query = Booking::whereDate('booking_date', $data['booking_date']);

        if ($data['booking_type'] === 'full_day') {
            return $query->exists();
        }

        if ($data['booking_type'] === 'half_day') {
            if ($query->where('booking_type', 'full_day')->exists()) {
                return true;
            }
            return $query->where('booking_slot', $data['booking_slot'])
                ->orWhere(function ($q) use ($data) {
                    if ($data['booking_slot'] === 'first_half') {
                        $q->where('booking_type', 'custom')
                            ->whereBetween('booking_from', ['06:00', '12:00']);
                    }
                    if ($data['booking_slot'] === 'second_half') {
                        $q->where('booking_type', 'custom')
                            ->whereBetween('booking_from', ['12:00', '18:00']);
                    }
                })
                ->exists();
        }

        if ($data['booking_type'] === 'custom') {
            if ($query->where('booking_type', 'full_day')->exists()) return true;
            if ($query->where('booking_type', 'half_day')->exists()) return true;

            return $query->where(function ($q) use ($data) {
                $q->whereBetween('booking_from', [$data['booking_from'], $data['booking_to']])
                    ->orWhereBetween('booking_to', [$data['booking_from'], $data['booking_to']]);
            })->exists();
        }

        return false;
    }
}
