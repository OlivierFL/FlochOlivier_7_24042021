<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\UserVoter;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends ApiController
{
    /**
     * @ParamConverter(converter="doctrine.orm", "company", class="App\Entity\Company")
     *
     * @param Company           $company
     * @param Request           $request
     * @param UserRepository    $repository
     * @param PaginationService $pagination
     *
     * @return Response
     */
    #[Route(
        '/{id}/users',
        name: 'users_list',
        requirements: ['id' => '\d+'],
        methods: ['GET']
    )]
    public function getUsers(Company $company, Request $request, UserRepository $repository, PaginationService $pagination): Response
    {
        if ($company !== $this->getUser()) {
            throw new AccessDeniedException();
        }

        $dataPaginated = $pagination->getDataPaginated($request, $repository, ['company' => $this->getUser()]);

        return $this->jsonApiResponseList($dataPaginated, $request->attributes->get('_route'), ['id' => $company->getId()]);
    }

    /**
     * @ParamConverter(
     *     converter="doctrine.orm",
     *     "user",
     *     class="App\Entity\User",
     *     options={"mapping": {"user_id": "id", "company_id": "company"}})
     *
     * @param User $user
     *
     * @return Response
     */
    #[Route(
        '/{company_id}/users/{user_id}',
        name: 'user_detail',
        requirements: [
            'company_id' => '\d+',
            'user_id' => '\d+',
        ],
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
     * @ParamConverter(
     *     converter="doctrine.orm",
     *     "user",
     *     class="App\Entity\User",
     *     options={"mapping": {"user_id": "id", "company_id": "company"}})
     *
     * @param User                   $user
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     */
    #[Route(
        '/{company_id}/users/{user_id}',
        name: 'user_delete',
        requirements: [
            'company_id' => '\d+',
            'user_id' => '\d+',
        ],
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
