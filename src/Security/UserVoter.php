<?php

namespace App\Security;

use App\Entity\Company;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    public const USER_DELETE = 'user_delete';
    public const USER_READ = 'user_read';

    /**
     * {@inheritDoc}
     */
    protected function supports(string $attribute, $subject): bool
    {
        if (!\in_array($attribute, [self::USER_READ, self::USER_DELETE], true)) {
            return false;
        }

        if (!$subject instanceof User) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $company = $token->getUser();

        if (!$company instanceof Company) {
            return false;
        }

        /** @var User $user */
        $user = $subject;

        return $user->getCompany() === $company;
    }
}
