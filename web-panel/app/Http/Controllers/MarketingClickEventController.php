<?php

namespace App\Http\Controllers;

use App\CPU\Helpers;
use App\Models\MarketingClickEvent;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MarketingClickEventController extends Controller
{
    public function store(Request $request): Response
    {
        $validator = Validator::make($request->all(), [
            'page_key' => ['required', 'string', 'max:60', 'regex:/^[a-z0-9_-]+$/'],
            'placement' => ['nullable', 'string', 'max:60', 'regex:/^[a-z0-9_-]+$/'],
            'cta_key' => ['required', 'string', 'max:60', 'regex:/^[a-z0-9_-]+$/'],
            'cta_label' => ['nullable', 'string', 'max:160'],
            'source_url' => ['required', 'url', 'max:2048'],
            'landing_url' => ['nullable', 'url', 'max:2048'],
            'target_url' => ['required', 'url', 'max:2048'],
            'client_token' => ['nullable', 'string', 'max:64'],
            'utm_source' => ['nullable', 'string', 'max:160'],
            'utm_medium' => ['nullable', 'string', 'max:160'],
            'utm_campaign' => ['nullable', 'string', 'max:160'],
            'utm_content' => ['nullable', 'string', 'max:160'],
            'utm_term' => ['nullable', 'string', 'max:160'],
        ]);

        if ($validator->fails()) {
            return response()->noContent();
        }

        $validated = $validator->validated();
        $requestHost = strtolower((string) $request->getHost());
        $sourceHost = $this->normalizedHost($validated['source_url']);
        $targetHost = $this->normalizedHost($validated['target_url']);

        if ($sourceHost === null || $sourceHost !== $requestHost) {
            return response()->noContent();
        }

        if (!$this->isAllowedTargetHost($targetHost, $requestHost)) {
            return response()->noContent();
        }

        MarketingClickEvent::query()->create([
            'page_key' => $validated['page_key'],
            'placement' => $validated['placement'] ?? null,
            'cta_key' => $validated['cta_key'],
            'cta_label' => trim((string) ($validated['cta_label'] ?? '')) ?: null,
            'source_host' => $sourceHost,
            'source_path' => $this->normalizedPath($validated['source_url']),
            'landing_path' => isset($validated['landing_url']) ? $this->normalizedPath($validated['landing_url']) : null,
            'target_host' => $targetHost,
            'target_path' => $this->normalizedPath($validated['target_url']),
            'client_token' => trim((string) ($validated['client_token'] ?? '')) ?: (string) Str::uuid(),
            'utm_source' => $this->nullableString($validated['utm_source'] ?? null),
            'utm_medium' => $this->nullableString($validated['utm_medium'] ?? null),
            'utm_campaign' => $this->nullableString($validated['utm_campaign'] ?? null),
            'utm_content' => $this->nullableString($validated['utm_content'] ?? null),
            'utm_term' => $this->nullableString($validated['utm_term'] ?? null),
            'user_agent' => $this->nullableString($request->userAgent()),
            'ip_hash' => hash('sha256', (string) $request->ip() . '|' . config('app.key')),
        ]);

        return response()->noContent();
    }

    private function normalizedHost(string $url): ?string
    {
        $host = parse_url($url, PHP_URL_HOST);

        return $host ? strtolower((string) $host) : null;
    }

    private function normalizedPath(string $url): ?string
    {
        $path = (string) (parse_url($url, PHP_URL_PATH) ?: '/');
        $fragment = parse_url($url, PHP_URL_FRAGMENT);

        if ($fragment) {
            $path .= '#' . $fragment;
        }

        return $path;
    }

    private function isAllowedTargetHost(?string $targetHost, string $requestHost): bool
    {
        if ($targetHost === null) {
            return false;
        }

        $allowedHosts = array_values(array_unique(array_filter(array_merge(
            [$requestHost],
            Helpers::dossentry_marketing_hosts(),
            Helpers::dossentry_public_demo_hosts(),
            array_filter([Helpers::dossentry_internal_admin_host()])
        ))));

        return in_array($targetHost, $allowedHosts, true);
    }

    private function nullableString(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value !== '' ? $value : null;
    }
}
