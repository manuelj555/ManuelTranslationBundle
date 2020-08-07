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

use ManuelAguirre\Bundle\TranslationBundle\Synchronization\Synchronizator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Manuel Aguirre <programador.manuel@gmail.com>
 */
class TranslationToFileCommand extends Command
{
    protected static $defaultName = 'manuel:translation:generate';
    /**
     * @var Synchronizator
     */
    private $synchronizator;

    public function __construct(Synchronizator $synchronizator)
    {
        parent::__construct();

        $this->synchronizator = $synchronizator;
    }

    protected function configure()
    {
        $this
            ->setDescription("Copia todas las traducciones que están en la Base de datos a un archivo de texto")
            ->setAliases(['manuel:translation:update']);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->writeln("Copiando traducciones al archivo");

        $result = $this->synchronizator->generateFile();

        if ($result) {
            $io->success("El archivo de traducciones se ha creado/actualizado con exito");
        } else {
            $io->error("No se pudo actualizar el archivo, debe sincronizar su base de datos antes");
        }

        return 0;
    }

}