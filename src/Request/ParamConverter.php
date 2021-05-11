<?php

namespace App\Request;

use App\Service\FileUploader;
use App\Service\JsonService;
use JsonException;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class ParamConverter implements ParamConverterInterface
{
    public function __construct(private SerializerInterface $serializer, private FileUploader $uploader, private JsonService $jsonService)
    {
    }

    /**
     * {@inheritDoc}
     *
     * @throws JsonException
     */
    public function apply(Request $request, \Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter $configuration): bool
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
    public function supports(\Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter $configuration): bool
    {
        $entity = strtolower((str_replace('App\Entity\\', '', $configuration->getClass())));

        return $configuration->getName() === $entity;
    }
}