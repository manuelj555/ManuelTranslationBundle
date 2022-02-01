<?php
/**
 * @author Manuel Aguirre
 */

namespace ManuelAguirre\Bundle\TranslationBundle\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\The;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

/**
 * @author Manuel Aguirre
 */
class ManageTranslationsVoter extends Voter
{
    const MANAGE_TRANSLATIONS = 'manage_translations';

    public function __construct(
        private Security $security,
        private string $securityRole,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $this->supportsAttribute($attribute);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return $this->security->isGranted($this->securityRole);
    }

    public function supportsAttribute(string $attribute): bool
    {
        return self::MANAGE_TRANSLATIONS === $attribute;
    }

    public function supportsType(string $subjectType): bool
    {
        return 'null' == $subjectType;
    }
}