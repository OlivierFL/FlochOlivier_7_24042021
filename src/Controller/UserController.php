<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\NormalizationService;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    /**
     * @throws ExceptionInterface
     */
    #[Route(
        '/users',
        name: 'users_list',
        methods: ['GET']
    )]
    public function getUsers(Request $request, UserRepository $repository, PaginationService $pagination): Response
    {
        $users = $repository->findBy(['company' => $this->getUser()]);
        $page = max(1, $request->query->getInt('page', 1));
        $limit = $request->query->getInt('limit', 10);

        $usersPaginated = $pagination->paginateData($users, $page, $limit);

        return $this->json($usersPaginated);
    }

    /**
     * @throws ExceptionInterface
     * @ParamConverter(converter="doctrine.orm", "user", class="App\Entity\User")
     */
    #[Route(
        '/users/{id}',
        name: 'user_detail',
        requirements: ['id' => '\d+'],
        methods: ['GET']
    )]
    public function getOneUser(User $user, NormalizationService $normalizationService): Response
    {
        if ($this->getUser() !== $user->getCompany()) {
            return $this->json('Access to this user is forbidden', Response::HTTP_FORBIDDEN);
        }

        $user = $normalizationService->normalize($user);

        return $this->json($user);
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
     *
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

    /**
     * @ParamConverter(converter="doctrine.orm", "user", class="App\Entity\User")
     */
    #[Route(
        '/users/{id}',
        name: 'user_delete',
        requirements: ['id' => '\d+'],
        methods: ['DELETE']
    )]
    public function deleteUser(User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() !== $user->getCompany()) {
            return $this->json('Access to this user is forbidden', Response::HTTP_FORBIDDEN);
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->json([], Response::HTTP_NO_CONTENT);
    }
}
