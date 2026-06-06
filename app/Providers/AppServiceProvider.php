<?php

namespace App\Providers;

use App\CentralLogics\Helpers;
use App\Model\Banner;
use App\Model\BusinessSetting;
use App\Model\Category;
use App\Models\LoginSetup;
use App\Observers\BannerObserver;
use App\Observers\BusinessSettingObserver;
use App\Observers\CategoryObserver;
use App\Observers\LoginSetupObserver;
use App\Traits\SystemAddonTrait;
use Barryvdh\DomPDF\PDF;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    use SystemAddonTrait;
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerAliases(
            [
                'PDF' => PDF::class,
                'Helpers' => Helpers::class,
            ]
        );
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        BusinessSetting::observe(BusinessSettingObserver::class);
        LoginSetup::observe(LoginSetupObserver::class);
        Banner::observe(BannerObserver::class);
        Category::observe(CategoryObserver::class);

        //for system addon
        Config::set('addon_admin_routes',$this->get_addon_admin_routes());
        Config::set('get_payment_publish_status',$this->get_payment_publish_status());

        try {
            $timezone = BusinessSetting::where(['key' => 'time_zone'])->first();
            if (isset($timezone)) {
                config(['app.timezone' => $timezone->value]);
                date_default_timezone_set($timezone->value);
            }
        }catch(\Exception $exception){}

        Paginator::useBootstrap();
    }


    protected function registerAliases(array $aliases): void
    {
        $loader = AliasLoader::getInstance();

        foreach ($aliases as $alias => $class) {
            if (class_exists($class)) {
                $loader->alias($alias, $class);
            }
        }
    }

}
