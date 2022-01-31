<?php


namespace ManuelAguirre\Bundle\TranslationBundle\Translation\Extractor;

use ManuelAguirre\Bundle\TranslationBundle\Translation\FileMessageCatalogue;
use Symfony\Bundle\FrameworkBundle\Translation\PhpExtractor as BaseExtrator;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Translation\MessageCatalogue;


class PhpExtractor extends BaseExtrator
{
    /**
     * {@inheritdoc}
     */
    public function extract($directory, MessageCatalogue $catalog)
    {
        // load any existing translation files
        $finder = new Finder();
        $files = $finder->files()->name('*.php')->in($directory);


        foreach ($files as $file) {

            $fileCatalog = new MessageCatalogue($catalog->getLocale());

            $this->parseTokens(token_get_all(file_get_contents($file)), $fileCatalog);

            $catalog->addCatalogue($fileCatalog);

            if ($catalog instanceof FileMessageCatalogue) {
                $catalog->addInFile($file, $fileCatalog->all());
            }
        }
    }
}
