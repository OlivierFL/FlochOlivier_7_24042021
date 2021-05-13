<?php

namespace App\Service;

use App\Entity\Product;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class ProductsService
{
    public function __construct(private PaginatorInterface $paginator)
    {
    }

    /**
     * @param Product[] $products
     * @param int       $page
     * @param int       $limit
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

        return $this->formatResult($page, $productsPaginated, $products);
    }

    /**
     * @param int                 $page
     * @param PaginationInterface $productsPaginated
     * @param array               $products
     *
     * @return array
     */
    private function formatResult(int $page, PaginationInterface $productsPaginated, array $products): array
    {
        $result = [];
        $result['page'] = $page;
        $result['count'] = \count($productsPaginated);
        $result['total'] = \count($products);
        $result['data'] = $productsPaginated;

        return $result;
    }
}
