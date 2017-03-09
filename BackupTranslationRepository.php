<?php
/**
 * Optime Consulting
 * User: manuel
 * Date: 09-03-2017
 */

namespace ManuelAguirre\Bundle\TranslationBundle;

/**
 * @author maguirre <maguirre@developerplace.com>
 */
class BackupTranslationRepository implements TranslationRepository
{
    private $backupDir;

    /**
     * BackupTranslationRepository constructor.
     *
     * @param $backupDir
     */
    public function __construct($backupDir)
    {
        $this->backupDir = $backupDir;
    }

    public function getActiveTranslations()
    {
        $file = rtrim($this->backupDir, '/').'/translations.php';

        if (is_file($file)) {
            $data = require $file;
        } else {
            return [];
        }

        $translations = [];

        foreach ($data['translations'] as $domain => $messages) {
            foreach ($messages as $code => $item) {
                $translations[$code] = [
                    'code' => $code,
                    'domain' => $domain,
                    'values' => $item['values'],
                ];
            }
        }

        return $translations;
    }
}