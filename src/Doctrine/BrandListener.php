<?php

namespace App\Doctrine;

use App\Entity\Brand;
use Symfony\Component\HttpFoundation\UrlHelper;

class BrandListener
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
     * @param Brand $brand
     */
    public function postLoad(Brand $brand): void
    {
        if (null === $brand->getLogoUrl()) {
            return;
        }

        $brand->setLogoUrl($this->urlHelper->getAbsoluteUrl($this->uploadsDirectory.'/'.$brand->getLogoUrl()));
    }
}
