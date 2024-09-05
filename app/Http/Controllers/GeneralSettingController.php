<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFaviconRequest;
use App\Http\Requests\StoreLogoRequest;
use App\Models\GeneralSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class GeneralSettingController extends Controller
{
    public function logoUpload(StoreLogoRequest $request)
    {
        $logo = GeneralSetting::firstOrCreate([
            'key' => 'logo'
        ]);

        $logo->media()->where('collection_name', 'logo')->get()->map(function ($logoMedia) {
            $logoMedia->delete();
        });

        $logoMediaInfo = $logo->addMedia($request->validated('logo'))->toMediaCollection('logo');

        $logo->update([
            'value' => $logoMediaInfo->original_url,
        ]);

        Cache::put('logo', $logoMediaInfo->original_url);

        return back()->with('message', 'Logo updated successfully');
    }

    public function faviconUpload(StoreFaviconRequest $request)
    {
        $favicon = GeneralSetting::firstOrCreate([
            'key' => 'favicon'
        ]);

        $favicon->media()->where('collection_name', 'favicon')->get()->map(function ($faviconMedia) {
            $faviconMedia->delete();
        });

        $faviconMediaInfo = $favicon->addMedia($request->validated('favicon'))->toMediaCollection('favicon');

        $favicon->update([
            'value' => $faviconMediaInfo->original_url,
        ]);

        Cache::put('favicon', $faviconMediaInfo->original_url);

        return back()->with('message', 'Favicon updated successfully');
    }
}
