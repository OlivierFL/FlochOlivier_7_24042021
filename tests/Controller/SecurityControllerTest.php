<?php

namespace Test\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SecurityControllerTest extends WebTestCase
{
    public function testRegisterNewCompany(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/register',
            [
                'name' => 'CompanyName',
                'password' => 'CompanyPassword1234',
                'logoAltText' => 'Alt text',
                'logoUrl' => 'phone.png',
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
            '/api/register',
            [
                'name' => '',
                'password' => 'CompanyPassword1234',
                'logoAltText' => 'Alt text',
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
            '/api/register',
            [
                'name' => 'CompanyName',
                'password' => null,
                'logoAltText' => 'Alt text',
            ],
        );

        self::assertResponseStatusCodeSame(400, 'An error is thrown when a company registers with an empty password');
    }

    public function testRegisterNewCompanyWithTooWeakPassword(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/register',
            [
                'name' => 'CompanyName',
                'password' => 'toto',
                'logoAltText' => 'CompanyAltText',
            ],
        );

        self::assertResponseStatusCodeSame(400, 'An error is thrown when a company registers with a too weak password');
    }

    public function testRegisterNewCompanyWithLogoFile(): void
    {
        static::markTestSkipped('Handle file uploads');
        $client = static::createClient();

        $filePath = '/home/olivier/projets/php/bilemo/tests/files/test.png';
        $uploadedFile = new UploadedFile($filePath, 'test.png');

        $client->request(
            'POST',
            '/api/register',
            [
                'name' => 'CompanyName',
                'password' => 'CompanyPassword',
                'logoAltText' => 'Alt text',
            ],
            [
                'logo' => $uploadedFile,
            ]
        );

        self::assertResponseStatusCodeSame(201, 'New company can be created with a logo file');
        self::isJson();
    }
}
