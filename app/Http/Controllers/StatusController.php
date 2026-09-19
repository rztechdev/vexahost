<?php

namespace App\Http\Controllers;

use App\Models\SystemComponent;
use App\Services\MaintenanceService;

/**
 * PHASE 1 - Halaman status publik.
 *
 * Menggantikan angka statis dengan keadaan sebenarnya dari tabel
 * system_components dan jendela maintenance yang sedang berjalan.
 *
 * Bila belum ada komponen di basis data, halaman tetap tampil dengan
 * nilai bawaan agar tidak pernah kosong.
 */
class StatusController extends Controller
{
    public function __invoke(MaintenanceService $maintenance)
    {
        $components = SystemComponent::visible()->orderBy('sort_order')->get();

        $allOperational = $components->isEmpty()
            || $components->every(fn (SystemComponent $c) => $c->status === 'operational');

        $overallUptime = $components->whereNotNull('uptime_percent')->avg('uptime_percent') ?? 99.98;

        return view('pages.status', [
            'components' => $components,
            'allOperational' => $allOperational,
            'overallUptime' => number_format((float) $overallUptime, 2),
            'runningWindows' => $maintenance->runningWindows(),
            'upcomingWindows' => $maintenance->upcomingWindows(),
            'globalMaintenance' => $maintenance->isGlobalActive(),
        ]);
    }
}
