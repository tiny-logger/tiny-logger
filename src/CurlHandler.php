<?php

declare(strict_types=1);

namespace Serhii\TinyLogger;

use Curl\Curl;

class CurlHandler
{
    private string $url;

    /**
     * @var array<int, mixed>|null
     */
    private ?array $json;

    private Text $text;
    private Option $option;
    private Curl $curl;

    /**
     * @param array<int, mixed>|null $json
     */
    public function __construct(
        string $url,
        array|null $json,
        Text $text,
        Option $option,
        Curl $curl,
    ) {
        $this->url = $url;
        $this->json = $json;
        $this->text = $text;
        $this->option = $option;
        $this->curl = $curl;
    }

    public function makeRequest(): void
    {
        $json = $this->json ? $this->createCustomJson() : $this->createDefaultJson();

        $this->curl->setHeader('Content-Type', 'application/json');
        $this->curl->post($this->url, $json, true);
    }

    /**
     * @return string[]
     */
    private function createDefaultJson(): array
    {
        return [
            'timestamp' => $this->text->getDateBlock(true),
            'message' => $this->text->getPreparedText(),
            'type' => $this->option->getErrorType(),
        ];
    }

    /**
     * @return string[]
     */
    private function createCustomJson(): array
    {
        $search = [JsonFieldValue::TIMESTAMP, JsonFieldValue::MESSAGE, JsonFieldValue::ERROR_TYPE];
        $replace = [$this->text->getDateBlock(true), $this->text->getPreparedText(), $this->option->getErrorType()];

        return str_replace($search, $replace, $this->json ?? []);
    }
}
