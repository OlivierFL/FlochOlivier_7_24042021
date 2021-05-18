<?php

namespace App\Doctrine;

use App\Entity\User;
use Symfony\Component\Security\Core\Security;

class UserSetCompanyListener
{
    /**
     * UserSetCompanyListener constructor.
     *
     * @param Security $security
     */
    public function __construct(private Security $security)
    {
    }

    /**
     * @param User $user
     */
    public function prePersist(User $user): void
    {
        if ($user->getCompany()) {
            return;
        }

        $company = $this->security->getUser();
        if ($company) {
            $user->setCompany($company);
        }
    }
}
