<?php

namespace App\Http\Controllers\Consumer;

use App\Http\Controllers\Controller;
use App\Services\AI\AIService;
use Illuminate\Http\Request;
use Throwable;

class AIController extends Controller
{
    public function index()
    {
        return view('consumer.ai.index');
    }

    public function ask(
        Request $request,
        AIService $aiService
    ) {
        $validated = $request->validate([
            'question' => [
                'required',
                'string',
                'max:1000',
            ],
        ]);

        try {
            $result = $aiService->askAssistant(
                $validated['question']
            );

            return back()
                ->withInput()
                ->with('ai_result', $result);
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with(
                    'ai_error',
                    'The AI assistant is temporarily unavailable. Please try again or submit a complaint.'
                );
        }
    }
}
