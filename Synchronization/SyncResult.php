<?php
/*
 * This file is part of the Manuel Aguirre Project.
 *
 * (c) Manuel Aguirre <programador.manuel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ManuelAguirre\Bundle\TranslationBundle\Synchronization;

/**
 * @author Manuel Aguirre <programador.manuel@gmail.com>
 */
class SyncResult
{
    public function __construct(
        private readonly int $news,
        private readonly int $updated,
        private readonly array $conflictItems,
    ) {
    }

    public function getNews(): int
    {
        return $this->news;
    }

    public function getUpdated(): int
    {
        return $this->updated;
    }

    public function getConflictItems(): array
    {
        return $this->conflictItems;
    }
}