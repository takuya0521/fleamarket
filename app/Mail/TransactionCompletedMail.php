<?php

namespace App\Mail;

use App\Models\Purchase;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransactionCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Purchase $purchase) {}

    public function build()
    {
        return $this->subject('【coachtechフリマ】取引が完了しました')
            ->view('emails.transaction_completed');
    }
}