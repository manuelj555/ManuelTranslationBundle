<?php

namespace ManuelAguirre\Bundle\TranslationBundle;

/**
 * @author maguirre <maguirre@developerplace.com>
 */
interface TranslationRepository
{
    public function getActiveTranslations();
}