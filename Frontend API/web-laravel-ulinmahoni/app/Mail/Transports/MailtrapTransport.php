<?php

namespace App\Mail\Transports;

use Illuminate\Mail\Transport\Transport;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mime\Message;
use Symfony\Component\Mime\Part\TextPart;

class MailtrapTransport extends Transport
{
    protected $apiKey;
    protected $endpoint = 'https://send.api.mailtrap.io/api/send';
    protected $fromEmail;
    protected $fromName;

    public function __construct($apiKey, $fromEmail, $fromName)
    {
        $this->apiKey = $apiKey;
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
    }

    public function send(SentMessage $message, \Closure $failure = null): void
    {
        $email = $message->getOriginalMessage();
        
        $payload = [
            'from' => [
                'email' => $this->fromEmail,
                'name' => $this->fromName,
            ],
            'to' => array_map(function ($address) {
                return ['email' => $address->getAddress()];
            }, $email->getTo()),
            'subject' => $email->getSubject(),
            'text' => $this->getText($email),
            'category' => 'Account Registration',
        ];

        $response = Http::withToken($this->apiKey)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->post($this->endpoint, $payload);

        if ($response->failed()) {
            throw new \Exception('Mailtrap API error: ' . $response->body());
        }
    }

    protected function getText(Message $email): string
    {
        foreach ($email->getParts() as $part) {
            if ($part instanceof TextPart && $part->getMediaType() === 'text' && $part->getMediaSubtype() === 'plain') {
                return $part->getBody();
            }
        }
        return '';
    }
}
