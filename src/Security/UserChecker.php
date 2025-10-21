<?php

namespace App\Security;

use App\Entity\Main\Utilisateur;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    // Pour les verifications avant la connexion
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof Utilisateur) {
            return;
        }

        // Si l'utilisateur est désactivé, on empêche la connexion
        if ($user->isEnableYN() === false) {
            throw new CustomUserMessageAccountStatusException(
                'Votre compte est désactivé. Veuillez contacter l’administrateur (MINFI/SG/DGB/DDPP/CI).'
            );
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {}
}