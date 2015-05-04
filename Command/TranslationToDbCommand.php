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
use Symfony\Component\Console\Question\ConfirmationQuestion;


/**
 * @author Manuel Aguirre <programador.manuel@gmail.com>
 */
class TranslationToDbCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('manuel:translation:load')
            ->setDescription("Copia todas las traducciones que están en el archivo a la Base de datos");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sync = $this->getContainer()->get('manuel_translation.local_synchronizator');

        $output->writeln("Este proceso Modificará las traducciones sin verificar modificaciones en la base de datos");
        $helper = $this->getHelper('question');

        $question = new ConfirmationQuestion('¿Está seguro de querer actualizar las traducciones? [y/N] ', false);

        if (!$helper->ask($input, $output, $question)) {
            return;
        }

        $output->writeln("Copiando las traducciones a la Base de datos");

        if(!$backupName = $sync->fromFile()){
            $output->writeln("No se pudo completar la operación");
            $output->writeln("Verifique que el archivo exista y se encuentre en el directorio correcto");
            return 1;
        }

        $output->writeln("La base de datos ha sido actualizada correctamente");
        $output->writeln(sprintf("Además se ha generado el archivo de backup <comment>%s</comment>", $backupName));
    }

}