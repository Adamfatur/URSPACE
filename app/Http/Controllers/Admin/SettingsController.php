<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Display settings page with tabs.
     */
    public function index()
    {
        $settings = [
            'general' => Setting::getByGroup('general'),
            'seo' => Setting::getByGroup('seo'),
            'forum' => Setting::getByGroup('forum'),
            'features' => Setting::getByGroup('features'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update settings.
     */
    public function update(Request $request)
    {
        $input = $request->except(['_token', 'tab']);

        foreach ($input as $key => $value) {
            // Determine type based on value
            $type = 'string';
            if (is_bool($value) || $value === 'true' || $value === 'false' || $value === '1' || $value === '0') {
                $type = 'boolean';
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
            } elseif (is_numeric($value) && !str_contains($value, '.')) {
                $type = 'integer';
            }

            // Determine group from key prefix
            $group = 'general';
            if (str_starts_with($key, 'seo_')) {
                $group = 'seo';
            } elseif (str_starts_with($key, 'forum_')) {
                $group = 'forum';
            } elseif (str_starts_with($key, 'feature_')) {
                $group = 'features';
            }

            Setting::set($key, $value, $type, $group);
        }

        $tab = $request->input('tab', 'general');

        return redirect()->route('admin.settings.index', ['tab' => $tab])
            ->with('success', 'Pengaturan berhasil disimpan.');
    }
}
