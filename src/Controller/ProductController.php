<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\NormalizationService;
use App\Service\PaginationService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class ProductController extends AbstractController
{
    /**
     * @throws ExceptionInterface
     */
    #[Route(
        '/products',
        name: 'products_list',
        methods: ['GET']
    )]
    public function getProducts(Request $request, ProductRepository $repository, PaginationService $pagination): Response
    {
        $products = $repository->findAll();
        $page = max(1, $request->query->getInt('page', 1));
        $limit = $request->query->getInt('limit', 10);

        $productsPaginated = $pagination->paginateData($products, $page, $limit);

        return $this->json($productsPaginated);
    }

    /**
     * @throws ExceptionInterface
     * @ParamConverter(converter="doctrine.orm", "product", class="App\Entity\Product")
     */
    #[Route(
        '/products/{id}',
        name: 'product_detail',
        requirements: ['id' => '\d+'],
        methods: ['GET']
    )]
    public function getOneProduct(Product $product, NormalizationService $normalizationService): Response
    {
        $product = $normalizationService->normalize($product);

        return $this->json($product);
    }
}
