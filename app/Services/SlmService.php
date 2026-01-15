<?php

namespace App\Services;

use App\Models\Balita;
use Illuminate\Support\Facades\Http;
use Throwable;

class SlmService
{
    public function generateRecommendation(Balita $balita, string $status, ?float $berat, ?float $tinggi): string
    {
        $usia = $balita->usia_bulan ?? null;
        $prompt = "Buat rekomendasi gizi singkat (maks 2 kalimat) dalam bahasa Indonesia untuk balita dengan status {$status}."
            . ($usia !== null ? " Usia: {$usia} bulan." : '')
            . ($berat !== null ? " Berat: {$berat} kg." : '')
            . ($tinggi !== null ? " Tinggi: {$tinggi} cm." : '')
            . ' Fokus pada tips praktis, aman, dan sesuai standar gizi anak.';

        $model = env('SLM_MODEL', 'llama3.2');
        $baseUrl = rtrim(env('SLM_BASE_URL', 'http://localhost:11434'), '/');

        try {
            $response = Http::timeout(15)->post($baseUrl . '/api/generate', [
                'model' => $model,
                'prompt' => $prompt,
                'stream' => false,
                'options' => [
                    'temperature' => 0.5,
                    'num_predict' => 120,
                ],
            ]);

            if ($response->successful() && ($text = $response->json('response'))) {
                return trim($text);
            }
        } catch (Throwable $e) {
            // pass through to throw below
        }

        throw new \RuntimeException('SLM tidak tersedia atau gagal merespons');
    }
}
