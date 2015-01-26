<?php
/*
 * This file is part of the Manuel Aguirre Project.
 *
 * (c) Manuel Aguirre <programador.manuel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ManuelAguirre\Bundle\TranslationBundle;

use Doctrine\ORM\Mapping\ClassMetaData;
use Doctrine\ORM\Query\Filter\SQLFilter;

/**
 * @autor Manuel Aguirre <programador.manuel@gmail.com>
 */
class TestFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {

        $class = $targetEntity->getReflectionClass()->getName();

        if($class == 'ManuelAguirre\Bundle\TranslationBundle\Entity\TranslationValue'){

            return $targetTableAlias . '.id != 5';
        }

        return '';
    }
}