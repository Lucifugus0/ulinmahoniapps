<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\Notification;

class PasswordResetConfirmationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;

    /**
     * Create a new notification instance.
     *
     * @param mixed $user
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
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
            $htmlContent = view('emails.password-reset-confirmation', [
                'user' => $notifiable,
                'resetTime' => now()->format('F j, Y, g:i a')
            ])->render();

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.mailtrap.api_key'),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post('https://send.api.mailtrap.io/api/send', [
                'from' => [
                    'email' => 'noreply@ulinmahoni.com',
                    'name' => 'Ulinmahoni - Password Reset Confirmation'
                ],
                'to' => [
                    ['email' => $notifiable->email]
                ],
                'subject' => 'Password Reset Successful - Ulinmahoni',
                'html' => $htmlContent,
                'category' => 'Password Reset Confirmation'
            ]);

            if ($response->failed()) {
                Log::error('Failed to send password reset confirmation email', [
                    'user_id' => $notifiable->id,
                    'email' => $notifiable->email,
                    'error' => $response->json()
                ]);
                throw new \RuntimeException('Failed to send password reset confirmation email');
            }

            Log::info('Password reset confirmation email sent successfully', [
                'user_id' => $notifiable->id,
                'email' => $notifiable->email
            ]);

        } catch (\Exception $e) {
            Log::error('Error sending password reset confirmation email', [
                'user_id' => $notifiable->id,
                'email' => $notifiable->email,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }

        // Return a dummy MailMessage to satisfy the interface
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->line('Your password has been reset successfully.')
            ->line('If you did not make this change, please contact us immediately.');
    }
}
