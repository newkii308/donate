<?php

namespace App\Http\Controllers\Streamer;

use App\Http\Controllers\Concerns\InteractsWithStreamer;
use App\Http\Controllers\Controller;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DonationHistoryController extends Controller
{
    use InteractsWithStreamer;

    public function index(Request $request): View
    {
        $streamer = $this->streamer();

        $donations = $this->query($request, $streamer->id)
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('streamer.donations', [
            'streamer' => $streamer,
            'donations' => $donations,
            'search' => $request->input('search'),
            'from' => $request->input('from'),
            'to' => $request->input('to'),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $streamer = $this->streamer();
        $query = $this->query($request, $streamer->id)->latest();

        $filename = 'donations-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ວັນທີ', 'ເວລາ', 'ຜູ້ໂດເນດ', 'ຈຳນວນ', 'ຄ່າບໍລິການ', 'ຍອດເຂົ້າກະເປົາ', 'ສະກຸນເງິນ', 'ເລກອ້າງອີງ', 'ຂໍ້ຄວາມ', 'ບໍ່ລະບຸຊື່', 'ສະຖານະ']);

            $query->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $d) {
                    fputcsv($out, [
                        $d->created_at->format('Y-m-d'),
                        $d->created_at->format('H:i:s'),
                        $d->donor_name,
                        $d->amount,
                        $d->platform_fee,
                        $d->net_amount,
                        $d->currency,
                        $d->transfer_reference,
                        $d->message,
                        $d->is_anonymous ? 'ແມ່ນ' : 'ບໍ່',
                        $d->status->label(),
                    ]);
                }
            });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /**
     * Build the filtered donation query shared by index + export.
     */
    private function query(Request $request, int $streamerId)
    {
        return Donation::forStreamer($streamerId)
            ->search($request->input('search'))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->date('to')));
    }
}
