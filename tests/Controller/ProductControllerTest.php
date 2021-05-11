<?php

namespace Test\Controller;

use JsonException;
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
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            [json_encode(['name' => 'CompanyTest', 'password' => 'Test1234321'], JSON_THROW_ON_ERROR)]
        );

        $response = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $client->request('GET', '/api/products', [
            'Authorization' => 'Bearer '.$response['token'],
        ]);

        self::assertResponseStatusCodeSame(401, 'Products list is not accessible when user is not authenticated');
        self::isJson();
    }

    public function testGetProductDetails(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/products/1');

        self::assertResponseIsSuccessful();
        self::isJson();
    }
}
