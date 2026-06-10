<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VpsInstance;
use Illuminate\Http\Request;

class InstanceController extends Controller
{
    public function index(Request $request)
    {
        $query = VpsInstance::with(['user', 'cloudSunnyAccount'])->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('label', 'like', "%{$search}%")
                  ->orWhere('public_ip', 'like', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $instances = $query->paginate(20)->appends($request->all());

        return view('admin.instances.index', compact('instances'));
    }
}
