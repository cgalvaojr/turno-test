<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Client\HttpClientException;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;
use Throwable;

class WeatherHttpClientExceptionHandler extends Exception
{
    private ResponseInterface $response;

    public function __construct(ResponseInterface $response, $message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->response = $response;
    }

    public function handle(): void
    {
        try {
            $this->isValidStatusCode()
                ->isValidJson()
                ->isFound()
                ->isValidToken();
        } catch (HttpClientException $exception) {
            logger("HTTP Request Error: " . $exception->getMessage());
            throw new RuntimeException($exception->getMessage());
        } catch (\JsonException $exception) {
            logger("Error decoding JSON response: " . $exception->getMessage());
            throw new RuntimeException("Error decoding JSON response: " . $exception->getMessage());
        }
    }

    public function isFound(): self
    {
        $responseBody = json_decode($this->response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        if ($responseBody['cod'] === '404') {
            throw new HttpClientException($responseBody['message']);
        }
        return $this;
    }

    public function isValidToken(): void
    {
        $responseBody = json_decode($this->response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        if ($responseBody['cod'] === 401) {
            throw new HttpClientException($responseBody['message']);
        }
    }

    public function isValidStatusCode(): self
    {
        $statusCode = $this->response->getStatusCode();
        if ($statusCode >= 400 && $statusCode <= 500) {
            $responseBody = json_decode($this->response->getBody(), true, 512, JSON_THROW_ON_ERROR);
            throw new HttpClientException($responseBody['message']);
        }
        return $this;
    }

    public function isValidJson(): self
    {
        $data = json_decode($this->response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $isValid = json_last_error() === JSON_ERROR_NONE && !empty($data);

        if (!$isValid) {
            throw new RuntimeException('Invalid JSON string returned from API');
        }
        return $this;
    }
}
