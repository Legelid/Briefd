<?php

namespace App\Services;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClaudeService
{
    public function generateDigest(Collection $items): string
    {
        if ($items->isEmpty()) {
            return '<p>No recent content found for this digest.</p>';
        }

        $itemsText = $items->map(function ($item, $index) {
            $date = $item->published_at ? $item->published_at->format('M j, Y') : 'Recent';
            return ($index + 1) . ". [{$item->title}]({$item->url}) — {$date}";
        })->implode("\n");

        $prompt = "You are a newsletter editor creating a concise, engaging digest for a professional audience.\n\n"
            . "Summarize each of the following news items into 2-3 sentences. Include the source link as a hyperlink on the title. "
            . "Format the output as clean HTML suitable for an email newsletter. Use <h3> for each item title (as a link), <p> for the summary. "
            . "Keep each summary factual, informative, and neutral. Do not add introduction or closing text.\n\n"
            . "Items:\n{$itemsText}";

        try {
            $response = Http::withHeaders([
                'x-api-key' => config('services.anthropic.key', env('ANTHROPIC_API_KEY')),
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-sonnet-4-6',
                'max_tokens' => 4096,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ])->throw();

            return $response->json('content.0.text', '<p>Unable to generate digest content.</p>');
        } catch (\Throwable $e) {
            Log::error('Claude API error: ' . $e->getMessage());

            // Fallback: plain list
            $fallback = '<ul>';
            foreach ($items as $item) {
                $fallback .= '<li><a href="' . htmlspecialchars($item->url) . '">' . htmlspecialchars($item->title) . '</a></li>';
            }
            $fallback .= '</ul>';
            return $fallback;
        }
    }
}
