<?php

namespace App\Services\AI;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class AIService
{
    protected string $baseUrl;

    protected int $timeout = 15;

    public function __construct()
    {
        $this->baseUrl = rtrim(
            (string) config(
                'services.ai.url',
                env(
                    'AI_SERVICE_URL',
                    'http://127.0.0.1:8001'
                )
            ),
            '/'
        );
    }

    public function askAssistant(
        string $question
    ): array {
        $response = Http::timeout(
            $this->timeout
        )->post(
            $this->baseUrl . '/assistant/query',
            [
                'question' => $question,
            ]
        );

        return $this->handleResponse(
            $response
        );
    }

    public function classifyComplaint(
        string $text
    ): array {
        $response = Http::timeout(
            $this->timeout
        )->post(
            $this->baseUrl . '/classification/predict',
            [
                'text' => $text,
            ]
        );

        return $this->handleResponse(
            $response
        );
    }

    public function analyzeComplaint(
        string $description
    ): array {
        $response = Http::timeout(
            $this->timeout
        )->post(
            $this->baseUrl . '/complaints/analyze',
            [
                'description' => $description,
            ]
        );

        return $this->handleResponse(
            $response
        );
    }

    public function findSimilarComplaints(
        array $complaint,
        array $candidates,
        int $limit = 5
    ): array {
        if (empty($candidates)) {
            return [
                'has_possible_related_complaints' => false,
                'count' => 0,
                'matches' => [],
            ];
        }

        $response = Http::timeout(
            $this->timeout
        )->post(
            $this->baseUrl . '/complaints/similar',
            [
                'complaint' => $complaint,
                'candidates' => $candidates,
                'limit' => $limit,
            ]
        );

        return $this->handleResponse(
            $response
        );
    }

    protected function handleResponse(
        Response $response
    ): array {
        if ($response->failed()) {
            throw new RuntimeException(
                'AI service request failed with status '
                    . $response->status()
                    . ': '
                    . $response->body()
            );
        }

        $data = $response->json();

        if (!is_array($data)) {
            throw new RuntimeException(
                'AI service returned an invalid response.'
            );
        }

        return $data;
    }
    public function recommendPlumbers(
        array $complaint,
        array $plumbers,
        int $limit = 5
    ): array {
        if (empty($plumbers)) {
            return [
                'has_recommendations' => false,
                'count' => 0,
                'recommendations' => [],
                'human_confirmation_required' => true,
            ];
        }

        $response = Http::timeout(
            $this->timeout
        )->post(
            $this->baseUrl
                . '/assignments/recommend-plumbers',
            [
                'complaint' => $complaint,
                'plumbers' => $plumbers,
                'limit' => $limit,
            ]
        );

        return $this->handleResponse(
            $response
        );
    }
}
