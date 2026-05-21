<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Courier;
use App\Models\Prescription;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $couriers = Courier::with('user')->where('is_active', true)->orderBy('name')->get();
        return view('admin.reports.index', compact('couriers'));
    }

    private function getQuery(Request $request)
    {
        $query = Prescription::with(['patient', 'courier', 'address'])
            ->orderBy('tanggal', 'desc')
            ->orderBy('nomor_resep', 'desc');

        if ($request->filled('courier_id')) {
            $query->where('courier_id', $request->courier_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('dari')) {
            $query->whereDate('tanggal', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('tanggal', '<=', $request->sampai);
        }

        return $query;
    }

    public function exportExcel(Request $request)
    {
        $prescriptions = $this->getQuery($request)->get();

        $filename = 'laporan-pengiriman-' . now()->format('Ymd-His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($prescriptions) {
            $file = fopen('php://output', 'w');
            // BOM for Excel UTF-8
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'No. Resep', 'Tanggal', 'Nama Pasien', 'Alamat',
                'Nama Kurir', 'No. Kendaraan', 'Status', 'Waktu Update',
            ]);

            foreach ($prescriptions as $p) {
                fputcsv($file, [
                    $p->nomor_resep,
                    $p->tanggal->format('d/m/Y'),
                    $p->patient->name ?? '-',
                    $p->address ? "{$p->address->label} — {$p->address->address}" : '-',
                    $p->courier->name ?? '-',
                    $p->courier->plate_number ?? '-',
                    $p->status_label,
                    $p->updated_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function preview(Request $request)
    {
        $prescriptions = $this->getQuery($request)->take(200)->get();

        return response()->json($prescriptions->map(fn($p) => [
            'nomor_resep'  => $p->nomor_resep,
            'tanggal'      => $p->tanggal->format('d/m/Y'),
            'patient'      => $p->patient->name ?? '-',
            'address'      => $p->address ? "{$p->address->label} — {$p->address->address}" : '-',
            'kurir'        => $p->courier->name ?? '-',
            'kendaraan'    => $p->courier->plate_number ?? '-',
            'status_label' => $p->status_label,
            'updated_at'   => $p->updated_at->format('d/m/Y H:i'),
        ]));
    }

    public function exportPdf(Request $request)
    {
        $prescriptions = $this->getQuery($request)->get();

        $courierName = '-';
        if ($request->filled('courier_id')) {
            $courier = Courier::find($request->courier_id);
            $courierName = $courier?->name ?? '-';
        }

        $filters = [
            'kurir'   => $request->courier_id ? $courierName : 'Semua Kurir',
            'status'  => $request->status     ? Prescription::statusList()[$request->status] ?? $request->status : 'Semua Status',
            'dari'    => $request->dari     ? \Carbon\Carbon::parse($request->dari)->format('d/m/Y')    : '-',
            'sampai'  => $request->sampai   ? \Carbon\Carbon::parse($request->sampai)->format('d/m/Y') : '-',
        ];

        $pdf = Pdf::loadView('admin.reports.pdf', compact('prescriptions', 'filters'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-pengiriman-' . now()->format('Ymd') . '.pdf');
    }
}
