<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

if (!function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}

if (!function_exists('activeSekolahId')) {
    function activeSekolahId(): ?int
    {
        $user = Auth::user();
        if (!$user) return null;
        if ($user->sekolah_id) return $user->sekolah_id;
        if (setting('app_mode') === 'single') {
            $defaultId = setting('default_sekolah_id');
            return $defaultId ? (int) $defaultId : null;
        }
        return null;
    }
}
