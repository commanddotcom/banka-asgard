<?php

namespace App\Services;

use App\Exceptions\MonobankException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Minimal client for the Monobank Personal API (https://api.monobank.ua).
 */
final class MonobankClient
{
    private const BASE_URL = 'https://api.monobank.ua';

    // Monobank enforces roughly 1 request/60s per token across personal/* endpoints.
    private const MIN_REQUEST_INTERVAL = 60;

    public function __construct(private readonly string $token)
    {
    }

    /** @return array<int, array<string, mixed>> */
    public function statement(int $from, int $to, string $account = '0'): array
    {
        $path = sprintf('/personal/statement/%s/%d/%d', $account, $from, $to);

        $result = $this->request($path);

        return is_array($result) ? $result : [];
    }

    /** @return array{clientId?: string, name?: string, accounts?: array, jars?: array} */
    public function clientInfo(): array
    {
        $result = $this->request('/personal/client-info');

        return is_array($result) ? $result : [];
    }

    private function request(string $path): mixed
    {
        $this->throttle();

        $response = Http::withHeaders(['X-Token' => $this->token])
            ->timeout(20)
            ->connectTimeout(10)
            ->get(self::BASE_URL.$path);

        $status = $response->status();
        $decoded = $response->json();

        if ($status === 403) {
            throw new MonobankException('Monobank відхилив токен доступу. Перевірте правильність токена.');
        }

        if ($status === 429) {
            throw new MonobankException('Перевищено ліміт запитів до Monobank. Спробуйте пізніше.');
        }

        if ($status !== 200) {
            $message = is_array($decoded) && isset($decoded['errorDescription'])
                ? $decoded['errorDescription']
                : "Monobank повернув статус {$status}.";
            throw new MonobankException((string) $message);
        }

        if (! is_array($decoded)) {
            throw new MonobankException('Monobank повернув некоректну відповідь.');
        }

        return $decoded;
    }

    private function throttle(): void
    {
        $key = 'monobank:last_request:'.sha1($this->token);
        $last = Cache::get($key);

        if ($last !== null) {
            $elapsed = now()->timestamp - $last;
            $wait = self::MIN_REQUEST_INTERVAL - $elapsed;
            if ($wait > 0) {
                throw new MonobankException("Забагато запитів до Monobank. Спробуйте ще раз через {$wait} с.");
            }
        }

        Cache::put($key, now()->timestamp, self::MIN_REQUEST_INTERVAL);
    }
}
