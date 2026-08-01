<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Course;
use App\Models\PaymentSetting;
use App\Policies\CoursePolicy;
use App\Policies\PaymentSettingPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Course::class => CoursePolicy::class,
        PaymentSetting::class => PaymentSettingPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
