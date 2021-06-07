<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\PaginationService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as Doc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends ApiController
{
    /**
     * @Doc\Response(
     *     response=200,
     *     description="Returns the products list",
     *     @Doc\JsonContent(
     *         type="array",
     *         @Doc\Items(ref=@Model(type=Product::class))
     *     )
     * )
     * @Doc\Parameter(
     *     name="page",
     *     in="query",
     *     description="Page number",
     *     @Doc\Schema(type="string")
     * )
     * @Doc\Parameter(
     *     name="limit",
     *     in="query",
     *     description="Number of products per page",
     *     @Doc\Schema(type="string")
     * )
     * @Doc\Parameter(
     *     name="sort",
     *     in="query",
     *     description="The field used to sort the products list (field name must be in camelCase)",
     *     @Doc\Schema(type="string")
     * )
     * @Doc\Parameter(
     *     name="direction",
     *     in="query",
     *     description="Direction (ASC or DESC) to sort the products list",
     *     @Doc\Schema(type="string")
     * )
     * @Doc\Tag(name="Products")
     * @Security(name="Bearer")
     *
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
     * @Doc\Response(
     *     response=200,
     *     description="Returns the product detail",
     *     @Model(type=Product::class)
     * )
     * @Doc\Response(
     *     response=403,
     *     description="Access denied",
     * )
     * @Doc\Response(
     *     response=404,
     *     description="Product not found",
     * )
     * @Doc\Tag(name="Products")
     * @Security(name="Bearer")
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
