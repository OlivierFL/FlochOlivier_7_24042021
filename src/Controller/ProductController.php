<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Service\ProductsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

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
    public function getProducts(Request $request, ProductRepository $repository, ProductsService $productsService, SerializerInterface $serializer): Response
    {
        $products = $repository->findAll();
        $page = max(1, $request->query->getInt('page', 1));
        $limit = $request->query->getInt('limit', 10);

        $productsPaginated = $productsService->getProductsPaginated($products, $page, $limit);

        $productsPaginated = $serializer->normalize($productsPaginated, null, [
            AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true,
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            },
        ]);

        return $this->json($productsPaginated);
    }

    #[Route(
        '/products/{id}',
        name: 'product_detail',
        requirements: ['id' => '\d+'],
        methods: ['GET']
    )]
    public function getOneProduct(int $id, ProductRepository $repository, SerializerInterface $serializer): Response
    {
        $product = $repository->findOneBy(['id' => $id]);
        $product = $serializer->normalize($product, null, [
            AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true,
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            },
        ]);

        return $this->json($product, 200, [], [
            'groups' => 'show_product',
        ]);
    }
}
