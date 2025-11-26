<?php

namespace App\Providers;

use App\Models\Chat;
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

                $view->with('chatUnreadCount', $unreadCount);
            }
        );
    }
}
