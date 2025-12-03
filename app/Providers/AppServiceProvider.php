<?php

namespace App\Providers;

use App\Models\Chat;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer(
            ['layouts.authorities.admin', 'layouts.orgStaff.staff'],
            function ($view) {
                $unreadCount = 0;

                if (Auth::check()) {
                    $unreadCount = Chat::where('receiver_id', Auth::id())
                        ->where('is_read', false)
                        ->count();
                }

                $activeBookingsCount = 0;

                if (Auth::check() && Auth::user()->role === 'admin') {
                    $activeBookingsCount = Booking::whereIn(
                        'booking_status',
                        Booking::ACTIVE_STATUSES
                    )->count();
                }

                $view->with('chatUnreadCount', $unreadCount);
                $view->with('activeBookingsCount', $activeBookingsCount);
            }
        );
    }
}
