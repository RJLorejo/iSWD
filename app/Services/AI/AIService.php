<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class AIService
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(
            config('services.ai.url'),
            '/'
        );
    }

    public function askAssistant(string $question): array
    {
        $response = Http::timeout(15)
            ->acceptJson()
            ->post(
                $this->baseUrl . '/assistant/query',
                [
                    'question' => $question,
                ]
            );

        if ($response->failed()) {
            throw new RuntimeException(
                'The AI service is currently unavailable.'
            );
        }

        return $response->json();
    }
}
