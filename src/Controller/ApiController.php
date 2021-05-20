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
    public const HAL_JSON = 'application/hal+json';

    public function __construct(private SerializerInterface $serializer)
    {
    }

    /**
     * @param mixed $data
     * @param int   $code
     * @param array $headers
     *
     * @return Response
     */
    public function jsonApiResponse(mixed $data, int $code = 200, array $headers = []): Response
    {
        $headers['Content-Type'] = self::HAL_JSON;
        $jsonData = $this->serializer->serialize($data, 'json');

        return new JsonResponse($jsonData, $code, $headers, true);
    }

    /**
     * @param array $data
     * @param int   $page
     * @param int   $limit
     * @param int   $total
     *
     * @return Response
     */
    public function jsonApiResponseList(array $data, int $page = 1, int $limit = 10, int $total = 0): Response
    {
        $pages = (int) ceil($total / $limit);

        $paginatedData = new PaginatedRepresentation(
            new CollectionRepresentation($data),
            'api_users_list',
            [],
            $page,
            $limit,
            $pages,
            'page',
            'limit',
            true
        );

        return $this->jsonApiResponse($paginatedData);
    }
}
