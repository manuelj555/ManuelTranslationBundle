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

use ManuelAguirre\Bundle\TranslationBundle\Synchronization\SyncResult;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;


/**
 * @author Manuel Aguirre <programador.manuel@gmail.com>
 */
class TranslationToDbCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('manuel:translation:sync')
            ->setDescription("Sincroniza las traducciones que están en el archivo con la Base de datos")
            ->addOption('show-conflicts', 'sc', InputOption::VALUE_OPTIONAL,
                'Muestra las etiquetas con conflictos si las hay');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->writeln("Sincronizando...");

        $result = $this->getContainer()->get('manuel_translation.synchronizator')->sync();
        $filenameTemplate = $this->getContainer()->getParameter('manuel_translation.filename_template');

        foreach ($this->getContainer()->getParameter('manuel_translation.locales') as $locale) {
            $filename = sprintf($filenameTemplate, $locale);
            $this->getContainer()->get('filesystem')->dumpFile($filename, time());
        }

        if (0 === $numConflicts = count($result->getConflictItems())) {
            $io->success("La base de datos ha sido actualizada correctamente");
        } else {
            $io->warning("La base de datos ha sido actualizada, pero no se pudieron actualizar todas las traducciones");
        }

        $io->table([], [
            ['Items Creados', sprintf('<fg=green>%d</>', $result->getNews())],
            ['Items Actualizados', sprintf('<fg=blue>%d</>', $result->getUpdated())],
            ['Items con conflictos', sprintf('<fg=red>%d</>', $numConflicts)],
        ]);

        if (0 !== $numConflicts) {
            $io->section("Traducciones en conflicto:");

            if ($input->hasOption('show-conflicts')) {
                $io->write('<options=bold;fg=yellow>');
                $io->listing(iterator_to_array($this->getConflictedCodes($result)));
                $io->write('</>');
            }

            $io->newLine();
            $io->writeln("Debe sincronizar desde el navegador para poder resolver los conflictos generados!!!");
        }
    }

    private function getConflictedCodes(SyncResult $result)
    {
        foreach ($result->getConflictItems() as $conflictItem) {
            yield $conflictItem['file']->getCode();
        }
    }

}
