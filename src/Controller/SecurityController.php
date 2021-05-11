<?php

namespace App\Controller;

use App\Entity\Company;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
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
     * @ParamConverter("company", class="App\Entity\Company")
     *
     * @throws Exception
     */
    #[Route('register', name: 'api_register')]
    public function register(Company $company): Response
    {
        $errors = $this->validator->validate($company);
        if (\count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $company->setPassword($this->encoder->encodePassword($company, $company->getPassword()));

        $this->em->persist($company);
        $this->em->flush();

        return $this->json($company, Response::HTTP_CREATED);
    }
}
