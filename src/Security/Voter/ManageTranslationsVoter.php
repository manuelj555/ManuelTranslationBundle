<?php
/**
 * @author Manuel Aguirre
 */

namespace ManuelAguirre\Bundle\TranslationBundle\Security\Voter;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\The;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use function in_array;

/**
 * @author Manuel Aguirre
 */
class ManageTranslationsVoter extends Voter
{
    const MANAGE_TRANSLATIONS = 'manage_translations';

    public function __construct(
        private Security $security,
        private RequestStack $requestStack,
        private string $securityRole,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $this->supportsAttribute($attribute);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return $this->security->isGranted($this->securityRole) || $this->isLocalServer();
    }

    public function supportsAttribute(string $attribute): bool
    {
        return self::MANAGE_TRANSLATIONS === $attribute;
    }

    public function supportsType(string $subjectType): bool
    {
        return 'null' == $subjectType;
    }

    private function isLocalServer(): bool
    {
        $ip = $this->requestStack->getCurrentRequest()->getClientIp();

        return in_array($ip, ['127.0.0.1', 'fe80::1', '::1']);
    }
}