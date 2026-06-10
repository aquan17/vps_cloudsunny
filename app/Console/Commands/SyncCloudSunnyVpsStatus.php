<?php

namespace App\Console\Commands;

use App\Models\VpsInstance;
use App\Services\CloudSunnyApiService;
use Illuminate\Console\Command;

class SyncCloudSunnyVpsStatus extends Command
{
    protected $signature = 'cloudsunny:sync-status';
    protected $description = 'Dong bo trang thai VPS tu CloudSunny';

    public function handle(CloudSunnyApiService $api): int
    {
        $instances = VpsInstance::whereNotNull('provider_vps_id')
            ->whereNotIn('status', ['Đã xóa', 'Hết hạn'])
            ->with('cloudSunnyAccount')
            ->get();

        foreach ($instances as $vps) {
            if (!$vps->cloudSunnyAccount) {
                continue;
            }

            try {
                $remote = $api->forAccount($vps->cloudSunnyAccount)->getVps((int) $vps->provider_vps_id);
                $vps->update([
                    'public_ip' => $this->remoteValue($remote, ['ip', 'ip_address', 'ipv4', 'main_ip', 'public_ip'], $vps->public_ip),
                    'login_username' => $this->remoteValue($remote, ['username', 'user', 'login_username', 'login_user'], $vps->login_username),
                    'root_password' => $this->remoteValue($remote, ['password', 'root_password', 'login_password'], $vps->root_password),
                    'status' => $this->mapStatus((string) $this->remoteValue($remote, ['status', 'state', 'power_status', 'vm_status', 'trang_thai'], $vps->status)),
                    'provider_payload' => $remote,
                ]);
                $this->info("Updated VPS ID {$vps->id}");
            } catch (\Throwable $e) {
                $this->error("VPS {$vps->id}: {$e->getMessage()}");
            }
        }

        return 0;
    }

    private function mapStatus(string $status): string
    {
        $status = $this->normalizeStatus($status);

        if ($this->containsAny($status, ['running', 'online', 'ready', 'started', 'power on', 'poweron', 'on', 'completed', 'complete', 'đang chạy', 'hoat dong', 'sẵn sàng'])) {
            return 'Sẵn sàng';
        }
        if ($this->containsAny($status, ['offline', 'stopped', 'shutdown', 'power off', 'poweroff', 'đã tắt'])) {
            return 'Đã tắt';
        }
        if ($this->containsAny($status, ['deleted', 'removed', 'đã xóa'])) {
            return 'Đã xóa';
        }
        if ($this->containsAny($status, ['error', 'failed', 'fail', 'loi'])) {
            return 'Lỗi';
        }

        if ($this->containsAny($status, ['active', 'success', 'bat', 'bật'])) {
            return 'Sẵn sàng';
        }
        if ($this->containsAny($status, ['off', 'tat', 'tắt'])) {
            return 'Đã tắt';
        }
        if ($this->containsAny($status, ['expired', 'het', 'hết'])) {
            return 'Hết hạn';
        }
        if ($this->containsAny($status, ['cancel'])) {
            return 'Đã xóa';
        }

        return 'Đang khởi tạo...';
    }

    private function normalizeStatus(string $status): string
    {
        $status = strtolower(strip_tags($status));
        $ascii = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $status);
        if (is_string($ascii) && $ascii !== '') {
            $status = $ascii;
        }

        return preg_replace('/[^a-z0-9]+/', ' ', $status) ?: '';
    }

    private function containsAny(string $value, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (strpos($value, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    private function remoteValue(array $remote, array $keys, $default = null)
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $remote) && $remote[$key] !== null && $remote[$key] !== '') {
                return $remote[$key];
            }
        }

        return $default;
    }
}
