<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class UserController extends AbstractController
{
    #[Route(
        '/users',
        name: 'users_list',
        methods: ['GET']
    )]
    public function getUsers(): Response
    {
        return $this->json([
            'message' => 'Get Users list',
        ]);
    }

    #[Route(
        '/users/{id}',
        name: 'user_detail',
        requirements: ['id' => '\d+'],
        methods: ['GET']
    )]
    public function getOneUser(): Response
    {
        return $this->json([
            'message' => 'Get User details',
        ]);
    }

    #[Route(
        '/users',
        name: 'user_create',
        methods: ['POST']
    )]
    public function createUser(): Response
    {
        return $this->json([
            'message' => 'Create new User',
        ]);
    }

    #[Route(
        '/users/{id}',
        name: 'user_delete',
        requirements: ['id' => '\d+'],
        methods: ['DELETE']
    )]
    public function deleteUser(): Response
    {
        return $this->json([
            'message' => 'Delete user',
        ]);
    }
}
