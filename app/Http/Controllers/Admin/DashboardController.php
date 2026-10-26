<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Services\Admin\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller {
    public function __invoke(Request $request, DashboardService $service): JsonResponse|View {
        $summary = $service->summary();
        if ($request->wantsJson()) {
            return response()->json(['data' => $summary]);
        }
        return view('admin.dashboard', ['summary' => $summary]);
    }
}