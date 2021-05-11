<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function getProducts(ProductRepository $repository, SerializerInterface $serializer): Response
    {
        $products = $repository->findAll();

        $products = $serializer->normalize($products, null, [
            AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true,
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            },
        ]);

        return $this->json($products);
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
