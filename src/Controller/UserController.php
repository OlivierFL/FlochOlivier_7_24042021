<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\UserVoter;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as Doc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends ApiController
{
    /**
     * @Doc\Parameter(
     *     name="page",
     *     in="query",
     *     description="Page number",
     *     @Doc\Schema(type="string")
     * )
     * @Doc\Parameter(
     *     name="limit",
     *     in="query",
     *     description="Number of users per page",
     *     @Doc\Schema(type="string")
     * )
     * @Doc\Parameter(
     *     name="sort",
     *     in="query",
     *     description="The field used to sort the users list (field name must be in camelCase)",
     *     @Doc\Schema(type="string")
     * )
     * @Doc\Parameter(
     *     name="direction",
     *     in="query",
     *     description="Direction (ASC or DESC) to sort the users list",
     *     @Doc\Schema(type="string")
     * )
     * @Security(name="Bearer")
     *
     * @param Request           $request
     * @param UserRepository    $repository
     * @param PaginationService $pagination
     *
     * @return Response
     */
    #[Route(
        '/users',
        name: 'users_list',
        methods: ['GET']
    )]
    public function getUsers(Request $request, UserRepository $repository, PaginationService $pagination): Response
    {
        $dataPaginated = $pagination->getDataPaginated($request, $repository, ['company' => $this->getUser()]);

        return $this->jsonApiResponseList($dataPaginated, $request->attributes->get('_route'));
    }

    /**
     * @ParamConverter(converter="doctrine.orm", "user", class="App\Entity\User")
     *
     * @Security(name="Bearer")
     *
     * @param User $user
     *
     * @return Response
     */
    #[Route(
        '/users/{id}',
        name: 'user_detail',
        requirements: ['id' => '\d+'],
        methods: ['GET']
    )]
    public function getOneUser(User $user): Response
    {
        $this->denyAccessUnlessGranted(UserVoter::USER_READ, $user);

        return $this->jsonApiResponse($user);
    }

    /**
     * @ParamConverter(converter="createentity", "user", class="App\Entity\User")
     *
     * @Security(name="Bearer")
     *
     * @param User                   $user
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface     $validator
     *
     * @return Response
     */
    #[Route(
        '/users',
        name: 'user_create',
        methods: ['POST']
    )]
    public function createUser(User $user, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        $errors = $validator->validate($user);
        if (\count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->jsonApiResponse($user, Response::HTTP_CREATED);
    }

    /**
     * @ParamConverter(converter="doctrine.orm", "user", class="App\Entity\User")
     *
     * @Security(name="Bearer")
     *
     * @param User                   $user
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     */
    #[Route(
        '/users/{id}',
        name: 'user_delete',
        requirements: ['id' => '\d+'],
        methods: ['DELETE']
    )]
    public function deleteUser(User $user, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(UserVoter::USER_DELETE, $user);

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->json([], Response::HTTP_NO_CONTENT);
    }
}
