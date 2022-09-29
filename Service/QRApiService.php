<?php

namespace Mike\QRCode\Service;

use GuzzleHttp\Client;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ResponseFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Webapi\Rest\Request;

/**
 * Class QRApiService
 */
class QRApiService
{
    /**
     * API request URL
     */
    const API_REQUEST_URI = 'https://www.de-vis-software.ro/qrcodeme.aspx';

    /** Authorization token config */
    const AUTH_TOKEN_CONFIG = "catalog/mike_qr_code/auth_token";

    /**
     * @var ResponseFactory
     */
    private ResponseFactory $responseFactory;

    /**
     * @var ClientFactory
     */
    private ClientFactory $clientFactory;

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @param ClientFactory $clientFactory
     * @param ResponseFactory $responseFactory
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ClientFactory $clientFactory,
        ResponseFactory $responseFactory,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->clientFactory = $clientFactory;
        $this->responseFactory = $responseFactory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Fetch some data from API
     */
    public function execute(string $text): string
    {
        $authToken = $this->scopeConfig->getValue(self::AUTH_TOKEN_CONFIG);
        if (!$authToken) {
            return '';
        }
        $response = $this->doRequest([
            'body' => json_encode(['plainText' => $text]),
            'headers' => [
                'Authorization' => 'Basic ' . $authToken,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]
        ]);

        if ($response->getStatusCode() == 200) {
            $result = json_decode($response->getBody()->getContents());
            return $result->base64QRCode;
        } else {
            return '';
        }
    }

    /**
     * Do request with provided params
     *
     * @param array $params
     *
     * @return Response
     */
    private function doRequest(
        array $params = []
    ): Response {
        /** @var Client $client */
        $client = $this->clientFactory->create(['config' => [
            'base_uri' => self::API_REQUEST_URI
        ]]);

        try {
            $response = $client->request(
                Request::HTTP_METHOD_POST,
                '',
                $params
            );
        } catch (GuzzleException $exception) {
            /** @var Response $response */
            $response = $this->responseFactory->create([
                'status' => $exception->getCode(),
                'reason' => $exception->getMessage()
            ]);
        }

        return $response;
    }
}
