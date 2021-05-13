<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Service\ProductsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class ProductController extends AbstractController
{
    public function __construct(private ProductsService $productsService)
    {
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route(
        '/products',
        name: 'products_list',
        methods: ['GET']
    )]
    public function getProducts(Request $request, ProductRepository $repository): Response
    {
        $products = $repository->findAll();
        $page = max(1, $request->query->getInt('page', 1));
        $limit = $request->query->getInt('limit', 10);

        $productsPaginated = $this->productsService->getProductsPaginated($products, $page, $limit);

        return $this->json($productsPaginated);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route(
        '/products/{id}',
        name: 'product_detail',
        requirements: ['id' => '\d+'],
        methods: ['GET']
    )]
    public function getOneProduct(int $id, ProductRepository $repository): Response
    {
        $product = $repository->findOneBy(['id' => $id]);

        if (!$product) {
            return $this->json(['Error' => 'Product not found'], 404);
        }

        $product = $this->productsService->normalize($product);

        return $this->json($product);
    }
}
