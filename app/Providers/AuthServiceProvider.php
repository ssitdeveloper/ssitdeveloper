<?php

namespace App\Providers;

use App\Models\Payment;
use App\Models\Question;
use App\Models\TestAttempt;
use App\Models\User;
use App\Policies\PaymentPolicy;
use App\Policies\QuestionPolicy;
use App\Policies\TestAttemptPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Payment::class => PaymentPolicy::class,
        Question::class => QuestionPolicy::class,
        TestAttempt::class => TestAttemptPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Define gates for common permissions
        Gate::define('is-admin', function (User $user) {
            return $user->role->value === 'admin';
        });

        Gate::define('is-student', function (User $user) {
            return $user->role->value === 'student';
        });

        Gate::define('is-instructor', function (User $user) {
            return $user->role->value === 'instructor';
        });

        Gate::define('is-moderator', function (User $user) {
            return $user->role->value === 'moderator';
        });

        Gate::define('manage-questions', function (User $user) {
            return $user->role->value === 'admin' || $user->role->value === 'instructor';
        });

        Gate::define('manage-tests', function (User $user) {
            return $user->role->value === 'admin' || $user->role->value === 'instructor';
        });

        Gate::define('view-analytics', function (User $user) {
            return $user->role->value === 'admin' || $user->role->value === 'instructor';
        });
    }
}
