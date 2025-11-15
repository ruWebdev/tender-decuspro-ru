<?php

namespace App\Providers;

use App\Models\Proposal;
use App\Models\Tender;
use App\Policies\ProposalPolicy;
use App\Policies\TenderPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
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
        Vite::prefetch(concurrency: 3);

        Gate::policy(Tender::class, TenderPolicy::class);
        Gate::policy(Proposal::class, ProposalPolicy::class);
    }
}
