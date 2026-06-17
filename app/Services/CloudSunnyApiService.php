<?php

namespace App\Services;

use App\Models\CloudSunnyAccount;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class CloudSunnyApiService
{
    private ?CloudSunnyAccount $account = null;
    private ?string $token = null;

    public function forAccount(CloudSunnyAccount $account): self
    {
        $this->account = $account;
        $this->token = $account->access_token;

        return $this;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function authenticate(?CloudSunnyAccount $account = null): array
    {
        if ($account) {
            $this->account = $account;
        }

        $username = $this->account ? $this->account->api_username : config('cloudsunny.api_username');
        $app = $this->account ? $this->account->api_app : config('cloudsunny.api_app');
        $secret = $this->account ? $this->account->api_secret : config('cloudsunny.api_secret');

        if (!$username || !$app || !$secret) {
            throw new RuntimeException('SeaServer API credentials are not configured.');
        }

        $data = $this->postPublic('/agency/get-access-token', [
            'api_username' => $username,
            'api_app' => $app,
            'api_secret' => $secret,
        ]);

        $token = $data['access_token'] ?? null;
        if (!$token) {
            throw new RuntimeException('CloudSunny did not return an access token.');
        }

        $this->token = $token;

        if ($this->account) {
            $this->account->access_token = $token;
            $this->account->refresh_token = $data['refresh_token'] ?? $this->account->refresh_token;
            $this->account->token_expires_at = $data['expires_at'] ?? null;
            $this->account->sync_error = null;
            $this->account->save();
        }

        return $data;
    }

    public function getAgencyInfo(): array
    {
        return $this->get('/agency/thong-tin-dai-ly');
    }

    public function listProducts(): array
    {
        return $this->get('/agency/danh-sach-san-pham');
    }

    public function listOperatingSystems(int $productId): array
    {
        return $this->get('/agency/danh-sach-he-dieu-hanh-vps-vn', [
            'product_id' => $productId,
        ]);
    }

    public function listBillingCycles(): array
    {
        return $this->get('/agency/danh-sach-thoi-gian-thue');
    }

    public function createVpsOrder(
        int $productId,
        string $billingCycle,
        int $osId,
        int $quantity = 1,
        int $addonCpu = 0,
        int $addonRam = 0,
        int $addonDisk = 0
    ): array {
        $data = $this->post('/agency/tao-moi-don-hang-vps', [
            'product_id' => $productId,
            'billing_cycle' => $billingCycle,
            'os' => $osId,
            'quantity' => $quantity,
            'addon_cpu' => $addonCpu,
            'addon_ram' => $addonRam,
            'addon_disk' => $addonDisk,
        ]);

        if (isset($data['vps']) && is_array($data['vps'])) {
            return $data['vps'];
        }

        if (isset($data['servers']) && is_array($data['servers'])) {
            return $data['servers'];
        }

        if (isset($data['items']) && is_array($data['items'])) {
            return $data['items'];
        }

        return $data;
    }

    public function listVps(): array
    {
        return $this->get('/agency/danh-sach-vps');
    }

    public function getVps(int $id): array
    {
        return $this->extractInstance($this->get('/agency/chi-tiet-vps', ['id' => $id]));
    }

    public function actionVps(array $ids, string $action, array $extra = []): array
    {
        return $this->post('/agency/thao-tac-vps', array_merge([
            'ids' => array_values($ids),
            'action' => $action,
        ], $extra));
    }

    public function bootVps(int $id): array
    {
        return $this->actionVps([$id], 'on');
    }

    public function shutdownVps(int $id): array
    {
        return $this->actionVps([$id], 'off');
    }

    public function rebootVps(int $id): array
    {
        return $this->actionVps([$id], 'restart');
    }

    public function renewVps(int $id, string $billingCycle): array
    {
        return $this->actionVps([$id], 'renew', [
            'billing_cycle' => $billingCycle,
        ]);
    }

    public function deleteVps(int $id): array
    {
        return $this->actionVps([$id], 'delete');
    }

    public function rebuildVps(int $id, int $osId): array
    {
        return $this->actionVps([$id], 'confirm_rebuild', [
            'os' => $osId,
        ]);
    }

    public function upgradeVps(int $id, int $addonCpu, int $addonRam, int $addonDisk = 0): array
    {
        $data = $this->actionVps([$id], 'upgrade', [
            'addon_cpu' => (int) $addonCpu,
            'addon_ram' => (int) $addonRam,
            'addon_disk' => (int) $addonDisk,
        ]);

        if (isset($data['error']) && $data['error'] === true) {
            throw new RuntimeException($data['message'] ?? 'Nâng cấp thất bại');
        }

        return $data;
    }

    // --- PROXY METHODS ---

    public function createProxy(array $data): array
    {
        $response = $this->post('/agency/tao-moi-don-hang-proxy', $data);
        
        if (isset($response['proxy']) && is_array($response['proxy'])) {
            return isset($response['proxy']['id']) ? [$response['proxy']] : $response['proxy'];
        }
        if (isset($response['proxies']) && is_array($response['proxies'])) {
            return isset($response['proxies']['id']) ? [$response['proxies']] : $response['proxies'];
        }
        if (isset($response['items']) && is_array($response['items'])) {
            return isset($response['items']['id']) ? [$response['items']] : $response['items'];
        }
        
        return isset($response['id']) ? [$response] : $response;
    }

    public function listProxies(): array
    {
        return $this->get('/agency/danh-sach-proxy');
    }

    public function getProxy(int $id): array
    {
        return $this->extractInstance($this->get('/agency/chi-tiet-proxy', ['id' => $id]));
    }

    public function actionProxy(array $ids, string $action, array $extra = []): array
    {
        return $this->post('/agency/thao-tac-proxy', array_merge([
            'ids' => array_values($ids),
            'action' => $action,
        ], $extra));
    }


    private function get(string $path, array $query = []): array
    {
        return $this->request('get', $path, $query);
    }

    private function post(string $path, array $payload = []): array
    {
        return $this->request('post', $path, $payload);
    }

    private function postPublic(string $path, array $payload): array
    {
        $response = Http::acceptJson()
            ->asJson()
            ->timeout(60)
            ->post($this->url($path), $payload);

        return $this->parseResponse($response);
    }

    private function request(string $method, string $path, array $data = []): array
    {
        $token = $this->validToken();

        $pending = Http::withToken($token)
            ->acceptJson()
            ->asJson()
            ->timeout(60);

        \Illuminate\Support\Facades\Log::info('API Request Payload', [
            'method' => $method,
            'url' => $this->url($path),
            'data' => $data,
        ]);

        $response = $method === 'get'
            ? $pending->withBody(json_encode($data), 'application/json')->get($this->url($path))
            : $pending->post($this->url($path), $data);

        if (in_array($response->status(), [401, 403]) && $this->account) {
            $this->authenticate($this->account);
            $pending = Http::withToken($this->token)
                ->acceptJson()
                ->asJson()
                ->timeout(60);

            $response = $method === 'get'
                ? $pending->withBody(json_encode($data), 'application/json')->get($this->url($path))
                : $pending->post($this->url($path), $data);
        }

        return $this->parseResponse($response);
    }

    private function validToken(): string
    {
        if ($this->account && (!$this->account->access_token || ($this->account->token_expires_at && $this->account->token_expires_at->isPast()))) {
            $this->authenticate($this->account);
        }

        if (!$this->token) {
            $this->authenticate($this->account);
        }

        if (!$this->token) {
            throw new RuntimeException('CloudSunny access token is not available.');
        }

        return $this->token;
    }

    private function parseResponse($response): array
    {
        $json = $response->json();

        if ($response->successful() && is_array($json)) {
            if (($json['error'] ?? 0) === 0) {
                return $json['data'] ?? [];
            }

            throw new RuntimeException($json['message'] ?? $json['msg'] ?? 'SeaServer API returned an error.');
        }

        throw new RuntimeException($json['message'] ?? $json['msg'] ?? 'SeaServer API error (HTTP ' . $response->status() . ')');
    }

    private function url(string $path): string
    {
        return rtrim(config('cloudsunny.api_base'), '/') . '/' . ltrim($path, '/');
    }

    private function extractInstance(array $data): array
    {
        foreach (['vps', 'proxy', 'server', 'item', 'detail', 'data'] as $key) {
            if (isset($data[$key]) && is_array($data[$key])) {
                return $this->extractInstance($data[$key]);
            }
        }

        if (isset($data[0]) && is_array($data[0])) {
            return $data[0];
        }

        return $data;
    }
}
