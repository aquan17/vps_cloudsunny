<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CloudSunnyAccount;
use App\Services\CloudSunnyAccountSyncService;
use App\Services\CloudSunnyApiService;
use Illuminate\Http\Request;

class CloudSunnyAccountController extends Controller
{
    public function index(CloudSunnyApiService $api, CloudSunnyAccountSyncService $sync)
    {
        $accounts = CloudSunnyAccount::withCount([
            'instances as active_count' => function ($q) {
                $q->whereNotIn('status', ['Lỗi', 'Đã xóa', 'Hết hạn']);
            },
        ])->orderBy('priority')->orderBy('id')->get();

        if ($accounts->count() <= 3) {
            foreach ($accounts as $account) {
                try {
                    $sync->sync($account, $api);
                } catch (\Throwable $e) {
                }
            }
            $accounts = CloudSunnyAccount::withCount([
                'instances as active_count' => function ($q) {
                    $q->whereNotIn('status', ['Lỗi', 'Đã xóa', 'Hết hạn']);
                },
            ])->orderBy('priority')->orderBy('id')->get();
        }

        $rows = $accounts->map(fn (CloudSunnyAccount $account) => [
            'model' => $account,
            'available_vnd' => (int) $account->credit_vnd,
            'used_pct' => $account->total_vnd > 0
                ? min(100, round((1 - ($account->credit_vnd / max(1, $account->total_vnd))) * 100))
                : 0,
        ]);

        return view('admin.accounts.index', [
            'rows' => $rows,
            'stats' => [
                'active' => $accounts->where('is_active', true)->where('is_full', false)->count(),
                'full' => $accounts->where('is_full', true)->count(),
                'total_reserved' => $accounts->sum('credit_vnd'),
            ],
        ]);
    }

    public function store(Request $request, CloudSunnyApiService $api, CloudSunnyAccountSyncService $sync)
    {
        $data = $request->validate([
            'label' => 'required|string|max:120',
            'api_username' => 'required|email|max:190',
            'api_app' => 'required|string|max:255',
            'api_secret' => 'required|string|min:20',
            'priority' => 'nullable|integer|min:0|max:999',
        ]);

        $account = CloudSunnyAccount::create([
            'label' => $data['label'],
            'api_username' => $data['api_username'],
            'api_app' => $data['api_app'],
            'api_secret' => $data['api_secret'],
            'priority' => (int) ($data['priority'] ?? 0),
            'is_active' => true,
        ]);

        try {
            $sync->sync($account, $api);
        } catch (\Throwable $e) {
            $account->sync_error = $e->getMessage();
            $account->save();

            return back()->with('error', 'Đã lưu account nhưng đồng bộ thất bại: ' . $e->getMessage());
        }

        return back()->with('success', 'Đã thêm account NovaCloud thành công.');
    }

    public function sync(CloudSunnyAccount $account, CloudSunnyApiService $api, CloudSunnyAccountSyncService $sync)
    {
        try {
            $sync->sync($account, $api);

            return back()->with('success', 'Đồng bộ NovaCloud account thành công.');
        } catch (\Throwable $e) {
            $account->sync_error = $e->getMessage();
            $account->save();

            return back()->with('error', 'Đồng bộ lỗi: ' . $e->getMessage());
        }
    }

    public function syncAll(CloudSunnyApiService $api, CloudSunnyAccountSyncService $sync)
    {
        $ok = 0;
        $fail = 0;

        foreach (CloudSunnyAccount::all() as $account) {
            try {
                $sync->sync($account, $api);
                $ok++;
            } catch (\Throwable $e) {
                $account->sync_error = $e->getMessage();
                $account->save();
                $fail++;
            }
        }

        return back()->with('success', "Đồng bộ xong: {$ok} OK, {$fail} lỗi.");
    }

    public function toggle(Request $request, CloudSunnyAccount $account)
    {
        $field = $request->input('field');
        if (!in_array($field, ['is_active', 'is_full'], true)) {
            return back()->with('error', 'Trường không hợp lệ.');
        }

        $account->$field = !$account->$field;
        $account->save();

        return back()->with('success', 'Đã cập nhật account.');
    }

    public function edit(CloudSunnyAccount $account)
    {
        return view('admin.accounts.edit', compact('account'));
    }

    public function update(Request $request, CloudSunnyAccount $account)
    {
        $data = $request->validate([
            'label' => 'required|string|max:120',
            'api_username' => 'required|email|max:190',
            'api_app' => 'required|string|max:255',
            'api_secret' => 'nullable|string|min:20',
            'priority' => 'nullable|integer|min:0|max:999',
            'is_active' => 'boolean',
            'is_full' => 'boolean',
        ]);

        $account->fill([
            'label' => $data['label'],
            'api_username' => $data['api_username'],
            'api_app' => $data['api_app'],
            'priority' => (int) ($data['priority'] ?? 0),
            'is_active' => $request->has('is_active'),
            'is_full' => $request->has('is_full'),
        ]);

        if (!empty($data['api_secret'])) {
            $account->api_secret = $data['api_secret'];
            $account->access_token = null;
            $account->refresh_token = null;
            $account->token_expires_at = null;
        }

        $account->save();

        return redirect()->route('admin.accounts.index')->with('success', 'Đã cập nhật account NovaCloud.');
    }

    public function destroy(CloudSunnyAccount $account)
    {
        if ($account->activeInstances()->exists()) {
            return back()->with('error', 'Không xóa account còn VPS đang chạy.');
        }

        $account->delete();

        return back()->with('success', 'Đã xóa account.');
    }
}
