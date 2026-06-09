<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Business;
use App\Models\User;

class BusinessInquiry extends Mailable
{
    use Queueable, SerializesModels;

    public $business;
    public $inquirer;
    public $messageText;

    public function __construct(Business $business, User $inquirer, string $messageText)
    {
        $this->business = $business;
        $this->inquirer = $inquirer;
        $this->messageText = $messageText;
    }

    public function build()
    {
        return $this->subject('New inquiry for ' . $this->business->business_name)
                    ->markdown('emails.business-inquiry');
    }
}