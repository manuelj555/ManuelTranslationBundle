<?php

namespace ManuelAguirre\Bundle\TranslationBundle;

use ManuelAguirre\Bundle\TranslationBundle\DependencyInjection\Compiler\AddTranslatorLoadersPass;
use ManuelAguirre\Bundle\TranslationBundle\DependencyInjection\Compiler\ConfigureExtractorsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ManuelTranslationBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddTranslatorLoadersPass());
        $container->addCompilerPass(new ConfigureExtractorsPass());
    }

}
