<?php 
namespace Thirtybittech\Invoicematic\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceGeneratedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $pdfPath;

    public function __construct($order, $pdfPath)
    {
        $this->order = $order;
        $this->pdfPath = $pdfPath;
    }

   public function build()
    {
        return $this->subject('Your Invoice')
                    ->markdown(config('invoicematic.email_template'))
                    ->attach($this->pdfPath, [
                        'as' => 'invoice.pdf',
                        'mime' => 'application/pdf',
                    ]);
    }
}
