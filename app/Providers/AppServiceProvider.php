<?php

namespace App\Providers;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layout.app', function ($view) {
            $user = Auth::user();

            if ($user) {
                $notifications = AuditLog::where('branch_id', $user->branch_id)
                    ->where('action', 'stock_minimum')
                    ->latest()
                    ->limit(5)
                    ->get();

                $view->with([
                    'notifications' => $notifications,
                    'unreadNotificationCount' => $notifications->count(),
                ]);
            } else {
                $view->with([
                    'notifications' => collect(),
                    'unreadNotificationCount' => 0,
                ]);
            }
        });
    }
}
