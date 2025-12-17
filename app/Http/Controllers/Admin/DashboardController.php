<?php

namespace App\Http\Controllers\Admin;

use App\Admin\Dashboard\DashboardService;
use App\Admin\Roles\RoleResolver;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(DashboardService $service)
    {
        $role = RoleResolver::resolve(auth()->user()->role);
        abort_if(!$role->canViewDashboard(),403);

        return response()->json(
            $service->getOverview()
        );
    }
}
