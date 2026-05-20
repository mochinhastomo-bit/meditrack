<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Courier;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        // ── Statistik utama ──────────────────────────────────────────────
        $stats = [
            'total_resep'        => Prescription::count(),
            'resep_hari_ini'     => Prescription::where('tanggal', $today)->count(),
            'total_pasien'       => Patient::count(),
            'total_kurir'        => Courier::where('is_active', true)->count(),
            'total_user'         => User::count(),
        ];

        // ── Resep per status ─────────────────────────────────────────────
        $statusCounts = Prescription::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $statusData = [];
        foreach (Prescription::statusList() as $key => $label) {
            $statusData[$key] = [
                'label' => $label,
                'count' => $statusCounts[$key] ?? 0,
                'color' => (new Prescription(['status' => $key]))->status_color,
            ];
        }

        // ── Resep terbaru (10) ───────────────────────────────────────────
        $recentPrescriptions = Prescription::with(['patient', 'courier'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'statusData', 'recentPrescriptions'));
    }
}
