<?php

namespace App\Doctrine;

use App\Entity\Brand;
use App\Entity\Company;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\UrlHelper;

class AbsoluteUrlListener
{
    /**
     * AbsoluteUrlListener constructor.
     *
     * @param UrlHelper $urlHelper
     * @param string    $uploadsDirectory
     */
    public function __construct(private UrlHelper $urlHelper, private string $uploadsDirectory)
    {
    }

    /**
     * @param object $entity
     */
    public function postLoad(object $entity): void
    {
        if ($entity instanceof Product) {
            $this->getAbsoluteUrl($entity, 'CoverImgUrl');
        }

        if ($entity instanceof Brand || $entity instanceof Company) {
            $this->getAbsoluteUrl($entity, 'LogoUrl');
        }
    }

    /**
     * @param object $entity
     * @param string $methodName
     */
    private function getAbsoluteUrl(object $entity, string $methodName): void
    {
        $getUrl = 'get'.$methodName;
        $setUrl = 'set'.$methodName;

        if (null === $entity->{$getUrl}()) {
            return;
        }

        $entity->{$setUrl}($this->urlHelper->getAbsoluteUrl($this->uploadsDirectory.'/'.$entity->{$getUrl}()));
    }
}
