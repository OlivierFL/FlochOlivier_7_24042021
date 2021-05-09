<?php

namespace App\Controller;

use App\Entity\Company;
use App\Service\FileUploader;
use Exception;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecurityController extends AbstractController
{
    public function __construct(
        private UserPasswordEncoderInterface $encoder,
        private ValidatorInterface $validator,
        private SerializerInterface $serializer,
        private FileUploader $uploader
    ) {
    }

    /**
     * @throws Exception
     */
    #[Route('/register', name: 'register')]
    public function register(Request $request): Response
    {
        $company = $this->serializer->deserialize(
            $this->checkAddContent($request),
            Company::class,
            'json'
        );

        $errors = $this->validator->validate($company);
        if (\count($errors) > 0) {
            return $this->json(
                $errors,
                Response::HTTP_BAD_REQUEST
            );
        }

        $company->setPassword($this->encoder->encodePassword($company, $company->getPassword()));

        if ($request->files->get('logo')) {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $request->files->get('logo');
            $filename = $this->uploader->upload($uploadedFile);
            $company->setLogoUrl($filename);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($company);
        $em->flush();

        return $this->json($company, Response::HTTP_CREATED);
    }

    /**
     * @param Request $request
     *
     * @throws JsonException
     *
     * @return bool|string
     */
    public function checkAddContent(Request $request): bool | string
    {
        if ($request->getContent()) {
            return $request->getContent();
        }

        return json_encode($request->request->all(), JSON_THROW_ON_ERROR);
    }
}
