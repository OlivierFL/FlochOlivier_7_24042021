<?php

namespace Test\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testGetUsersList(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/users');

        self::assertResponseIsSuccessful();
        self::isJson();
    }

    public function testGetUserDetails(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/users/1');

        self::assertResponseIsSuccessful();
        self::isJson();
    }

    public function testCreateNewUser(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/users');

        self::assertResponseIsSuccessful();
        self::isJson();
    }

    public function testDeleteUser(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '/api/users/1');

        self::assertResponseIsSuccessful();
        self::isJson();
    }
}
