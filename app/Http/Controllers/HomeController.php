<?php

namespace App\Http\Controllers;

use App\Models\CloudSunnyAccount;
use App\Services\CloudSunnyPricingService;

class HomeController extends Controller
{
    public function index(CloudSunnyPricingService $pricing)
    {
        $plans = $pricing->getPlans();
        $hasProvider = CloudSunnyAccount::where('is_active', true)->where('is_full', false)->exists()
            || (bool) config('cloudsunny.api_username');

        return view('home', [
            'plans' => $plans,
            'regions' => $pricing->getRegions(),
            'availablePlanIds' => $hasProvider ? array_keys($plans) : [],
        ]);
    }
}
