<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::allAsArray();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $allowed = [
            'site_name','site_tagline','site_email','site_phone','site_description',
            'breaking_ticker','footer_tagline','articles_per_page',
            'facebook_url','instagram_url','youtube_url','twitter_url',
        ];
        foreach ($allowed as $key) {
            if ($request->has($key)) {
                Setting::set($key, $request->input($key, ''));
            }
        }
        return back()->with('success', '✅ Settings saved successfully.');
    }
}
