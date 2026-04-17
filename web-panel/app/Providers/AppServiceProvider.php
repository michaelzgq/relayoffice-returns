<?php

namespace App\Providers;
ini_set('memory_limit', '-1');

use App\CentralLogics\Helpers;
use Barryvdh\DomPDF\Facade\Pdf;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use App\Models\BusinessSetting;
use Throwable;
use Milon\Barcode\Facades\DNS1DFacade;
use Milon\Barcode\Facades\DNS2DFacade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Custom class aliases (facades) used in your app
        $aliases = [
            'Toastr'  => Toastr::class,
            'DNS1D' => DNS1DFacade::class,
            'DNS2D' => DNS2DFacade::class,
            'PDF' => Pdf::class,
        ];

        foreach ($aliases as $alias => $class) {
            if (! class_exists($alias) && class_exists($class)) {
                class_alias($class, $alias);
            }
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

        if (filter_var(env('FORCE_HTTPS', false), FILTER_VALIDATE_BOOL)) {
            URL::forceScheme('https');
        }

        try {
            $timezone = BusinessSetting::where(['key' => 'time_zone'])->first();
            if (isset($timezone)) {
                config(['app.timezone' => $timezone->value]);
                date_default_timezone_set($timezone->value);
            }
        } catch (\Exception $exception) {
        }

        if ((Request::is('admin/auth/login') || Request::is('admin/business-settings*')) && $this->shouldPingLicenseKeeper()) {
            $this->pingLicenseKeeper();
        }
    }

    private function shouldPingLicenseKeeper(): bool
    {
        if (app()->runningInConsole()) {
            return false;
        }

        if (strtolower((string) env('SELF_HOSTED_BOOTSTRAP_MODE', '')) !== '') {
            return false;
        }

        return filled(env(base64_decode('QlVZRVJfVVNFUk5BTUU=')))
            && filled(env(base64_decode('UFVSQ0hBU0VfQ09ERQ==')))
            && filled(env(base64_decode('U09GVFdBUkVfSUQ=')));
    }

    private function pingLicenseKeeper(): void
    {
        if (! function_exists('curl_init')) {
            return;
        }

        $softwareId = env(base64_decode('U09GVFdBUkVfSUQ='));

        if (! is_string($softwareId) || $softwareId === '') {
            return;
        }

        $post = [
            base64_decode('dXNlcm5hbWU=') => env(base64_decode('QlVZRVJfVVNFUk5BTUU=')),
            base64_decode('cHVyY2hhc2Vfa2V5') => env(base64_decode('UFVSQ0hBU0VfQ09ERQ==')),
            base64_decode('c29mdHdhcmVfaWQ=') => base64_decode($softwareId),
            base64_decode('ZG9tYWlu') => preg_replace("#^[^:/.]*[:/]+#i", "", url('/')),
        ];

        try {
            $ch = curl_init(base64_decode('aHR0cHM6Ly9jaGVjay42YW10ZWNoLmNvbS9hcGkvdjEvbG9nLWtlZXBlcg=='));

            if ($ch === false) {
                return;
            }

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
            curl_exec($ch);
            curl_close($ch);
        } catch (Throwable $exception) {
        }
    }
}
