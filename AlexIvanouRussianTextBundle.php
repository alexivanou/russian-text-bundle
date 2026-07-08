<?php

namespace AlexIvanou\RussianTextBundle;

use AlexIvanou\RussianTextBundle\DependencyInjection\Compiler\ValidatorCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AlexIvanouRussianTextBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ValidatorCompilerPass());
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new DependencyInjection\AlexIvanouRussianTextExtension();
        }

        return $this->extension;
    }
}
