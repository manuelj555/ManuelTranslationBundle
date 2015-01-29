<?php

namespace ManuelAguirre\Bundle\TranslationBundle\Translation\Extractor;

use ManuelAguirre\Bundle\TranslationBundle\Translation\FileMessageCatalogue;
use Symfony\Bridge\Twig\Translation\TwigExtractor as BaseExtractor;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Translation\MessageCatalogue;

class TwigExtractor extends BaseExtractor
{
    /**
     * {@inheritdoc}
     */
    public function extract($directory, MessageCatalogue $catalogue)
    {
        // load any existing translation files
        $finder = new Finder();
        $files = $finder->files()->name('*.twig')->sortByName()->in($directory);

        foreach ($files as $file) {

            $fileCatalog = new MessageCatalogue($catalogue->getLocale());

            try {
                $this->extractTemplate(file_get_contents($file->getPathname()), $fileCatalog);
            } catch (\Twig_Error $e) {
                $e->setTemplateFile($file->getRelativePathname());

                throw $e;
            }

            $catalogue->addCatalogue($fileCatalog);

            if($catalogue instanceof FileMessageCatalogue){
                $catalogue->addInFile($file, $fileCatalog->all());
            }

        }
    }
}
