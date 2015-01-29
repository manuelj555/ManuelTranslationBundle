<?php
/*
 * This file is part of the Manuel Aguirre Project.
 *
 * (c) Manuel Aguirre <programador.manuel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ManuelAguirre\Bundle\TranslationBundle\Translation;

use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Translation\MessageCatalogue;


/**
 * @autor Manuel Aguirre <programador.manuel@gmail.com>
 */
class FileMessageCatalogue extends MessageCatalogue
{
    private $filesMessages;
    private $filesPrefix;

    public function __construct($filesPrefix, $locale, array $messages = array())
    {
        parent::__construct($locale, $messages);

        $this->filesPrefix = $filesPrefix;
    }

    public function addInFile(SplFileInfo $file, $messages)
    {
        $file = trim(strtr((string) $file, $this->filesPrefix), '\\/');

        foreach ($messages as $domain => $items) {
            foreach ($items as $code => $val) {
                $this->filesMessages[$domain][$code][] = $file;
            }
        }
    }

    /**
     * @param mixed $filesMessages
     */
    public function setFilesMessages($filesMessages)
    {
        $this->filesMessages = $filesMessages;
    }

    /**
     * @return mixed
     */
    public function getFilesMessages()
    {
        return $this->filesMessages;
    }
}