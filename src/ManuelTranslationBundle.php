<?php

namespace ManuelAguirre\Bundle\TranslationBundle;

use ManuelAguirre\Bundle\TranslationBundle\DependencyInjection\Compiler\AddTranslatorLoadersPass;
use ManuelAguirre\Bundle\TranslationBundle\DependencyInjection\Compiler\AddTranslatorResourcesPass;
use ManuelAguirre\Bundle\TranslationBundle\DependencyInjection\Compiler\ConfigureExtractorsPass;
use ManuelAguirre\Bundle\TranslationBundle\DependencyInjection\Compiler\LoggingTranslatorPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use function dirname;

class ManuelTranslationBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddTranslatorLoadersPass());
//        $container->addCompilerPass(new ConfigureExtractorsPass());
        $container->addCompilerPass(new AddTranslatorResourcesPass(), PassConfig::TYPE_OPTIMIZE);

//        if ($container->getParameter('kernel.debug')) {
//            $container->addCompilerPass(new LoggingTranslatorPass());
//        }
    }

    public function getPath(): string
    {
        return dirname(__DIR__);
    }
}
