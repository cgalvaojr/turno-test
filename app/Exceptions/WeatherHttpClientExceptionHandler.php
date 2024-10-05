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
                ->isValidRateLimit()
                ->isValidRequest()
                ->isValidToken();
        } catch (HttpClientException $e) {
            logger("HTTP Request Error: " . $e->getMessage());
            throw new RuntimeException("HTTP Request Error: " . $e->getMessage());
        } catch (RuntimeException $e) {
            logger("Runtime Error: " . $e->getMessage());
            throw $e;
        } catch (Exception $e) {
            logger("Unexpected Error: " . $e->getMessage());
            throw new RuntimeException("Unexpected Error: " . $e->getMessage());
        } catch (Throwable $e) {
            logger("Unexpected Error: " . $e->getMessage());
            throw new RuntimeException("Unexpected Error: " . $e->getMessage());
        }
    }

    public function isValidToken(): void
    {
        try {
            $responseBody = json_decode($this->response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            logger("Error decoding JSON response: " . $exception->getMessage());
            throw new RuntimeException("Error decoding JSON response: " . $exception->getMessage());
        }

        if (isset($responseBody['errors']['token'])) {
            throw new HttpClientException($responseBody['errors']['token']);
        }
    }

    public function isValidRequest(): self
    {
        $responseBody = json_decode($this->response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        if (isset($responseBody['errors']['requests'])) {
            throw new HttpClientException($responseBody['errors']['requests']);
        }
        return $this;
    }

    public function isValidStatusCode(): self
    {
        $statusCode = $this->response->getStatusCode();
        if ($statusCode >= 400 && $statusCode <= 500) {
            throw new HttpClientException(
                "API returned HTTP error {$this->response->getStatusCode()} ({$this->response->getReasonPhrase()})"
            );
        }
        return $this;
    }

    public function isValidRateLimit(): self
    {
        $responseBody = json_decode($this->response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        if (isset($responseBody['errors']['rateLimit'])) {
            throw new HttpClientException($responseBody['errors']['rateLimit']);
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
