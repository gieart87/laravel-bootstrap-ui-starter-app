<?php

use App\Models\Setting;

if (!function_exists('get_setting')) {
    function get_setting($key, $cache = true)
    {
        if (!$cache) {
            return get_setting_value($key);
        }

        return Cache::remember('support-get-setting-'. $key, 5, function () use ($key) {
            return get_setting_value($key);
        });
    }
}

if (!function_exists('get_setting_value')) {
    function get_setting_value($key)
    {
        $setting = Setting::where('setting_key', $key)->first();
        if ($setting) {
            return $setting[$setting->setting_type . '_value'];
        }

        return '';
    }
}

if (!function_exists('get_contact_emails')) {
    function get_contact_emails($firstEmailOnly = true)
    {
        $setting = get_setting('general_email_contact');

        $emails = explode(',', $setting);

        if (!empty($emails) && $firstEmailOnly) {
            return trim($emails[0]);
        }

        if (!empty($emails)) {
            return $setting;
        }

        return '';
    }
}
