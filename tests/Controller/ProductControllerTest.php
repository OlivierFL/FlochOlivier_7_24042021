<?php

namespace Test\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductControllerTest extends WebTestCase
{
    public function testGetProductsList(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/products');

        self::assertResponseIsSuccessful();
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