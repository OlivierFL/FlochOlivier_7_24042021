<?php

namespace App\Service;

use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

class NormalizationService
{
    public function __construct(private SerializerInterface $serializer)
    {
    }

    /**
     * @param mixed $data
     *
     * @throws ExceptionInterface
     *
     * @return mixed
     */
    public function normalize(mixed $data): mixed
    {
        return $this->serializer->normalize($data, null, [
            'circular_reference_handler' => fn ($object) => $object->getId(),
        ]);
    }
}
