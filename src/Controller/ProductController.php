<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\PaginationService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends ApiController
{
    /**
     * @param Request           $request
     * @param ProductRepository $repository
     * @param PaginationService $pagination
     *
     * @return Response
     */
    #[Route(
        '/products',
        name: 'products_list',
        methods: ['GET']
    )]
    public function getProducts(Request $request, ProductRepository $repository, PaginationService $pagination): Response
    {
        $dataPaginated = $pagination->getDataPaginated($request, $repository, []);

        return $this->jsonApiResponseList($dataPaginated, $request->attributes->get('_route'));
    }

    /**
     * @ParamConverter(converter="doctrine.orm", "product", class="App\Entity\Product")
     *
     * @param Product $product
     *
     * @return Response
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
