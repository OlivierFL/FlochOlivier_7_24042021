<?php

namespace App\Controller;

use App\Entity\Company;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
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
     * @param Company      $company
     * @param FileUploader $uploader
     *
     * @return Response
     */
    #[Route('register', name: 'register')]
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
