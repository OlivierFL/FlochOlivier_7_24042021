<?php

namespace Test\Controller;

use JsonException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    /**
     * @throws JsonException
     */
    public function testCreateNewUser(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'POST',
            '/api/users',
            [
                'first_name' => 'User',
                'last_name' => 'Test',
                'email' => 'usertest@example.com',
            ]
        );

        self::assertResponseStatusCodeSame(201, 'New user can be created');
        self::isJson();
    }

    /**
     * @throws JsonException
     */
    public function testCreateNewUserWithInvalidEmail(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'POST',
            '/api/users',
            [
                'first_name' => 'User',
                'last_name' => 'Test',
                'email' => '',
            ]
        );

        self::assertResponseStatusCodeSame(400, 'An error is thrown when a company creates new user with invalid email');
        self::isJson();
    }

    /**
     * @throws JsonException
     */
    public function testCreateNewUserWithInvalidName(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->request(
            'POST',
            '/api/users',
            [
                'first_name' => 'User',
                'last_name' => '',
                'email' => 'user@example.com',
            ]
        );

        self::assertResponseStatusCodeSame(400, 'An error is thrown when a company creates new user with invalid name');
        self::isJson();
    }

    public function testGetUsersList(): void
    {
        self::markTestSkipped('implement auth');
        $client = static::createClient();
        $client->request('GET', '/api/users');

        self::assertResponseIsSuccessful();
        self::isJson();
    }

    public function testGetUserDetails(): void
    {
        self::markTestSkipped('implement auth');
        $client = static::createClient();
        $client->request('GET', '/api/users/1');

        self::assertResponseIsSuccessful();
        self::isJson();
    }

    public function testDeleteUser(): void
    {
        self::markTestSkipped('implement auth');
        $client = static::createClient();
        $client->request('DELETE', '/api/users/1');

        self::assertResponseIsSuccessful();
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
