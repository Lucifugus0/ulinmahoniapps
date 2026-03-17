<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Notifications\Notification;

class BookingConfirmationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;
    protected $transaction;
    protected $paymentUrl;

    /**
     * Create a new notification instance.
     *
     * @param array $booking
     * @param array $transaction
     * @param string|null $paymentUrl
     * @return void
     */
    public function __construct($booking, $transaction, $paymentUrl = null)
    {
        $this->booking = $booking;
        $this->transaction = $transaction;
        $this->paymentUrl = $paymentUrl;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Send the notification via Mailtrap API
     *
     * @param mixed $notifiable
     * @return void
     */
    public function toMail($notifiable)
    {
        try {
            // Render the Blade template to HTML
            $htmlContent = view('emails.booking-confirmation', [
                'user' => $notifiable,
                'booking' => $this->booking,
                'transaction' => $this->transaction,
                'paymentUrl' => $this->paymentUrl
            ])->render();

            // Plain text version (kept for fallback)
            $message = $this->buildPlainTextMessage($notifiable);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.mailtrap.api_key'),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post('https://send.api.mailtrap.io/api/send', [
                'from' => [
                    'email' => 'no-reply@ulinmahoni.com',
                    'name' => 'Ulinmahoni - Booking Confirmation'
                ],
                'to' => [
                    ['email' => $notifiable->email]
                ],
                'subject' => 'Booking Confirmation - Order #' . $this->transaction['order_id'],
                'html' => $htmlContent,
                'category' => 'Booking Confirmation'
            ]);

            if ($response->failed()) {
                Log::error('Failed to send booking confirmation email', [
                    'user_id' => $notifiable->id,
                    'email' => $notifiable->email,
                    'order_id' => $this->transaction['order_id'],
                    'error' => $response->json()
                ]);
                throw new \RuntimeException('Failed to send booking confirmation email');
            }

            Log::info('Booking confirmation email sent successfully', [
                'user_id' => $notifiable->id,
                'email' => $notifiable->email,
                'order_id' => $this->transaction['order_id']
            ]);

        } catch (\Exception $e) {
            Log::error('Error sending booking confirmation email', [
                'user_id' => $notifiable->id,
                'email' => $notifiable->email,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }

        // Return a dummy MailMessage to satisfy the interface
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->line('Your booking has been confirmed.')
            ->line('Order ID: ' . $this->transaction['order_id']);
    }

    /**
     * Build plain text message
     *
     * @param mixed $notifiable
     * @return string
     */
    private function buildPlainTextMessage($notifiable)
    {
        $message = "Dear {$notifiable->first_name} {$notifiable->last_name},\n\n";
        $message .= "Thank you for your booking with Ulinmahoni!\n\n";
        $message .= "Booking Details:\n";
        $message .= "Order ID: {$this->transaction['order_id']}\n";
        $message .= "Property: {$this->transaction['property_name']}\n";
        $message .= "Room: {$this->transaction['room_name']}\n";
        $message .= "Check-in: {$this->transaction['check_in']}\n";
        $message .= "Check-out: {$this->transaction['check_out']}\n";
        $message .= "Total Amount: Rp " . number_format($this->transaction['grandtotal_price'], 0, ',', '.') . "\n\n";

        if ($this->paymentUrl) {
            $message .= "Please complete your payment using the following link:\n";
            $message .= "{$this->paymentUrl}\n\n";
        }

        $message .= "If you have any questions, please don't hesitate to contact us.\n\n";
        $message .= "Best regards,\nThe Ulinmahoni Team";

        return $message;
    }

    /**
     * Send booking confirmation email via SMTP
     *
     * @param mixed $notifiable
     * @return bool
     */
    public function sendViaSMTP($notifiable)
    {
        try {
            // Configure SMTP mailer with custom env variables
            config([
                'mail.mailers.smtp.host' => env('SMTP_MAIL_HOST'),
                'mail.mailers.smtp.port' => env('SMTP_MAIL_PORT', 587),
                'mail.mailers.smtp.username' => env('SMTP_MAIL_USERNAME'),
                'mail.mailers.smtp.password' => env('SMTP_MAIL_PASSWORD'),
            ]);

            Mail::mailer('smtp')->send('emails.booking-confirmation', [
                'user' => $notifiable,
                'booking' => $this->booking,
                'transaction' => $this->transaction,
                'paymentUrl' => $this->paymentUrl
            ], function ($message) use ($notifiable) {
                $message->to($notifiable->email)
                    ->from(env('SMTP_MAIL_USERNAME', 'no-reply@ulinmahoni.com'), 'Ulinmahoni - Booking Confirmation')
                    ->subject('Booking Confirmation - Order #' . $this->transaction['order_id']);
            });

            Log::info('Booking confirmation email sent via SMTP', [
                'user_id' => $notifiable->id,
                'email' => $notifiable->email,
                'order_id' => $this->transaction['order_id']
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send booking confirmation email via SMTP', [
                'user_id' => $notifiable->id,
                'email' => $notifiable->email,
                'order_id' => $this->transaction['order_id'],
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }
}
