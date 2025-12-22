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
                    ->where(function($query) use ($code) {
                        $query->where('reservation_code', $code)
                              ->orWhere('id', $code);
                    })
                    ->firstOrFail();

        $invoiceCode = $reservation->reservation_code ?? $reservation->id;
        $pdf = Pdf::loadView('admin.invoices.show', compact('reservation'))
                ->setOptions(['defaultFont' => 'DejaVu Sans']);

        return $pdf->download("invoice-{$invoiceCode}.pdf");
    }
}
