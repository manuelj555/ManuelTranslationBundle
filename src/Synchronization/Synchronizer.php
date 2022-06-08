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

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use ManuelAguirre\Bundle\TranslationBundle\Entity\Translation;
use ManuelAguirre\Bundle\TranslationBundle\Entity\TranslationRepository;
use ManuelAguirre\Bundle\TranslationBundle\Model\TranslationLastEdit;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @autor Manuel Aguirre <programador.manuel@gmail.com>
 */
class Synchronizer
{
    const STATUS_SUCCESS = 1;
    const STATUS_ERROR = 2;
    const STATUS_CONFLICT = 3;

    public function __construct(
        private EntityManagerInterface $om,
        private TranslationRepository $translationRepository,
        private Filesystem $filesystem,
        private string $cacheDir,
        private array $locales,
        private string $backupDir,
    ) {
    }

    public function generateFile(?string $path = null): bool
    {
        $fileHash = $this->getFileHash();
        $localHash = $this->getLocalHash();

        if (false !== $fileHash and $fileHash != $localHash) {
            return false;
        }

        $newHash = $this->generateHashNumber();

        if (null === $path) {
            $path = rtrim($this->backupDir, '/') . '/translations.php';
        }

        $translations = $this->translationRepository->getAll();

        $export = $this->getFileData();
        $export['hash'] = $newHash;

        /** @var Translation $translation */
        foreach ($translations as $translation) {
            $export['translations'][$translation['domain']][$translation['code']] = [
                'hash' => $translation['hash'],
                'active' => $translation['active'],
            ];

            foreach ($translation['values'] as $locale => $value) {
                $export['translations'][$translation['domain']][$translation['code']]['values'][$locale] = $value;
            }
        }

        ksort($export['translations']);

        foreach ($export['translations'] as $domain => $items) {
            ksort($export['translations'][$domain]);
        }

        $output = "<?php\n\nreturn " . var_export($export, true) . ";\n";

        $this->filesystem->dumpFile($path, $output);
        $this->updateLocalHash($newHash);

        return true;
    }

    public function sync(bool $forced = false): SyncResult
    {
        list($fileHash, $fileTranslations) = $this->createTranslationsFromFile();
        $localHash = $this->getLocalHash();

        if ($forced || (false !== $fileHash and $fileHash !== $localHash)) {
            $result = $this->doSync($fileTranslations);

            if (count($result->getConflictItems()) == 0) {
                $this->updateLocalHash($fileHash);
            }
        } else {
            $result = new SyncResult(0, 0, []);
        }

        return $result;
    }

    public function updateTranslation(
        Translation $translation,
        iterable $values,
        string $hash,
        bool $active,
        TranslationLastEdit $lastChanged
    ): void {
        foreach ($values as $locale => $value) {
            $translation->setValue($locale, $value);
        }

        $translation->setHash($hash);
        $translation->setActive($active);
        $translation->setLastChanged($lastChanged);

        $this->om->persist($translation);
    }

    public function markSyncAsDone(): void
    {
        $this->updateLocalHash($this->getFileHash());
    }

    protected function getValidValues(array $values): array
    {
        $valids = [];

        $locales = $this->locales;
        sort($locales);

        foreach ($locales as $locale) {
            if (array_key_exists($locale, $values)) {
                $valids[$locale] = $values[$locale];
            }
        }

        return $valids;
    }

    protected function doSync(iterable $fileTranslations): SyncResult
    {
        $dbTranslations = $this->getTranslationsFromDatabase();

        $conflicts = [];
        $numNews = $numUpdates = 0;

        foreach ($fileTranslations as $domain => $translations) {
            /**
             * @var string $code
             * @var Translation $t
             */
            foreach ($translations as $code => $t) {
                if (isset($dbTranslations[$domain][$code])) {
                    /** @var Translation $dbT */
                    $dbT = $dbTranslations[$domain][$code];

                    if ($t->getHash() === $dbT->getHash()) {
                        // Si lo hash son iguales, no se debe hacer ningún cambio
                        continue;
                    }
                    // debemos verificar si tienen data distinta
                    if ($this->withOutConflicts($t, $dbT) or $dbT->getLastChanged() !== TranslationLastEdit::LOCAL) {
                        $this->updateTranslation(
                            $dbT,
                            $t->getValues(),
                            $t->getHash(),
                            $t->getActive(),
                            $dbT->getLastChanged()
                        );
                        ++$numUpdates;
                    } else {
                        $conflicts[] = [
                            'file' => $t,
                            'database' => $dbT,
                            'hash' => $t->getHash(),
                            'database_hash' => $dbT->getHash(),
                        ];
                    }
                } else {
                    //La creamos de una vez
                    ++$numNews;
                    $this->om->persist($t);
                }
            }
        }

        $this->om->flush();

        return new SyncResult($numNews, $numUpdates, $conflicts);
    }

    protected function withOutConflicts(Translation $file, Translation $database): bool
    {
        if ($file->getActive() != $database->getActive()) {
            return false;
        } elseif ($file->getValues() != $database->getValues()) {
            // Si los valores son distintos, debemos verificar cada locale, para determinar si son iguales o no,
            // en base a los locales en la traduccion en el archivo
            $fileValues = $file->getValues();
            $dbValues = $database->getValues();

            foreach ($fileValues as $locale => $value) {
                if (isset($dbValues[$locale])) {
                    $one = str_replace("\r\n", "\n", $dbValues[$locale]);
                    $two = str_replace("\r\n", "\n", $value);

                    if ($one !== $two) {
                        return false;
                    }
                }
            }

            return true;
        }

        return true;
    }

    public function getFileData(): array
    {
        $path = rtrim($this->backupDir, '/') . '/translations.php';

        if (is_file($path)) {
            return require $path;
        } else {
            return array(
                'hash' => false,
                'translations' => [],
            );
        }
    }

    protected function createTranslationsFromFile(): array
    {
        $data = $this->getFileData();

        $hash = $data['hash'];
        $translations = $data['translations'];

        foreach ($translations as $domain => $items) {
            foreach ($items as $code => $info) {
                $translations[$domain][$code] = $t = new Translation($code, $domain);

                $t->setValues($info['values']);
                $t->setHash($info['hash']);
                $t->setActive($info['active']);
                $t->setLastChanged(TranslationLastEdit::FILE);
            }
        }

        return array($hash, $translations);
    }

    protected function getTranslationsFromDatabase(): array
    {
        $translations = [];
        $dbTranslations = $this->translationRepository->getAllEntities();

        /** @var Translation $translation */
        foreach ($dbTranslations as $translation) {
            $translations[$translation->getDomain()][$translation->getCode()] = $translation;
        }

        return $translations;
    }

    public function updateLocalHash(string $hash): void
    {
        $filename = rtrim($this->cacheDir, '/') . '/manuel_translations_hash';

        $this->filesystem->dumpFile($filename, $hash);
    }

    protected function getLocalHash(): string
    {
        $filename = rtrim($this->cacheDir, '/') . '/manuel_translations_hash';

        if (is_file($filename)) {
            return file_get_contents($filename);
        }

        $this->updateLocalHash($hash = $this->generateHashNumber());

        return $hash;
    }

    protected function getFileHash(): string|false
    {
        $path = rtrim($this->backupDir, '/') . '/translations.php';

        if (is_file($path)) {
            $data = require $path;
            return $data['hash'];
        } else {
            return false;
        }
    }

    private function generateHashNumber(): string
    {
        return uniqid(md5(time()));
    }
}
