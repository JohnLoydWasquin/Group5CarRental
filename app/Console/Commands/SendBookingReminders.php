<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Services\SmsService;
use Carbon\Carbon;

class SendBookingReminders extends Command
{
    protected $signature = 'bookings:send-reminders';
    protected $description = 'Send reminders to customers about upcoming bookings';
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        parent::__construct();
        $this->smsService = $smsService;
    }

    public function handle()
    {
        $now = Carbon::now();

        // Get bookings that are Confirmed or Ongoing
        $bookings = Booking::whereIn('booking_status', ['Confirmed', 'Ongoing'])->get();

        foreach ($bookings as $booking) {
            $return = Carbon::parse($booking->return_datetime);

            // ✅ 1-day reminder (for rentals ending soon, 3–4 hrs before)
            if (!$booking->reminder_sent_3hrs && $now->diffInHours($return, false) <= 27 && $now->diffInHours($return, false) >= 23) {
                $this->sendReminder($booking, '1-day');
                $booking->reminder_sent_3hrs = 1;
                $booking->save();
            }

            // ✅ 1-week reminder (3 days before end)
            if (!$booking->reminder_sent_3days && $now->diffInDays($return, false) <= 7 && $now->diffInDays($return, false) >= 4) {
                $this->sendReminder($booking, '1-week');
                $booking->reminder_sent_3days = 1;
                $booking->save();
            }

            // ✅ 1-month reminder (3 weeks before end)
            if (!$booking->reminder_sent_1week && $now->diffInDays($return, false) <= 30 && $now->diffInDays($return, false) >= 22) {
                $this->sendReminder($booking, '1-month');
                $booking->reminder_sent_1week = 1;
                $booking->save();
            }
        }

        $this->info('Booking reminders processed successfully.');
    }

    protected function sendReminder($booking, $type)
    {
        $vehicleName = $booking->vehicle->Brand . ' ' . $booking->vehicle->Model;

        if ($booking->user->phone) {
            $phone = $this->formatPhoneNumber($booking->user->phone);

            $message = "Hello {$booking->user->name}, this is a {$type} reminder: your booking #{$booking->booking_id} for {$vehicleName} is ending on " .
                Carbon::parse($booking->return_datetime)->format('M d, Y H:i') . ". Please plan accordingly.";

            $this->smsService->sendMessage($phone, $message);
        }
    }

    protected function formatPhoneNumber($phone)
    {
        // Make sure phone starts with 0
        $phone = preg_replace('/\D/', '', $phone);

        if (substr($phone, 0, 3) === '63') {
            $phone = '0' . substr($phone, 2);
        }

        if (substr($phone, 0, 1) !== '0') {
            $phone = '0' . $phone;
        }

        return $phone;
    }
}
