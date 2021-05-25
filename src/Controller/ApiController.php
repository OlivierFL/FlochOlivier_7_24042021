<?php

namespace App\Controller;

use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\PaginatedRepresentation;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends AbstractController
{
    /**
     * ApiController constructor.
     *
     * @param SerializerInterface $serializer
     */
    public function __construct(private SerializerInterface $serializer)
    {
    }

    /**
     * @param mixed $data
     * @param int   $status
     *
     * @return Response
     */
    public function jsonApiResponse(mixed $data, int $status = 200): Response
    {
        $jsonData = $this->serializer->serialize($data, 'json');

        return new JsonResponse($jsonData, $status, [], true);
    }

    /**
     * @param array  $data
     * @param string $route
     *
     * @return Response
     */
    public function jsonApiResponseList(array $data, string $route): Response
    {
        $pages = (int) ceil($data['total'] / $data['limit']);

        $paginatedData = new PaginatedRepresentation(
            new CollectionRepresentation($data['data']),
            $route,
            [],
            (int) $data['page'],
            (int) $data['limit'],
            $pages,
            'page',
            'limit',
            true
        );

        return $this->jsonApiResponse($paginatedData);
    }
}
