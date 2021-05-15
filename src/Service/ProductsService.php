<?php

namespace App\Service;

use App\Entity\Product;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ProductsService
{
    public function __construct(
        private PaginatorInterface $paginator,
        private SerializerInterface $serializer
    ) {
    }

    /**
     * @param Product[] $products
     * @param int       $page
     * @param int       $limit
     *
     * @throws ExceptionInterface
     *
     * @return array
     */
    public function getProductsPaginated(array $products, int $page, int $limit): array
    {
        $productsPaginated = $this->paginator->paginate(
            $products,
            $page,
            $limit
        );

        $productsPaginated = $this->normalize($productsPaginated);

        $total = \count($products);

        return $this->formatResult($productsPaginated, $total, $page, $limit);
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

    /**
     * @param array $productsPaginated
     * @param int   $total
     * @param int   $page
     * @param int   $limit
     *
     * @return array
     */
    private function formatResult(array $productsPaginated, int $total, int $page, int $limit): array
    {
        $result = [];
        $result['page'] = $page;
        $result['limit'] = $limit;
        $result['total'] = $total;
        $result['data'] = $productsPaginated;

        return $result;
    }
}
