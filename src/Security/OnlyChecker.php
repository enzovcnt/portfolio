<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;

class OnlyChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if ($user->getUserIdentifier() !== 'enzovcnt@gmail.com') {
            throw new CustomUserMessageAccountStatusException('.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // rien à faire ici pour ton besoin
    }

}
