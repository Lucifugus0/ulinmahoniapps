<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RoomTransferNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $transferDetails;

    public function __construct($transferDetails)
    {
        $this->transferDetails = $transferDetails;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Room Transfer Notification')
            ->view('emails.room_transfer', [
                'transferDetails' => $this->transferDetails
            ]);
    }
}
