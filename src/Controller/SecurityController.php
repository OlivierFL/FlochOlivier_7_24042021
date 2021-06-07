<?php

namespace App\Controller;

use App\Entity\Company;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as Doc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecurityController extends AbstractController
{
    /**
     * SecurityController constructor.
     *
     * @param UserPasswordEncoderInterface $encoder
     * @param ValidatorInterface           $validator
     * @param EntityManagerInterface       $em
     */
    public function __construct(
        private UserPasswordEncoderInterface $encoder,
        private ValidatorInterface $validator,
        private EntityManagerInterface $em
    ) {
    }

    /**
     * @ParamConverter(converter="createentity", "company", class="App\Entity\Company")
     *
     * @Doc\Response(
     *     response=201,
     *     description="Registers a new Company",
     *     @Model(type=Company::class)
     * )
     * @Doc\Response(
     *     response=400,
     *     description="Validation failed",
     * )
     * @Doc\Parameter(
     *     name="body",
     *     in="path",
     *     required=true,
     *     @Doc\JsonContent(
     *        type="object",
     *        @Doc\Property(
     *             type="string",
     *             property="name",
     *             description="Company name",
     *           ),
     *        @Doc\Property(
     *             type="string",
     *             property="password",
     *             description="Company password",
     *          ),
     *        @Doc\Property(
     *             type="string",
     *             property="logoUrl",
     *             description="Company logo",
     *           ),
     *        @Doc\Property(
     *             type="string",
     *             property="logoAltText",
     *             description="Company logo alternative text",
     *           )
     *     ),
     * )
     * @Doc\Tag(name="Company")
     * @Security(name="Bearer")
     *
     * @param Company      $company
     * @param FileUploader $uploader
     *
     * @return Response
     */
    #[Route(
        '/register',
        name: 'register',
        methods: ['POST']
    )]
    public function register(Company $company, FileUploader $uploader): Response
    {
        $errors = $this->validator->validate($company);
        if (\count($errors) > 0) {
            if ($company->getLogoUrl()) {
                $uploader->remove($company->getLogoUrl());
            }

            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $company->setPassword($this->encoder->encodePassword($company, $company->getPassword()));

        $this->em->persist($company);
        $this->em->flush();

        return $this->json($company, Response::HTTP_CREATED);
    }
}
