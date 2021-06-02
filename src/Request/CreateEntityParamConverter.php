<?php

namespace App\Request;

use App\Service\FileUploader;
use App\Service\JsonService;
use JsonException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class CreateEntityParamConverter implements ParamConverterInterface
{
    /**
     * CreateEntityParamConverter constructor.
     *
     * @param SerializerInterface $serializer
     * @param FileUploader        $uploader
     * @param JsonService         $jsonService
     */
    public function __construct(private SerializerInterface $serializer, private FileUploader $uploader, private JsonService $jsonService)
    {
    }

    /**
     * {@inheritDoc}
     *
     * @throws JsonException
     */
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $entity = $this->serializer->deserialize(
            $this->jsonService->toJson($request),
            $configuration->getClass(),
            'json'
        );

        if ($request->files->get('logo')) {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $request->files->get('logo');
            $filename = $this->uploader->upload($uploadedFile);
            $entity->setLogoUrl($filename);
        }

        $request->attributes->set($configuration->getName(), $entity);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function supports(ParamConverter $configuration): bool
    {
        $entity = strtolower((str_replace('App\Entity\\', '', $configuration->getClass())));

        return $configuration->getName() === $entity;
    }
}
