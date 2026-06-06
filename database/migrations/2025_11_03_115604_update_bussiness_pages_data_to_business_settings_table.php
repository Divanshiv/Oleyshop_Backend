<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $businessPages = \Illuminate\Support\Facades\DB::table('business_settings')->whereIn('key', ['about_us', 'terms_and_conditions', 'privacy_policy', 'faq', 'cancellation_policy', 'refund_policy', 'return_policy'])->get();

        foreach ($businessPages as $key => $businessPage)
        {
            $data = ['background_image' => null, 'description' => $businessPage->value ?? ''];
            \Illuminate\Support\Facades\DB::table('business_settings')
                ->where('key', $businessPage->key)
                ->update(['value' => json_encode($data)]);
        }
    }
};
