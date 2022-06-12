<?php

namespace SoftCreatR\SteamGuard\Test;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Message;
use GuzzleHttp\Handler\MockHandler;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

abstract class TestCase extends PHPUnitTestCase
{
    /**
     * The being HTTP client instance.
     */
    protected Client $httpClient;

    /**
     * The being mock handler instance.
     */
    protected MockHandler $mockHandler;

    /**
     * Get the HTTP client instance.
     */
    final protected function getHttpClient(): Client
    {
        $this->httpClient = new Client([
            'handler' => HandlerStack::create($this->getMockHandler()),
        ]);

        return $this->httpClient;
    }

    /**
     * Get the mock handler instance.
     *
     * @return MockHandler
     */
    final protected function getMockHandler(): MockHandler
    {
        $this->mockHandler = new MockHandler();

        return $this->mockHandler;
    }

    /**
     * Mock the HTTP client response with given mock file.
     */
    final protected function mockRequest(string $filename): void
    {
        $mockResponse = $this->loadMockResponse($filename);
        $parsedResponse = Message::parseResponse($mockResponse);
        $this->mockHandler->append($parsedResponse);
    }

    /**
     * Load the mock file.
     */
    final protected function loadMockResponse(string $responseType): string
    {
        return file_get_contents(__DIR__ . '/Mock/' . strtolower($responseType) . 'Response.txt');
    }
}
