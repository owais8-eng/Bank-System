<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Admin\Dashboard\DashboardService;
use App\Admin\Roles\RoleResolver;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index(DashboardService $service)
    {
        $role = RoleResolver::resolve(auth()->user()->role);
        abort_if(! $role->canViewDashboard(), 403);

        return response()->json(
            $service->getOverview()
        );
    }
}
