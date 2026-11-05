<?php

namespace App\Mail\Transport;

use Illuminate\Support\Facades\Http;
use RuntimeException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class BrevoTransport extends AbstractTransport
{
    private const API_URL = 'https://api.brevo.com/v3/smtp/email';

    public function __construct(
        private readonly string $apiKey
    ) {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        if (empty($this->apiKey)) {
            throw new RuntimeException(
                'Brevo API key is not configured.'
            );
        }

        $email = $message->getOriginalMessage();

        if (!$email instanceof Email) {
            throw new RuntimeException(
                'Brevo transport requires a Symfony Email message.'
            );
        }

        $from = $email->getFrom()[0] ?? $email->getSender();

        if (!$from instanceof Address) {
            throw new RuntimeException(
                'The email sender address is missing.'
            );
        }

        $to = $this->formatAddresses($email->getTo());

        if (empty($to)) {
            throw new RuntimeException(
                'The email recipient is missing.'
            );
        }

        $payload = [
            'sender' => $this->formatAddress($from),
            'to' => $to,
            'subject' => $email->getSubject() ?? '',
        ];

        if (!empty($email->getCc())) {
            $payload['cc'] = $this->formatAddresses(
                $email->getCc()
            );
        }

        if (!empty($email->getBcc())) {
            $payload['bcc'] = $this->formatAddresses(
                $email->getBcc()
            );
        }

        if (!empty($email->getReplyTo())) {
            $payload['replyTo'] = $this->formatAddress(
                $email->getReplyTo()[0]
            );
        }

        $htmlContent = $email->getHtmlBody();
        $textContent = $email->getTextBody();

        if ($htmlContent !== null) {
            $payload['htmlContent'] = $htmlContent;
        }

        if ($textContent !== null) {
            $payload['textContent'] = $textContent;
        }

        if ($htmlContent === null && $textContent === null) {
            throw new RuntimeException(
                'The email has no text or HTML content.'
            );
        }

        $response = Http::acceptJson()
            ->withHeaders([
                'api-key' => $this->apiKey,
            ])
            ->timeout(20)
            ->post(self::API_URL, $payload);

        $response->throw();

        $messageId = $response->json('messageId');

        if (is_string($messageId) && $messageId !== '') {
            $message->setMessageId($messageId);
        }
    }

    private function formatAddresses(array $addresses): array
    {
        return array_map(
            fn (Address $address) => $this->formatAddress($address),
            $addresses
        );
    }

    private function formatAddress(Address $address): array
    {
        $formatted = [
            'email' => $address->getAddress(),
        ];

        if ($address->getName() !== '') {
            $formatted['name'] = $address->getName();
        }

        return $formatted;
    }

    public function __toString(): string
    {
        return 'brevo';
    }
}
