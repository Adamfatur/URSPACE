<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\Report;

class GeminiService
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('gemini.api_key');
        $this->model = config('gemini.model');
        $this->baseUrl = config('gemini.base_url');
    }

    /**
     * Make a request to Gemini API
     */
    protected function makeRequest(string $prompt, string $cacheKey = null): ?array
    {
        // Check rate limit
        if (RateLimiter::tooManyAttempts('gemini-api', config('gemini.rate_limit.requests_per_minute'))) {
            Log::warning('Gemini API rate limit exceeded');
            return null;
        }

        // Check cache
        if ($cacheKey) {
            $cached = Cache::get($cacheKey);
            if ($cached) {
                return $cached;
            }
        }

        RateLimiter::hit('gemini-api', 60);

        try {
            $response = Http::timeout(30)->post(
                "{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}",
                [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.3,
                        'maxOutputTokens' => 1024,
                    ]
                ]
            );

            if ($response->successful()) {
                $data = $response->json();
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

                if ($text) {
                    // Try to parse as JSON
                    $result = $this->parseJsonResponse($text);

                    // Cache the result
                    if ($cacheKey && $result) {
                        Cache::put($cacheKey, $result, config('gemini.rate_limit.cache_ttl'));
                    }

                    return $result;
                }
            }

            Log::error('Gemini API error', ['response' => $response->body()]);
            return null;

        } catch (\Exception $e) {
            Log::error('Gemini API exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Parse JSON from AI response
     */
    protected function parseJsonResponse(string $text): ?array
    {
        // Remove markdown code blocks if present
        $text = preg_replace('/```json\s*/', '', $text);
        $text = preg_replace('/```\s*/', '', $text);
        $text = trim($text);

        $decoded = json_decode($text, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        // Return as plain text if not JSON
        return ['text' => $text];
    }

    /**
     * Moderate content for violations
     */
    public function moderate(string $content): array
    {
        $minLength = config('gemini.processing.min_content_length');
        if (strlen($content) < $minLength) {
            return ['score' => 0, 'flags' => [], 'safe' => true];
        }

        $content = substr($content, 0, config('gemini.processing.max_content_length'));
        $cacheKey = 'ai_moderation_' . md5($content);

        $prompt = <<<PROMPT
Kamu adalah content moderator untuk forum universitas. Analisis konten berikut dan berikan penilaian.

Konten:
"{$content}"

Berikan response dalam format JSON:
{
    "score": <angka 1-10, 1=sangat aman, 10=sangat berbahaya>,
    "safe": <true/false>,
    "flags": [<array kategori pelanggaran jika ada>],
    "reason": "<penjelasan singkat dalam Bahasa Indonesia>",
    "suggested_action": "<none/warn/review/delete>"
}

Kategori pelanggaran yang mungkin: spam, hate_speech, harassment, violence, adult_content, misinformation, personal_attack, profanity
PROMPT;

        $result = $this->makeRequest($prompt, $cacheKey);

        return $result ?? ['score' => 0, 'flags' => [], 'safe' => true, 'reason' => 'Tidak dapat dianalisis'];
    }

    /**
     * Analyze a report and provide recommendations
     */
    public function analyzeReport(Report $report): array
    {
        $reported = $report->reported;
        $reportedContent = '';

        if ($reported) {
            $reportedContent = method_exists($reported, 'getContentForModeration')
                ? $reported->getContentForModeration()
                : ($reported->content ?? $reported->title ?? '');
        }

        $cacheKey = 'ai_report_' . $report->id;

        $prompt = <<<PROMPT
Kamu adalah asisten moderator untuk forum universitas. Analisis laporan berikut dan tentukan apakah laporan ini valid atau palsu/salah (konten sebenarnya aman).

Alasan Pelaporan: {$report->reason}
Konten yang Dilaporkan: "{$reportedContent}"
Tipe Konten: {$report->reported_type}

Berikan analisis dalam format JSON:
{
    "priority_score": <angka 1-10, 10=sangat urgent, 1=sangat aman/laporan palsu>,
    "severity": "<low/medium/high/critical>",
    "suggested_action": "<dismiss/ignore/warn/delete/ban>",
    "is_false_report": <true/false - true jika konten sebenarnya aman dan laporan tidak berdasar>,
    "analysis": "<analisis detail dalam Bahasa Indonesia. Jika laporan palsu, jelaskan kenapa konten ini aman.>",
    "confidence": <persentase keyakinan 0-100>
}
PROMPT;

        $result = $this->makeRequest($prompt, $cacheKey);

        return $result ?? [
            'priority_score' => 5,
            'severity' => 'medium',
            'suggested_action' => 'review',
            'analysis' => 'Memerlukan review manual',
            'confidence' => 0
        ];
    }

    /**
     * Generate weekly analytics summary
     */
    public function summarizeAnalytics(array $data): array
    {
        $cacheKey = 'ai_weekly_summary_' . date('Y-W');

        $dataJson = json_encode($data, JSON_PRETTY_PRINT);

        $prompt = <<<PROMPT
Kamu adalah analis data untuk forum universitas. Berikan ringkasan mingguan berdasarkan data berikut:

{$dataJson}

Berikan ringkasan dalam format JSON:
{
    "summary": "<ringkasan 2-3 paragraf dalam Bahasa Indonesia>",
    "highlights": [<array 3-5 poin penting>],
    "concerns": [<array masalah yang perlu diperhatikan, jika ada>],
    "recommendations": [<array 2-3 rekomendasi aksi>],
    "trend": "<positive/stable/negative>"
}
PROMPT;

        $result = $this->makeRequest($prompt, $cacheKey);

        return $result ?? [
            'summary' => 'Tidak dapat menghasilkan ringkasan',
            'highlights' => [],
            'concerns' => [],
            'recommendations' => [],
            'trend' => 'stable'
        ];
    }

    /**
     * Suggest tags for content
     */
    public function suggestTags(string $title, string $content, array $availableTags): array
    {
        $minLength = config('gemini.processing.min_content_length');
        if (strlen($content) < $minLength) {
            return [];
        }

        $content = substr($content, 0, 1000);
        $cacheKey = 'ai_tags_' . md5($title . $content);
        $tagsJson = json_encode($availableTags);

        $prompt = <<<PROMPT
Berdasarkan judul dan konten berikut, pilih 1-3 tag yang paling relevan dari daftar yang tersedia.

Judul: {$title}
Konten: {$content}

Tag yang tersedia: {$tagsJson}

Berikan response dalam format JSON:
{
    "suggested_tags": [<array nama tag yang disarankan>],
    "reason": "<alasan singkat>"
}
PROMPT;

        $result = $this->makeRequest($prompt, $cacheKey);

        return $result['suggested_tags'] ?? [];
    }

    /**
     * Analyze sentiment of multiple contents
     */
    public function analyzeSentiment(array $contents): array
    {
        if (empty($contents)) {
            return ['positive' => 0, 'neutral' => 0, 'negative' => 0, 'topics' => []];
        }

        $contentsText = implode("\n---\n", array_slice($contents, 0, 20));
        $cacheKey = 'ai_sentiment_' . md5($contentsText);

        $prompt = <<<PROMPT
Analisis sentimen dari kumpulan postingan forum universitas berikut:

{$contentsText}

Berikan analisis dalam format JSON:
{
    "positive_percent": <persentase sentimen positif>,
    "neutral_percent": <persentase sentimen netral>,
    "negative_percent": <persentase sentimen negatif>,
    "overall_mood": "<positive/neutral/negative>",
    "trending_topics": [<array 3-5 topik yang sering dibahas>],
    "concerns": [<array isu yang perlu diperhatikan>]
}
PROMPT;

        $result = $this->makeRequest($prompt, $cacheKey);

        return $result ?? [
            'positive_percent' => 33,
            'neutral_percent' => 34,
            'negative_percent' => 33,
            'overall_mood' => 'neutral',
            'trending_topics' => [],
            'concerns' => []
        ];
    }

    /**
     * Check if API is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }
}
