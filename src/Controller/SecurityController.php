<?php

namespace App\Controller;

use App\Entity\Company;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecurityController extends AbstractController
{
    public function __construct(
        private UserPasswordEncoderInterface $encoder,
        private ValidatorInterface $validator,
        private EntityManagerInterface $em
    ) {
    }

    /**
     * @ParamConverter(converter="createentity", "company", class="App\Entity\Company")
     *
     * @throws Exception
     * @throws ExceptionInterface
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
