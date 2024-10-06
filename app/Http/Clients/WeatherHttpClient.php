<?php

namespace App\Http\Clients;

use App\Exceptions\WeatherHttpClientExceptionHandler;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use GuzzleHttp\HandlerStack;

class WeatherHttpClient
{
    private Client $client;
    private array $defaultQueryStringParams;

    public function __construct()
    {
        $this->defaultQueryStringParams = [
            'appid' => config('services.api.secret')
        ];

        $handlerStack = HandlerStack::create();
        $handlerStack->push($this->errorHandlerMiddleware());

        $this->client = new Client([
            'base_uri' => config('services.api.host'),
            'query' => ['appid' => config('services.api.secret')],
            'handler' => $handlerStack,
        ]);
    }

    private function errorHandlerMiddleware(): \Closure
    {
        return function (callable $handler) {
            return static function (RequestInterface $request, array $options) use ($handler) {
                return $handler($request, $options)->then(
                    function (ResponseInterface $response) {
                        // Handle successful response
                        return $response;
                    },
                    function (RequestException $exception) {
                        // Handle error response
                        $response = $exception->getResponse();
                        if ($response) {
                            $handler = new WeatherHttpClientExceptionHandler($response);
                            $handler->handle();
                        }
                        throw $exception;
                    }
                );
            };
        };
    }

    public function get(string $endpoint, string|array $queryString = ''): mixed
    {
        if (!is_array($queryString)) {
            $queryString = [];
        }

        $queryString = array_merge($this->defaultQueryStringParams, $queryString);
        try {
            $apiResponse = $this->client->get($endpoint, ['query' => $queryString]);
        } catch (GuzzleException $e) {
            logger("HTTP Request Error: " . $e->getMessage());
            throw new \RuntimeException($e->getMessage());
        }
        return $this->handleResponse($apiResponse);
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
}
