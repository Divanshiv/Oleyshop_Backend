<?php

namespace App\Providers;

use App\CentralLogics\Helpers;
use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;

class FirebaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('firebase.firestore', function ($app) {
            $serviceAccountKey = Helpers::get_business_settings('push_notification_service_file_content')??[];
            if(count($serviceAccountKey)>0){
                $serviceAccount = $serviceAccountKey;
                return (new Factory)
                    ->withServiceAccount($serviceAccount)
                    ->createMessaging();
            }
            return false;
        });

        $this->app->singleton('firebase.messaging', function ($app) {
            $serviceAccountKey = \App\CentralLogics\Helpers::get_business_settings('push_notification_service_file_content')??[];

            if (!is_array($serviceAccountKey) || empty($serviceAccountKey['service_file_content'])) {
                return false;
            }

            $jsonDecodeKey = json_decode($serviceAccountKey['service_file_content'], true);

            if(isset($jsonDecodeKey) && count($jsonDecodeKey)>0){
                $serviceAccount = $jsonDecodeKey;
                return (new Factory)
                    ->withServiceAccount($serviceAccount)
                    ->createMessaging();
            }
            return false;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
