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

    /**
     * @throws JsonException
     */
    public function testGetUsersListAuthenticated(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/api/users');

        self::assertResponseIsSuccessful('Users list is accessible when user is authenticated');
        self::isJson();
        $response = json_decode($client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('page', $response);
        self::assertArrayHasKey('limit', $response);
        self::assertArrayHasKey('total', $response);
        self::assertArrayHasKey('data', $response);
    }

    public function testGetUsersListNotAuthenticated(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/users');

        self::assertResponseStatusCodeSame(401, 'Users list is not accessible when company is not authenticated');
        self::isJson();
    }

    public function testGetUserDetailsNotAuthenticated(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/users/1');

        self::assertResponseStatusCodeSame(401, 'User detail is not accessible when company is not authenticated');
        self::isJson();
    }

    /**
     * @throws JsonException
     */
    public function testGetUserDetails(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/api/users/1');

        self::assertResponseIsSuccessful('User detail is accessible when user is authenticated');
        self::isJson();
    }

    /**
     * @throws JsonException
     */
    public function testGetUserDetailsUserNotExists(): void
    {
        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/api/users/100000');

        self::assertResponseStatusCodeSame(404, 'When User does not exists, response code equals to 404');
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
