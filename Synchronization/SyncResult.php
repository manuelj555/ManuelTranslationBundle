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
    private $news = 0;
    private $updated = 0;
    private $conflictItems = array();

    /**
     * SyncResult constructor.
     *
     * @param int   $news
     * @param int   $updated
     * @param array $conflictItems
     */
    public function __construct($news, $updated, array $conflictItems)
    {
        $this->news = $news;
        $this->updated = $updated;
        $this->conflictItems = $conflictItems;
    }

    /**
     * @return int
     */
    public function getNews()
    {
        return $this->news;
    }

    /**
     * @return int
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @return array
     */
    public function getConflictItems()
    {
        return $this->conflictItems;
    }

}