<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends ApiController
{
    #[Route(
        '/products',
        name: 'products_list',
        methods: ['GET']
    )]
    public function getProducts(Request $request, ProductRepository $repository): Response
    {
        $page = max(1, $request->query->getInt('page', 1));
        $limit = $request->query->getInt('limit', 10);
        $offset = ($page - 1) * $limit;
        $total = $repository->count([]);
        $products = $repository->findBy([], [], $limit, $offset);

        return $this->jsonApiResponseList($products, page: $page, limit: $limit, total: $total);
    }

    /**
     * @ParamConverter(converter="doctrine.orm", "product", class="App\Entity\Product")
     */
    #[Route(
        '/products/{id}',
        name: 'product_detail',
        requirements: ['id' => '\d+'],
        methods: ['GET']
    )]
    public function getOneProduct(Product $product): Response
    {
        return $this->jsonApiResponse($product);
    }
}
