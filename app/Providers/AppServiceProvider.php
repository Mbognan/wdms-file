<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Enums\UserType;
use Illuminate\Support\Facades\View;

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
        View::composer('admin.layouts.sidebar', function ($view) {

            $user = auth()->user();

            $unverifiedCount = 0;

            if ($user?->user_type === UserType::ADMIN) {
                $unverifiedCount = User::whereIn('user_type', [
                    UserType::INTERNAL_ASSESSOR,
                    UserType::ACCREDITOR,
                ])
                ->where('status', 'Pending')
                ->count();
            }

            if ($user?->user_type === UserType::DEAN) {
                $unverifiedCount = User::where('user_type', UserType::TASK_FORCE)
                    ->where('status', 'Pending')
                    ->count();
            }

            $view->with('unverifiedCount', $unverifiedCount);
        });
    }
}
