<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProxyInstance;
use App\Services\CloudSunnyApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProxyController extends Controller
{
    public function index(Request $request)
    {
        $proxies = ProxyInstance::query()
            ->with(['user', 'cloudSunnyAccount'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($query) use ($search) {
                    $query->where('ip', 'like', "%{$search}%")
                        ->orWhere('provider_proxy_id', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $statuses = ProxyInstance::query()->whereNotNull('status')->distinct()->orderBy('status')->pluck('status');

        return view('admin.proxies.index', compact('proxies', 'statuses'));
    }

    public function destroy(ProxyInstance $proxy, CloudSunnyApiService $api)
    {
        try {
            if ($proxy->cloudSunnyAccount && $proxy->provider_proxy_id) {
                $api->forAccount($proxy->cloudSunnyAccount)->deleteProxy((int) $proxy->provider_proxy_id);
            }

            DB::transaction(function () use ($proxy) {
                $proxy->update(['status' => 'Đã xoá']);
                $proxy->delete();
            });

            return redirect()->route('admin.proxies.index')->with('success', 'Đã xóa Proxy khỏi hệ thống.');
        } catch (\Throwable $exception) {
            Log::error('Admin proxy destroy failed', [
                'proxy_id' => $proxy->id,
                'provider_proxy_id' => $proxy->provider_proxy_id,
                'message' => $exception->getMessage(),
            ]);

            return back()->with('error', 'Xóa Proxy thất bại: ' . $exception->getMessage());
        }
    }
}
