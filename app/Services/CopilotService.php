<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CopilotService
{
    private string $baseUrl;

    private string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.copilot.base_url');
        $this->apiKey = config('services.copilot.key');
    }

    public function getModels(): Collection
    {
        return Cache::remember('copilot_models', now()->addHour(), function () {
            $response = Http::withToken($this->apiKey)
                ->get($this->baseUrl.'/catalog/models');

            $response->throw();

            return collect($response->object());
        });
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function getResponse(string $modelId, array $messages, array $responseFormat = ['type' => 'text']): object
    {
        $response = Http::withToken($this->apiKey)
            ->timeout(90)
            ->post($this->baseUrl.'/inference/chat/completions', [
                'model' => $modelId,
                'messages' => $messages,
                'response_format' => $responseFormat,
            ]);

        $response->throw();

        return $response->object();
    }
}
