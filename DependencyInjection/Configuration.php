<?php

namespace AlexIvanou\RussianTextBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('russian_text');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->booleanNode('enable_pluralizer')->defaultTrue()->end()
                ->booleanNode('enable_number_speller')->defaultTrue()->end()
                ->booleanNode('enable_money_speller')->defaultTrue()->end()
                ->booleanNode('enable_name_inflector')->defaultTrue()->end()
                ->booleanNode('enable_noun_decliner')->defaultTrue()->end()
                ->booleanNode('enable_adjective_decliner')->defaultTrue()->end()
                ->booleanNode('enable_geo_inflector')->defaultTrue()->end()
                ->booleanNode('enable_time_speller')->defaultTrue()->end()
                ->booleanNode('enable_transliterator')->defaultTrue()->end()
                ->booleanNode('enable_identifier_validator')->defaultTrue()->end()
                ->booleanNode('enable_phone_formatter')->defaultTrue()->end()
                ->booleanNode('enable_name_parser')->defaultTrue()->end()
                ->booleanNode('enable_russian_date_formatter')->defaultTrue()->end()
                ->booleanNode('enable_text_helper')->defaultTrue()->end()
                ->booleanNode('enable_twig_plural')->defaultTrue()->end()
                ->booleanNode('enable_twig_numeral')->defaultTrue()->end()
                ->booleanNode('enable_twig_name')->defaultTrue()->end()
                ->booleanNode('enable_twig_declension')->defaultTrue()->end()
                ->booleanNode('enable_twig_time')->defaultTrue()->end()
                ->booleanNode('enable_twig_utility')->defaultTrue()->end()
                ->booleanNode('enable_twig_validation')->defaultTrue()->end()
            ->end();

        return $treeBuilder;
    }
}
