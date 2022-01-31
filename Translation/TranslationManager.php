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

use ManuelAguirre\Bundle\TranslationBundle\Translation\Dumper\DoctrineDumper;
use ManuelAguirre\Bundle\TranslationBundle\Translation\Loader\DoctrineLoader;
use Symfony\Component\Translation\Extractor\ExtractorInterface;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\Reader\TranslationReaderInterface;

/**
 * @autor Manuel Aguirre <programador.manuel@gmail.com>
 */
class TranslationManager
{
    private array $filesPrefix;

    public function __construct(
        private ExtractorInterface $extractor,
        private TranslationReaderInterface $translationReader,
        private DoctrineLoader $translationLoader,
        private DoctrineDumper $translationDumper,
        private array $locales,
        private array $extractDirs,
        private array $translationFilesDirs,
    ) {
    }

    public function setFilesPrefix(array $filesPrefix): void
    {
        $this->filesPrefix = $filesPrefix;
    }

    public function getUsedMessages(): FileMessageCatalogue
    {
        $locale = current($this->locales);
        $usedMessages = new FileMessageCatalogue($this->filesPrefix, $locale);

        foreach ($this->extractDirs as $dir) {
            $this->extractor->extract($dir, $usedMessages);
        }

        return $usedMessages;
    }

    protected function loadFileMessages(string $locale): MessageCatalogue
    {
        $catalogue = new MessageCatalogue($locale);

        foreach ($this->translationFilesDirs as $dir) {
            $this->translationReader->read($dir, $catalogue);
        }

        return $catalogue;
    }

    public function extractToDatabase()
    {
        $usedMessages = $this->getUsedMessages();

        $catalogues = array();

        foreach ($this->locales as $locale) {
            $fileMessages = $this->loadFileMessages($locale);
            $forDump = new MessageCatalogue($locale, $usedMessages->all());

            foreach ($usedMessages as $domain => $items) {
                foreach ($items as $usedCode => $usedValue) {
                    if ($fileMessages->has($usedCode, $domain)) {
                        $forDump->set($usedCode, $fileMessages->get($usedCode, $domain), $domain);
                    }
                }
            }

            $catalogues[$locale] = $forDump;
        }

        $this->translationDumper->dumpCatalogues($catalogues);
    }
}