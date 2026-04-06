<?php

namespace App\Http\Middleware;

use Closure;
use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NoCacheHtmlResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!$this->shouldApplyNoCache($request, $response)) {
            return $response;
        }

        // Browser tetap revalidate tiap request halaman, tapi boleh simpan copy lokal.
        $response->headers->set('Cache-Control', 'private, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
        $response->headers->set('Vary', $this->mergeVaryHeader(
            (string) $response->headers->get('Vary', ''),
            'Cookie'
        ));

        // Conditional cache hanya untuk response HTML final.
        if (!$request->isMethodCacheable() || $response->getStatusCode() !== 200) {
            return $response;
        }

        $content = (string) $response->getContent();
        if ($content === '') {
            return $response;
        }

        $contentHash = sha1($content);
        $response->setEtag('"' . $contentHash . '"');
        $response->setLastModified($this->buildDeterministicLastModified($contentHash));

        // Otomatis kirim 304 jika browser sudah punya versi terbaru.
        $response->isNotModified($request);

        return $response;
    }

    private function shouldApplyNoCache(Request $request, Response $response): bool
    {
        $contentType = strtolower((string) $response->headers->get('Content-Type', ''));

        if (str_contains($contentType, 'text/html')) {
            return true;
        }

        // Untuk redirect halaman web (content-type bisa kosong), tetap no-cache.
        if (method_exists($request, 'acceptsHtml')) {
            return $request->acceptsHtml();
        }

        $accept = strtolower((string) $request->header('Accept', ''));
        return str_contains($accept, 'text/html') || str_contains($accept, '*/*');
    }

    private function mergeVaryHeader(string $existingVary, string $value): string
    {
        $parts = collect(explode(',', $existingVary))
            ->map(fn ($item) => trim($item))
            ->filter()
            ->map(fn ($item) => strtolower($item))
            ->values();

        if (!$parts->contains(strtolower($value))) {
            $parts->push(strtolower($value));
        }

        return $parts
            ->map(fn ($item) => ucwords($item, '-'))
            ->implode(', ');
    }

    private function buildDeterministicLastModified(string $contentHash): DateTimeImmutable
    {
        // Timestamp stabil berdasarkan hash konten (supaya Last-Modified konsisten
        // untuk konten yang sama, tanpa menyimpan state tambahan).
        $baseTimestamp = 1609459200; // 2021-01-01 00:00:00 UTC
        $rangeSeconds = 126230400; // 4 tahun
        $offset = hexdec(substr($contentHash, 0, 8)) % $rangeSeconds;
        $timestamp = $baseTimestamp + $offset;

        return (new DateTimeImmutable('@' . $timestamp))
            ->setTimezone(new DateTimeZone('GMT'));
    }
}
