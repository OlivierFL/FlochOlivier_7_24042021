<?php

namespace App\Doctrine;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\UrlHelper;

class ProductListener
{
    /**
     * ProductListener constructor.
     *
     * @param UrlHelper $urlHelper
     * @param string    $uploadsDirectory
     */
    public function __construct(private UrlHelper $urlHelper, private string $uploadsDirectory)
    {
    }

    /**
     * @param Product $product
     */
    public function postLoad(Product $product): void
    {
        if (null === $product->getCoverImgUrl()) {
            return;
        }

        $product->setCoverImgUrl($this->urlHelper->getAbsoluteUrl($this->uploadsDirectory.'/'.$product->getCoverImgUrl()));
    }
}
