<?php

namespace App\Service;

use App\Entity\Product;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class ProductsService
{
    public function __construct(private PaginatorInterface $paginator, private string $productsListLimit)
    {
    }

    /**
     * @param Product[] $products
     * @param int       $page
     *
     * @return array
     */
    public function getProductsPaginated(array $products, int $page): array
    {
        $productsPaginated = $this->paginator->paginate(
            $products,
            $page,
            (int) $this->productsListLimit
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
