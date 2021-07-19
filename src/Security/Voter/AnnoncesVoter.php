<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Annonces;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AnnoncesVoter extends Voter
{
    const ANNONCE_EDIT = 'annonce_edit';
    const ANNONCE_DELETE = 'annonce_delete';
    
    protected function supports(string $attribute, $annonce): bool
    {

        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::ANNONCE_EDIT, self::ANNONCE_DELETE])
            && $annonce instanceof \App\Entity\Annonces;
    }

    protected function voteOnAttribute(string $attribute, $annonce, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }
        // On vérifie si l'annonce a un propriétaire
        if(null === $annonce->getUser()) return false;

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::ANNONCE_EDIT:
                // On vérifie si on peut éditer
                return $this->canEdit($annonce, $user);
                break;
            case self::ANNONCE_DELETE:
                // On vérifie si on peut supprimer
                return $this->canDelete($annonce, $user);
                break;
        }

        return false;
    }
    private function canEdit(Annonces $annonce, User $user){
        //Le propriétaire de l'annonce peut la modifier
       
        return $user === $annonce->getUser();
    }
    private function canDelete(Annonces $annonce, User $user){
        //Le propriétaire de l'annonce peut la supprimer
        return $annonce->getUser();
    }
}
