<?php

namespace Test\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testRegisterNewCompany(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/register',
            [
                'username' => 'CompanyName',
                'password' => 'CompanyPassword',
            ],
        );

        self::assertResponseStatusCodeSame(201, 'New company can be created');
        self::isJson();
    }

    public function testRegisterNewCompanyWithEmptyCompanyName(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/register',
            [
                'username' => '',
                'password' => 'CompanyPassword',
            ],
        );

        self::assertResponseStatusCodeSame(400, 'An error is thrown when a company registers with an empty name');
        self::isJson();
    }

    public function testRegisterNewCompanyWithEmptyPassword(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/register',
            [
                'username' => 'CompanyName',
                'password' => null,
            ],
        );

        self::assertResponseStatusCodeSame(500, 'An error is thrown when a company registers with an empty password');
    }
}
