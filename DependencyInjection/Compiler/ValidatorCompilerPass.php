<?php

namespace AlexIvanou\RussianTextBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ValidatorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('alexivanou.russian_text.identifier_validator')) {
            return;
        }

        $definition = $container->findDefinition('alexivanou.russian_text.identifier_validator');
        $taggedServices = $container->findTaggedServiceIds('russian_text.validator');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addValidator', array(new Reference($id)));
        }
    }
}
