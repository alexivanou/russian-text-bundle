<?php

namespace AlexIvanou\RussianTextBundle\DependencyInjection\Compiler;

use AlexIvanou\RussianTextBundle\Service\IdentifierValidator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ValidatorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(IdentifierValidator::class)) {
            return;
        }

        $definition = $container->findDefinition(IdentifierValidator::class);
        $taggedServices = $container->findTaggedServiceIds('russian_text.validator');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addValidator', array(new Reference($id)));
        }
    }
}
