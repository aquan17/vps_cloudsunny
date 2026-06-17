<?php

namespace App\Services;

class CloudSunnyProxyPricingService
{
    public function enrichProducts(array $products): array
    {
        return collect($products)
            ->map(function (array $product) {
                $product['price'] = $this->priceFor($product);
                $product['provider_price'] = $this->providerCostFor($product);
                $product['data_pricing']['monthly'] = $product['price'];
                $product['title'] = $this->nameFor($product);

                return $product;
            })
            ->values()
            ->all();
    }

    public function priceFor(array $product, string $billingCycle = 'monthly'): int
    {
        $productId = (int) ($product['id'] ?? 0);
        $configured = config("cloudsunny.proxy_plans.{$productId}.price_per_month");

        if ($configured !== null) {
            return (int) $configured;
        }

        return (int) ($product['data_pricing'][$billingCycle] ?? 0);
    }

    public function providerCostFor(array $product, string $billingCycle = 'monthly'): int
    {
        $productId = (int) ($product['id'] ?? 0);
        $configured = config("cloudsunny.proxy_plans.{$productId}.provider_price_per_month");

        if ($configured !== null) {
            return (int) $configured;
        }

        return (int) ($product['data_pricing'][$billingCycle] ?? 0);
    }

    public function nameFor(array $product): string
    {
        $productId = (int) ($product['id'] ?? 0);

        return config("cloudsunny.proxy_plans.{$productId}.name")
            ?? (string) ($product['title'] ?? ('Proxy #' . $productId));
    }
}
