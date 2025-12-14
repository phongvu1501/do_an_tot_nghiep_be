<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function pdf($code)
    {
        $reservation = Reservation::with(['reservationItems.menu', 'tables', 'user', 'voucher'])
                    ->where('reservation_code', $code)
                    ->firstOrFail();

        $pdf = Pdf::loadView('admin.invoices.show', compact('reservation'))
                ->setOptions(['defaultFont' => 'DejaVu Sans']);

        return $pdf->download("invoice-{$code}.pdf");
    }
}
