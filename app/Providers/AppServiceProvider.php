<?php

namespace App\Providers;

use App\Models\Proposal;
use App\Models\Tender;
use App\Policies\ProposalPolicy;
use App\Policies\TenderPolicy;
use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
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

        // Apply dynamic settings (DeepSeek, SMTP) from DB if available
        if (Schema::hasTable('settings')) {
            try {
                $deepseekKey = Setting::get('deepseek_api_key');
                if ($deepseekKey) {
                    Config::set('services.deepseek.token', $deepseekKey);
                }

                $smtpHost = Setting::get('mail_host');
                $smtpPort = Setting::get('mail_port');
                $smtpUser = Setting::get('mail_username');
                $smtpPass = Setting::get('mail_password');
                $smtpEncrypt = Setting::get('mail_encryption');
                $fromAddress = Setting::get('mail_from_address');
                $fromName = Setting::get('mail_from_name');

                if ($smtpHost) {
                    Config::set('mail.mailers.smtp.host', $smtpHost);
                }
                if ($smtpPort) {
                    Config::set('mail.mailers.smtp.port', (int) $smtpPort);
                }
                if ($smtpUser) {
                    Config::set('mail.mailers.smtp.username', $smtpUser);
                }
                if ($smtpPass) {
                    Config::set('mail.mailers.smtp.password', $smtpPass);
                }
                if ($smtpEncrypt) {
                    Config::set('mail.mailers.smtp.encryption', $smtpEncrypt);
                }
                if ($fromAddress) {
                    Config::set('mail.from.address', $fromAddress);
                }
                if ($fromName) {
                    Config::set('mail.from.name', $fromName);
                }
            } catch (\Throwable $e) {
                // ignore boot-time setting errors
            }
        }
    }
}
