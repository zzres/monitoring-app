<?php

namespace App\Services;

use App\Models\Probe;
use Illuminate\Support\Facades\Http;

class ProbeCheckService
{
    public function check(Probe $probe): array 
    {
        return match ($probe->type) {
            'http' => $this->checkHttp($probe->target),
            'tcp' => this->checkTcp($probe->target),
        };
    }

    private function checkHttp(string $url): array 
    {
        $start = microtime(true);

        try {
            $response = Http::timeout(10)->withoutRedirecting()->get($url);
            $responseTimeMs = (int) ((microtime(true) - $start) * 1000);

            return [
                'is_up' => $response->status() < 400,
                'response_time_ms' => $responseTimeMs,
                'status_code' => $response->status(),
                'error_message' => $response->status() < 400 ? null : "Code HTTP {$response->status()}",
            ];
        } catch (\Exception $e) {
            return [
                'is_up' => false,
                'response_time_ms' => null,
                'status_code' => null,
                'error_message' => $e->getMessage(),
            ];
        }
    }

    private function checkTcp(string $target): array 
    {
        [$host, $port] = explode(':', $target);
        $start = microtime(true);

        $connection = @fsockopen($host, (int) $port, $errno, $errstr, 5);

        if ($connection) {
            $responseTimeMs = (int) ((microtime(true) - $start) * 1000);
            fclose($connection);

            return [
                'is_up' => true,
                'response_time_ms' => $responseTimeMs,
                'status_code' => null,
                'error_message' => null,
            ];
        }

        return [
            'is_up' => false,
            'response_time_ms' => null,
            'status_code' => null,
            'error_message' => $errstr ?: 'Connexion impossible',
        ];
    }
}