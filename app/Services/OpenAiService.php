<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiService
{
    private string $baseUrl;

    private array $headers;

    public function __construct()
    {
        $this->baseUrl = config('services.openai.base_url');
        $this->headers = [
            'Authorization' => sprintf('Bearer %s', config('services.openai.key')),
            'Content-Type' => 'application/json',
            'OpenAI-Beta' => 'assistants=v2',
        ];
    }

    /**
     * @throws ConnectionException
     */
    public function getResponse(
        string $prompt,
        ?string $instructions = null,
        ?int $maxTokens = null,
        ?string $model = null,
        bool $returnObject = false
    ): object|string|null {
        $response = Http::withHeaders($this->headers)
            ->timeout(90)
            ->retry(10, 15 * 1000, function ($exception) {
                Log::warning('OpenAI request failed, retrying...', [
                    'error' => $exception->getMessage(),
                ]);

                return $exception instanceof ConnectionException;
            })
            ->post($this->baseUrl.'/responses', [
                'model' => $model ?? config('services.openai.model'),
                'instructions' => $instructions,
                'input' => $prompt,
                'max_output_tokens' => $maxTokens ?? config('services.openai.model_max_tokens'),
            ]);

        if ($returnObject) {
            return $response->object();
        }

        $outputText = '';
        foreach ($response->object()->output as $output) {
            if ($output->type === 'message') {
                $outputText .= $output->content[0]->text ?? '';
            }
        }

        return $outputText;
    }

    /**
     * @throws ConnectionException
     */
    public function getStreamedResponse(array $content, ?int $maxTokens = null, ?string $instructions = null): ?string
    {
        $client = Http::withHeaders($this->headers)
            ->timeout(90)
            ->retry(10, 15000, fn ($e) => $e instanceof ConnectionException)
            ->withOptions(['stream' => true]);

        $messages = [];

        if ($instructions) {
            $messages[] = [
                'role' => 'developer',
                'content' => $instructions,
            ];
        }

        $messages[] = [
            'role' => 'user',
            'content' => $content,
        ];

        $response = $client->post($this->baseUrl.'/chat/completions',
            [
                'model' => config('services.openai.model'),
                'messages' => $messages,
                'temperature' => config('services.openai.temperature'),
                'max_tokens' => $maxTokens ?? config('services.openai.model_max_tokens'),
                'stream' => true,
            ]
        );

        $body = $response->toPsrResponse()->getBody();

        $buffer = '';
        $result = '';

        while (! $body->eof()) {
            $buffer .= $body->read(1024);

            while (($pos = strpos($buffer, "\n")) !== false) {
                $line = trim(substr($buffer, 0, $pos));
                $buffer = substr($buffer, $pos + 1);

                if (! str_starts_with($line, 'data: ')) {
                    continue;
                }

                $data = substr($line, strlen('data: '));

                if ($data === '[DONE]') {
                    return $result ?: null;
                }

                $json = json_decode($data, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    continue;
                }

                $deltaContent = $json['choices'][0]['delta']['content'] ?? '';

                $result .= $deltaContent;
            }
        }

        return $result ?: null;
    }
}
