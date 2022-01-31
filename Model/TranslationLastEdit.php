<?php

namespace ManuelAguirre\Bundle\TranslationBundle\Model;

/**
 * @author Manuel Aguirre
 */
enum TranslationLastEdit: string
{
    case LOCAL = 'local';
    case FILE = 'file';
}