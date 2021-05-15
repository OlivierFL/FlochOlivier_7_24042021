<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\NormalizationService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

    /**
     * @ParamConverter(converter="createentity", "user", class="App\Entity\User")
     *
     * @param User                   $user
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface     $validator
     * @param NormalizationService   $normalization
     *
     * @throws ExceptionInterface
     * @return Response
     */
    #[Route(
        '/users',
        name: 'user_create',
        methods: ['POST']
    )]
    public function createUser(User $user, EntityManagerInterface $entityManager, ValidatorInterface $validator, NormalizationService $normalization): Response
    {
        $errors = $validator->validate($user);
        if (\count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $user->setCompany($this->getUser());

        $entityManager->persist($user);
        $entityManager->flush();

        $user = $normalization->normalize($user);

        return $this->json($user, Response::HTTP_CREATED);
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
