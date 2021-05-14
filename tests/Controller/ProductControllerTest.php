<?php

namespace Test\Controller;

use JsonException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductControllerTest extends WebTestCase
{
    public function testGetProductsListNotAuthenticated(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/products');

        self::assertResponseStatusCodeSame(401, 'Products list is not accessible when user is not authenticated');
        self::isJson();
    }

    /**
     * @throws JsonException
     */
    public function testGetProductsListAuthenticated(): void
    {
        $client = $this->createAuthenticatedClient();

        $client->request('GET', '/api/products');

        self::assertResponseIsSuccessful('Products list is accessible when user is authenticated');
        self::isJson();
    }

    /**
     * @throws JsonException
     */
    public function testGetProductDetails(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/api/products/106');

        self::assertResponseIsSuccessful('Products detail is accessible when user is authenticated');
        self::isJson();
    }

    /**
     * @throws JsonException
     */
    public function testGetProductDetailsProductNotExists(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/api/products/1');

        self::assertResponseStatusCodeSame(404, 'When Product does not exists, response code equals to 404');
        self::isJson();
    }

    /**
     * Create a client with a default Authorization header.
     *
     * @param string $username
     * @param string $password
     *
     * @throws JsonException
     * @return KernelBrowser
     */
    protected function createAuthenticatedClient(string $username = 'Company', string $password = 'Company1234'): KernelBrowser
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => $username,
                'password' => $password,
            ], JSON_THROW_ON_ERROR)
        );

        $data = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        return $client;
    }
}
