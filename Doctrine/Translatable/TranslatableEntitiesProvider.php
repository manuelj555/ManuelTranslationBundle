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
    private $entities;

    public function __construct(array $entities)
    {
        $this->entities = $entities;
    }

    public function getEntities(): array
    {
        return $this->entities;
    }
}