<?php
/**
 * @author Manuel Aguirre
 */

namespace ManuelAguirre\Bundle\TranslationBundle\Doctrine\Listener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use ManuelAguirre\Bundle\TranslationBundle\Entity\Translation;

/**
 * @author Manuel Aguirre
 */
class ChangeTableNameListener
{
    private string $tablePrefix;

    public function __construct(string $tablePrefix)
    {
        $this->tablePrefix = rtrim($tablePrefix, '_') . '_';
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $metadata = $eventArgs->getClassMetadata();

        if (!in_array($metadata->getName(), [
            Translation::class,
        ])) {
            return;
        }

        $metadata->setPrimaryTable([
            'name' => $this->tablePrefix . $metadata->getTableName()
        ]);
    }
}