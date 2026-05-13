<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;

class ChatbotController extends Controller
{
    public function message(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $apiKey = config('services.groq.key');
        if (!$apiKey) {
            return response()->json([
                'message' => 'Chatbot is not configured.',
            ], 500);
        }

        $systemPrompt = 'You are the APM Employee Hub assistant. Provide clear, concise help about using the system. '
            . 'Do not request or access private employee data. If asked about personal data, instruct the user to contact admin.';

        try {
            $response = Http::withToken($apiKey)
                ->timeout(20)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => config('services.groq.model', 'llama3-70b-8192'),
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $validated['message']],
                    ],
                    'max_tokens' => 300,
                    'temperature' => 0.2,
                ]);

            if (!$response->ok()) {
                $errorMessage = $response->json('error.message') ?? 'Chatbot request failed.';
                logger()->warning('OpenAI chatbot error', [
                    'status' => $response->status(),
                    'error' => $errorMessage,
                ]);
                return response()->json([
                    'message' => $errorMessage,
                ], 502);
            }

            $content = $response->json('choices.0.message.content') ?? 'Sorry, I could not respond.';

            return response()->json([
                'message' => $content,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Chatbot is unavailable right now.',
            ], 503);
        }
    }
}
