<?php

namespace Vexura;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Vexura\Exception\ParameterException;
use ReflectionClass;

class MailCowAPI
{
    /**
     * @var Client
     */
    private $httpClient;

    /**
     * @var Credentials
     */
    private $credentials;

    /**
     * @var string
     */
    private $apiToken;

    /**
     * @var array Handler instances
     */
    private $handlers = [];

    /**
     * Default HTTP client configuration
     */
    private const HTTP_CLIENT_CONFIG = [
        'allow_redirects' => false,
        'follow_redirects' => false,
        'timeout' => 60,
        'http_errors' => false,
        'verify' => false
    ];

    /**
     * Default request headers
     */
    private const DEFAULT_HEADERS = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json'
    ];

    /**
     * Available handlers mapping
     */
    private const HANDLERS = [
        'domains' => 'Vexura\Domains\Domains',
        'antiSpam' => 'Vexura\AntiSpam\AntiSpam',
        'dkim' => 'Vexura\Dkim\Dkim',
        'mailBoxes' => 'Vexura\MailBoxes\MailBoxes',
        'aliases' => 'Vexura\Aliases\Aliases'
    ];

    /**
     * HTTP methods mapping
     */
    private const HTTP_METHODS = [
        'get' => 'GET',
        'post' => 'POST',
        'put' => 'PUT',
        'delete' => 'DELETE',
        'patch' => 'PATCH'
    ];

    /**
     * MailCowAPI constructor
     *
     * @param string $url Base URL for the API
     * @param string $token API Token for authentication
     * @param Client|null $httpClient Custom HTTP client (optional)
     */
    public function __construct(string $url, string $token, ?Client $httpClient = null)
    {
        $this->apiToken = $token;
        $this->setHttpClient($httpClient);
        $this->setCredentials($token, $url);
    }

    /**
     * Set the HTTP client
     *
     * @param Client|null $httpClient
     * @return void
     */
    public function setHttpClient(?Client $httpClient = null): void
    {
        $this->httpClient = $httpClient ?: new Client(self::HTTP_CLIENT_CONFIG);
    }

    /**
     * Set API credentials
     *
     * @param string|Credentials $credentials
     * @param string $url
     * @return void
     */
    public function setCredentials($credentials, string $url): void
    {
        if (!$credentials instanceof Credentials) {
            $credentials = new Credentials($url, $credentials);
        }
        $this->credentials = $credentials;
    }

    /**
     * Magic method to handle API requests
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws ParameterException
     */
    public function __call(string $name, array $arguments)
    {
        // Check if it's an HTTP method
        if (isset(self::HTTP_METHODS[strtolower($name)])) {
            $method = self::HTTP_METHODS[strtolower($name)];
            $path = $arguments[0] ?? '';
            $params = $arguments[1] ?? [];

            return $this->processRequest(
                $this->request($path, $params, $method)
            );
        }

        // Check if it's a handler request
        if (isset(self::HANDLERS[$name])) {
            return $this->getHandler($name);
        }

        throw new ParameterException("Method {$name} not found");
    }

    /**
     * Make an API request
     *
     * @param string $actionPath The resource path
     * @param array $params Request parameters
     * @param string $method HTTP method
     * @return ResponseInterface
     * @throws ParameterException
     */
    private function request(string $actionPath, array $params = [], string $method = 'GET'): ResponseInterface
    {
        if (!in_array($method, self::HTTP_METHODS)) {
            throw new ParameterException('Invalid HTTP method');
        }

        $url = $this->getCredentials()->getUrl() . $actionPath;
        $headers = array_merge(self::DEFAULT_HEADERS, ['x-api-key' => $this->apiToken]);
        $options = ['headers' => $headers, 'verify' => false];

        if (!empty($params)) {
            $options['json'] = $params;
        }

        return $this->httpClient->request($method, $url, $options);
    }

    /**
     * Process API response
     *
     * @param ResponseInterface $response
     * @return mixed
     */
    private function processRequest(ResponseInterface $response)
    {
        $content = $response->getBody()->__toString();
        $result = json_decode($content);

        return (json_last_error() === JSON_ERROR_NONE) ? $result : $content;
    }

    /**
     * Get or create a handler instance
     *
     * @param string $name
     * @return mixed
     */
    private function getHandler(string $name)
    {
        if (!isset($this->handlers[$name])) {
            $class = self::HANDLERS[$name];
            $this->handlers[$name] = new $class($this);
        }

        return $this->handlers[$name];
    }

    /**
     * Get the HTTP client instance
     *
     * @return Client
     */
    public function getHttpClient(): Client
    {
        return $this->httpClient;
    }

    /**
     * Get the API token
     *
     * @return string
     */
    public function getToken(): string
    {
        return $this->apiToken;
    }

    /**
     * Get the credentials instance
     *
     * @return Credentials
     */
    private function getCredentials(): Credentials
    {
        return $this->credentials;
    }
}
