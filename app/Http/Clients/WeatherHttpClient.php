<?php

namespace App\Http\Clients;

use App\Exceptions\JogosHoje\FootballHttpClientExceptionHandler;
use App\Exceptions\WeatherHttpClientExceptionHandler;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
class WeatherHttpClient
{
    private Client $client;
    private array $defaultQueryStringParams;


    public function __construct()
    {
        try {
            $this->defaultQueryStringParams = [
                'appid' => config('services.api.secret')
            ];
            $this->client = new Client(['base_uri' => config('services.api.host'), 'query' => ['appid' => config('services.api.secret')]]);
        } catch (\Exception $exception) {
            logger($exception->getMessage());
            throw new \RuntimeException($exception->getMessage());
        }
    }

    public function get(string $endpoint, string|array $queryString = ''): mixed
    {
        if (!is_array($queryString)) {
            $queryString = [];
        }

        try {
            $queryString = array_merge($this->defaultQueryStringParams, $queryString);
            $apiResponse = $this->client->get($endpoint, ['query' => $queryString]);
            $this->handleErrors($apiResponse);
            return $this->handleResponse($apiResponse);
        } catch (GuzzleException $exception) {
            if (is_array($queryString)) {
                $queryString = http_build_query($queryString);
            }
            $errorMessage = "Error accessing {$endpoint} with query '{$queryString}': " . $exception->getMessage();
            logger($errorMessage);
            throw new \RuntimeException($errorMessage);
        } catch (\JsonException $e) {
            logger("Error decoding JSON response: " . $e->getMessage());
            throw new \RuntimeException("Error decoding JSON response: " . $e->getMessage());
        }
    }

    public function handleResponse(ResponseInterface $response): mixed
    {
        try {
            return json_decode($response->getBody(), false, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            logger("Error decoding JSON response: " . $exception->getMessage());
            throw new \RuntimeException("Error decoding JSON response: " . $exception->getMessage());
        }
    }

    public function getHeaders(string $token): array
    {
        return [
            'appid' => $token
        ];
    }

    public function handleErrors(ResponseInterface $response): void
    {
        $handler = new WeatherHttpClientExceptionHandler($response);
        $handler->handle();
    }
}
