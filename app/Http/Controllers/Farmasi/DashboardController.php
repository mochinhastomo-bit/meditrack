<?php

namespace App\Http\Controllers\Farmasi;

use App\Http\Controllers\Controller;
use App\Models\Prescription;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        // ── Statistik ────────────────────────────────────────────────────
        $stats = [
            'resep_hari_ini'     => Prescription::where('tanggal', $today)->count(),
            'penyiapan'          => Prescription::where('status', 'penyiapan')->count(),
            'siap_kirim'         => Prescription::where('status', 'siap_kirim')->count(),
            'dibawa'             => Prescription::where('status', 'dibawa')->count(),
            'dalam_pengiriman'   => Prescription::where('status', 'dalam_pengiriman')->count(),
            'terkirim_hari_ini'  => Prescription::where('status', 'terkirim')
                                        ->where('updated_at', '>=', now()->startOfDay())
                                        ->count(),
        ];

        // ── Kolom Kanban ─────────────────────────────────────────────────
        $kolPenyiapan = Prescription::with('patient')
            ->where('status', 'penyiapan')
            ->where('is_active', true)
            ->orderBy('tanggal')->orderBy('nomor_resep')
            ->get();

        $kolSiapKirim = Prescription::with('patient')
            ->where('status', 'siap_kirim')
            ->where('is_active', true)
            ->orderBy('tanggal')->orderBy('nomor_resep')
            ->get();

        $kolDibawa = Prescription::with(['patient', 'courier'])
            ->where('status', 'dibawa')
            ->where('is_active', true)
            ->latest('updated_at')
            ->get();

        $kolPengiriman = Prescription::with(['patient', 'courier'])
            ->where('status', 'dalam_pengiriman')
            ->where('is_active', true)
            ->latest('updated_at')
            ->get();

        return view('farmasi.dashboard', compact(
            'stats', 'kolPenyiapan', 'kolSiapKirim', 'kolDibawa', 'kolPengiriman'
        ));
    }

    /**
     * JSON endpoint untuk auto-refresh kolom Dalam Pengiriman.
     * @deprecated Gunakan kanbanJson()
     */
    public function pengirimanJson()
    {
        return $this->kanbanJson();
    }

    /**
     * Satu endpoint untuk semua kolom Kanban.
     * Mengembalikan data ketiga kolom + counter stat sekaligus.
     */
    public function kanbanJson()
    {
        $today = now()->toDateString();

        $map = function ($p, bool $withCourier = false) {
            $data = [
                'id'           => $p->id,
                'nomor_resep'  => $p->nomor_resep,
                'patient_name' => $p->patient->name ?? '-',
                'tanggal'      => $p->tanggal->format('d/m/Y'),
                'keterangan'   => $p->keterangan ?? '',
            ];
            if ($withCourier) {
                $data['courier_name']  = $p->courier?->name;
                $data['plate_number']  = $p->courier?->plate_number;
                $data['courier_phone'] = $p->courier?->phone;
                $data['has_gps']       = (bool) $p->courier?->last_latitude;
                $data['last_seen']     = $p->courier?->last_seen_at?->diffForHumans();
                $data['courier_lat']   = $p->courier?->last_latitude;
                $data['courier_lng']   = $p->courier?->last_longitude;
                $data['dest_lat']      = $p->address ? (float)$p->address->latitude  : null;
                $data['dest_lng']      = $p->address ? (float)$p->address->longitude : null;
                $data['dest_label']    = $p->address?->label;
                $data['dest_address']  = $p->address?->address;
            }
            return $data;
        };

        $penyiapan  = Prescription::with('patient')
            ->where('status', 'penyiapan')->where('is_active', true)
            ->orderBy('tanggal')->orderBy('nomor_resep')->get();

        $siapKirim  = Prescription::with('patient')
            ->where('status', 'siap_kirim')->where('is_active', true)
            ->orderBy('tanggal')->orderBy('nomor_resep')->get();

        $dibawa = Prescription::with(['patient', 'courier'])
            ->where('status', 'dibawa')->where('is_active', true)
            ->latest('updated_at')->get();

        $pengiriman = Prescription::with(['patient', 'courier', 'address'])
            ->where('status', 'dalam_pengiriman')->where('is_active', true)
            ->latest('updated_at')->get();

        return response()->json([
            'penyiapan'  => $penyiapan->map(fn($p) => $map($p)),
            'siap_kirim' => $siapKirim->map(fn($p) => $map($p)),
            'dibawa'     => $dibawa->map(fn($p) => $map($p, true)),
            'pengiriman' => $pengiriman->map(fn($p) => $map($p, true)),
            'stats' => [
                'penyiapan'        => $penyiapan->count(),
                'siap_kirim'       => $siapKirim->count(),
                'dibawa'           => $dibawa->count(),
                'dalam_pengiriman' => $pengiriman->count(),
                'resep_hari_ini'   => Prescription::where('tanggal', $today)->count(),
                'terkirim_hari_ini'=> Prescription::where('status','terkirim')
                                        ->where('updated_at', '>=', now()->startOfDay())->count(),
            ],
        ]);
    }
}
