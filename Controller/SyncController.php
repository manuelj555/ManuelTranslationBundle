<?php
/*
 * This file is part of the Manuel Aguirre Project.
 *
 * (c) Manuel Aguirre <programador.manuel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ManuelAguirre\Bundle\TranslationBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


/**
 * @author Manuel Aguirre <programador.manuel@gmail.com>
 */
class SyncController extends Controller
{
    /**
     * @Route("/generate-files")
     */
    public function generateFilesAction()
    {
        $this->get('manuel_translation.synchronizator')->generateFiles();

        die('LISTO');

        return $this->redirectToRoute('manuel_translation_list');
    }
    /**
     * @Route("/sync")
     */
    public function syncAction()
    {
        $this->get('manuel_translation.synchronizator')->sync();

        die('LISTO');

        return $this->redirectToRoute('manuel_translation_list');
    }
}