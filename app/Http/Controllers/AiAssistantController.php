<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AiAssistantController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        $message = $request->input('message');
        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            return response()->json([
                'reply' => 'Maaf, API Key Gemini belum dikonfigurasi di server.'
            ]);
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=' . $apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => "Kamu adalah Asisten AI ramah untuk platform e-commerce bernama 'BelanjaIn'. Selalu berikan jawaban yang singkat, padat, dan berbahasa Indonesia yang santai. Jangan gunakan format yang terlalu rumit. Pengguna bertanya: " . $message
                            ]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Maaf, saya tidak bisa memahami respons dari API.';
                
                // Konversi markdown sederhana ke HTML agar rapi di UI
                $htmlReply = Str::markdown($reply);

                return response()->json([
                    'reply' => $htmlReply
                ]);
            } else {
                Log::error('Gemini API Error: ' . $response->body());
                return response()->json([
                    'reply' => 'Maaf, terjadi kesalahan saat menghubungi otak AI saya.'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Gemini Exception: ' . $e->getMessage());
            return response()->json([
                'reply' => 'Maaf, koneksi ke otak AI terputus.'
            ]);
        }
    }
}
