<?php

namespace App\Controller;

use App\Entity\Company;
use App\Exception\InvalidParamException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecurityController extends AbstractController
{
    /**
     * @throws Exception
     */
    #[Route('/register', name: 'register')]
    public function register(Request $request, UserPasswordEncoderInterface $encoder, ValidatorInterface $validator): Response
    {
        $em = $this->getDoctrine()->getManager();

        $companyName = $request->request->get('username');
        $password = $request->request->get('password');

        if (null === $password || '' === $password) {
            throw InvalidParamException::nullPassword();
        }

        $logoUrl = $request->request->get('logo_url');
        $logoAltText = $request->request->get('logo_alt_text') ?? $companyName;

        $company = new Company();
        $company->setName($companyName);
        $company->setPassword($encoder->encodePassword($company, $password));
        $company->setLogoUrl($logoUrl);
        $company->setLogoAltText($logoAltText);

        $errors = $validator->validate($company);

        if (\count($errors) > 0) {
            $errorsString = (string) $errors;

            return new JsonResponse('Error : '.$errorsString, Response::HTTP_BAD_REQUEST, [], true);
        }

        $em->persist($company);
        $em->flush();

        return new JsonResponse('Company successfully registered', Response::HTTP_CREATED);
    }
}
