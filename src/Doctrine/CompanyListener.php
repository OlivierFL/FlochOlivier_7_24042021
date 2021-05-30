<?php

namespace App\Doctrine;

use App\Entity\Company;
use Symfony\Component\HttpFoundation\UrlHelper;

class CompanyListener
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
     * @param Company $company
     */
    public function postLoad(Company $company): void
    {
        if (null === $company->getLogoUrl()) {
            return;
        }

        $company->setLogoUrl($this->urlHelper->getAbsoluteUrl($this->uploadsDirectory.'/'.$company->getLogoUrl()));
    }
}
