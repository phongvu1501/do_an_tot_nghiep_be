<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class RefundSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;

    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    public function build()
    {
        $mail = $this->subject('Thông báo hoàn tiền đặt bàn')
                    ->view('emails.refund_success');

        // Đính kèm ảnh bill hoàn tiền nếu có
        if ($this->reservation->refund_bill_image) {
            $filePath = Storage::disk('public')->path($this->reservation->refund_bill_image);
            
            // Kiểm tra file tồn tại
            if (file_exists($filePath)) {
                // Lấy tên file gốc hoặc tạo tên mới
                $fileName = basename($this->reservation->refund_bill_image);
                // Đảm bảo extension đúng
                $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                $attachmentName = 'bill_hoan_tien_' . ($this->reservation->reservation_code ?? $this->reservation->id) . '.' . $extension;
                
                $mail->attach($filePath, [
                    'as' => $attachmentName,
                    'mime' => $this->getMimeType($extension),
                ]);
            }
        }

        return $mail;
    }

    /**
     * Lấy MIME type dựa trên extension
     */
    private function getMimeType($extension)
    {
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'pdf' => 'application/pdf',
        ];

        return $mimeTypes[strtolower($extension)] ?? 'application/octet-stream';
    }
}

