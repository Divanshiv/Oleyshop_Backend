<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\AI\app\Models\AISetting;

class AIConfigurationController extends Controller
{
    public function index(): View
    {
        $aiSetting = AISetting::first();

        return view('admin-views.business-settings.ai-configuration.index', compact('aiSetting'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'status' => 'nullable|in:on',
            'api_key' => 'required_if:status,on|nullable|string',
            'organization_id' => 'required_if:status,on|nullable|string'
        ]);

        AISetting::UpdateOrCreate(['ai_name' => 'OpenAI'], [
            'api_key' => $request->api_key,
            'organization_id' => $request->organization_id,
            'status' => $request->filled('status') ? 1 : 0
        ]);

        Toastr::success(translate('Information updated successfully!'));

        return back();
    }
}
