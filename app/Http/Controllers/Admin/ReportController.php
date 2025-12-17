<?php

namespace App\Http\Controllers\Admin;

use App\Admin\Reports\ReportFactory;
use App\Admin\Roles\RoleResolver;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
   public function generate(Request $request)
{
    $user = auth()->user();
    abort_if(!$user, 401);

    $role = RoleResolver::resolve($user->role);
    abort_if(!$role->canGenerateReports(), 403);

    $validated = $request->validate([
        'type' => 'required|string|in:daily_transactions,account_summary,audit_logs',
    ]);

    $report = ReportFactory::make($validated['type']);

    return response()->json([
        'data' => $report->generate()
    ]);
}
}
