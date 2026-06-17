<?php

namespace App\Http\Controllers;

use App\Models\CloudSunnyAccount;
use App\Services\CloudSunnyProxyPricingService;
use App\Services\CloudSunnyPricingService;

class HomeController extends Controller
{
    public function index(
        CloudSunnyPricingService $pricing,
        CloudSunnyProxyPricingService $proxyPricing,
        \App\Services\CloudSunnyApiService $api,
        \App\Services\CloudSunnyAccountRouter $router
    )
    {
        $plans = $pricing->getPlans();
        $hasProvider = CloudSunnyAccount::where('is_active', true)->where('is_full', false)->exists()
            || (bool) config('cloudsunny.api_username');

        $proxyProducts = [];
        $proxyCategories = [];
        try {
            $account = $router->firstActive();
            if ($account) {
                $products = $api->forAccount($account)->listProducts();
                if (isset($products['proxy']) && is_array($products['proxy'])) {
                    $proxyProducts = $proxyPricing->enrichProducts($products['proxy']);
                }
                if (isset($products['proxy_categories']) && is_array($products['proxy_categories'])) {
                    $proxyCategories = collect($products['proxy_categories'])->pluck('title', 'id')->toArray();
                }
            }
        } catch (\Exception $e) {
            // Ignore if API fails
        }

        return view('home', [
            'plans' => $plans,
            'regions' => $pricing->getRegions(),
            'availablePlanIds' => $hasProvider ? array_keys($plans) : [],
            'proxyProducts' => $proxyProducts,
            'proxyCategories' => $proxyCategories,
        ]);
    }
}
