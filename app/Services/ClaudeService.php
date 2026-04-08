<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClaudeService
{
    public function generateDigest(Collection $groupedSources): string
    {
        if ($groupedSources->isEmpty()) {
            return '<p style="color:#666;">No recent content found for this digest.</p>';
        }

        $sourceBlocks = $groupedSources->map(function ($group) {
            $source = $group['source'];
            $items  = $group['items'];

            $typeLabel = strtoupper($source->type ?? 'rss');
            $header    = "== Source: {$source->name} ({$typeLabel}) ==";

            $itemsList = $items->map(function ($item) {
                $lines = "Title: {$item->title}";
                $lines .= "\nURL: {$item->url}";
                if (! empty($item->summary)) {
                    $lines .= "\nDescription: {$item->summary}";
                }
                return $lines;
            })->implode("\n\n---\n\n");

            return $header . "\n\n" . $itemsList;
        })->implode("\n\n====\n\n");

        $prompt = <<<PROMPT
You are a newsletter editor creating a professional digest email.

For each article below, write a 2-3 sentence summary in plain, engaging English.

Output ONLY valid HTML — no markdown, no code fences, no intro text, no closing text.

Sources are grouped with == Source: Name (TYPE) == headers.

For EACH source group, output exactly this wrapper, then a digest-item for each article:

<div class="source-group">
<h3 class="source-header">SOURCE NAME HERE</h3>
<span class="source-badge source-badge--TYPE">TYPE</span>

<div class="digest-item">
<p><strong><a href="REPLACE_WITH_URL">REPLACE_WITH_TITLE</a></strong></p>
<p>REPLACE_WITH_YOUR_2_TO_3_SENTENCE_SUMMARY</p>
<p><a href="REPLACE_WITH_URL" class="read-more">Read more →</a></p>
</div>
<hr>

</div>

Where TYPE is 'rss' or 'discord' (lowercase, for the CSS class).

Articles by source:

{$sourceBlocks}
PROMPT;

        try {
            $response = Http::withHeaders([
                'x-api-key'         => config('services.anthropic.key'),
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])->post('https://api.anthropic.com/v1/messages', [
                'model'      => 'claude-sonnet-4-6',
                'max_tokens' => 4096,
                'messages'   => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ])->throw();

            return $response->json('content.0.text', '<p>Unable to generate digest content.</p>');
        } catch (\Throwable $e) {
            Log::error('Claude API error: ' . $e->getMessage());

            return $this->fallback($groupedSources);
        }
    }

    private function fallback(Collection $groupedSources): string
    {
        return $groupedSources->map(function ($group) {
            $source = $group['source'];
            $items  = $group['items'];
            $type   = strtolower($source->type ?? 'rss');

            $itemsHtml = $items->map(function ($item) {
                $title   = htmlspecialchars($item->title);
                $url     = htmlspecialchars($item->url);
                $summary = $item->summary ? '<p>' . htmlspecialchars(mb_substr($item->summary, 0, 200)) . '…</p>' : '';

                return <<<HTML
<div class="digest-item">
<p><strong><a href="{$url}">{$title}</a></strong></p>
{$summary}
<p><a href="{$url}" class="read-more">Read more →</a></p>
</div>
<hr>
HTML;
            })->implode("\n");

            $sourceName = htmlspecialchars($source->name);

            return <<<HTML
<div class="source-group">
<h3 class="source-header">{$sourceName}</h3>
<span class="source-badge source-badge--{$type}">{$type}</span>
{$itemsHtml}
</div>
HTML;
        })->implode("\n");
    }
}
