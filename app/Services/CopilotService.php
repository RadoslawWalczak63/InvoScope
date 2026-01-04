<?php

namespace App\Services;

use App\Enums\Currency;
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

    public function getResponse(string $modelId, array $messages, array $responseFormat = ['type' => 'text']): object
    {
        $cacheKey = 'copilot_response_'.md5($modelId.serialize($messages).serialize($responseFormat));

        return Cache::remember($cacheKey, now()->addDay(), function () use ($modelId, $messages, $responseFormat) {
            $response = Http::withToken($this->apiKey)
                ->timeout(90)
                ->post($this->baseUrl.'/inference/chat/completions', [
                    'model' => $modelId,
                    'messages' => $messages,
                    'response_format' => $responseFormat,
                ]);

            $response->throw();

            return $response->object();
        });
    }

    public function getInvoiceJsonScheme(): array
    {
        return [
            'type' => 'json_schema',
            'json_schema' => [
                'name' => 'invoice_extraction',
                'strict' => true,
                'schema' => [
                    'type' => 'object',
                    'properties' => [
                        'number' => [
                            'type' => 'string',
                            'description' => 'The invoice number (e.g. INV-001)',
                        ],
                        'issue_date' => [
                            'type' => 'string',
                            'format' => 'date',
                            'description' => 'YYYY-MM-DD',
                        ],
                        'due_date' => [
                            'type' => ['string'],
                            'format' => 'date',
                            'description' => 'YYYY-MM-DD',
                        ],
                        'paid_date' => [
                            'type' => ['string', 'null'],
                            'format' => 'date',
                            'description' => 'YYYY-MM-DD',
                        ],
                        'type' => [
                            'type' => 'string',
                            'enum' => ['Income', 'Expense'],
                            'description' => 'Determine if this is an Income (Sales) or Expense (Purchase) invoice.',
                        ],
                        'currency' => [
                            'type' => 'string',
                            'description' => 'The currency code (e.g. USD, EUR, GBP) in ISO 4217 format.',
                            'enum' => array_map(fn ($c) => $c->value, Currency::cases()),
                        ],
                        'buyer_details' => [
                            'type' => 'object',
                            'properties' => [
                                'name' => ['type' => ['string', 'null']],
                                'tax_id' => ['type' => ['string', 'null']],
                                'address' => ['type' => ['string', 'null']],
                            ],
                            'required' => ['name', 'tax_id', 'address'],
                            'additionalProperties' => false,
                        ],
                        'seller_details' => [
                            'type' => 'object',
                            'properties' => [
                                'name' => ['type' => ['string', 'null']],
                                'tax_id' => ['type' => ['string', 'null']],
                                'address' => ['type' => ['string', 'null']],
                            ],
                            'required' => ['name', 'tax_id', 'address'],
                            'additionalProperties' => false,
                        ],

                        'items' => [
                            'type' => 'array',
                            'items' => [
                                'type' => 'object',
                                'properties' => [
                                    'name' => ['type' => 'string'],
                                    'description' => ['type' => ['string', 'null']],
                                    'sku' => ['type' => ['string', 'null']],
                                    'quantity' => ['type' => 'number'],
                                    'unit' => [
                                        'type' => ['string', 'null'],
                                        'enum' => [
                                            'Hour', 'Day', 'Week', 'Month', 'Year',
                                            'Piece', 'Set', 'Kit', 'Box', 'Pack', 'Dozen',
                                            'Kilogram', 'Gram', 'Tonne', 'Meter', 'Centimeter',
                                            'Square Meter', 'Liter', 'Service', 'Flat Rate', 'Project',
                                        ],
                                    ],
                                    'price' => ['type' => 'number'],
                                    'tax_amount' => ['type' => 'number'],
                                    'discount' => ['type' => 'number'],
                                ],

                                'required' => [
                                    'name',
                                    'description',
                                    'sku',
                                    'quantity',
                                    'unit',
                                    'price',
                                    'tax_amount',
                                    'discount',
                                ],
                                'additionalProperties' => false,
                            ],
                        ],
                    ],

                    'required' => [
                        'number',
                        'issue_date',
                        'due_date',
                        'paid_date',
                        'type',
                        'currency',
                        'buyer_details',
                        'seller_details',
                        'items',
                    ],
                    'additionalProperties' => false,
                ],
            ],
        ];
    }
}
