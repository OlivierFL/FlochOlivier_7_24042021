<?php

namespace Test\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testGetUsersList(): void
    {
        $client = static::createClient();
        $client->request('GET', '/users');

        self::assertResponseIsSuccessful();
        self::isJson();
    }

    public function testGetUserDetails(): void
    {
        $client = static::createClient();
        $client->request('GET', '/users/1');

        self::assertResponseIsSuccessful();
        self::isJson();
    }

    public function testCreateNewUser(): void
    {
        $client = static::createClient();
        $client->request('POST', '/users');

        self::assertResponseIsSuccessful();
        self::isJson();
    }

    public function testDeleteUser(): void
    {
        $client = static::createClient();
        $client->request('DELETE', '/users/1');

        self::assertResponseIsSuccessful();
        self::isJson();
    }
}
