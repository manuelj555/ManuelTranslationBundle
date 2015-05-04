<?php
/*
 * This file is part of the Manuel Aguirre Project.
 *
 * (c) Manuel Aguirre <programador.manuel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ManuelAguirre\Bundle\TranslationBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * @author Manuel Aguirre <programador.manuel@gmail.com>
 */
class TranslationToFileCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('manuel:translation:write')
            ->setDescription("Copia todas las traducciones que están en la Base de datos a un archivo de texto");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sync = $this->getContainer()->get('manuel_translation.local_synchronizator');

        $output->writeln("Copiando traducciones al archivo");

        $file = $sync->toFile();

        $output->writeln(sprintf("El archivo <comment>%s</comment> se ha creado/actualizado con exito", $file));
    }

}