<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route(
        '/users',
        name: 'users_list',
        methods: ['GET', 'POST']
    )]
    public function users(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
        ]);
    }

    #[Route(
        '/users/{id}',
        name: 'user_detail',
        requirements: ['id' => '\d+'],
        methods: ['GET', 'DELETE']
    )]
    public function user(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
        ]);
    }
}
