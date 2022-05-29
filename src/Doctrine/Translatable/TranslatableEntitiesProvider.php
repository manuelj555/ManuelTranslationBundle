<?php
/**
 * @author Manuel Aguirre
 */

namespace ManuelAguirre\Bundle\TranslationBundle\Doctrine\Translatable;

/**
 * @author Manuel Aguirre
 */
class TranslatableEntitiesProvider
{
    public function __construct(private readonly array $entities)
    {
    }

    public function getEntities(): array
    {
        return $this->entities;
    }
}