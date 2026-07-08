<?php

namespace AlexIvanou\RussianTextBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class AlexIvanouRussianTextExtension extends Extension
{
    private static $flagToServices = array(
        'enable_pluralizer' => array('alexivanou.russian_text.pluralizer'),
        'enable_number_speller' => array('alexivanou.russian_text.number_speller'),
        'enable_money_speller' => array('alexivanou.russian_text.money_speller'),
        'enable_name_inflector' => array('alexivanou.russian_text.name_inflector'),
        'enable_noun_decliner' => array('alexivanou.russian_text.noun_decliner'),
        'enable_adjective_decliner' => array('alexivanou.russian_text.adjective_decliner'),
        'enable_geo_inflector' => array('alexivanou.russian_text.geo_inflector'),
        'enable_time_speller' => array('alexivanou.russian_text.time_speller'),
        'enable_transliterator' => array('alexivanou.russian_text.transliterator'),
        'enable_identifier_validator' => array(
            'alexivanou.russian_text.identifier_validator',
            'alexivanou.russian_text.validator.inn',
            'alexivanou.russian_text.validator.kpp',
            'alexivanou.russian_text.validator.ogrn',
            'alexivanou.russian_text.validator.snils',
            'alexivanou.russian_text.validator.okpo',
            'alexivanou.russian_text.validator.bik',
            'alexivanou.russian_text.validator.unp',
            'alexivanou.russian_text.validator.personal_number',
            'alexivanou.russian_text.validator.passport_rf',
            'alexivanou.russian_text.validator.passport_rb',
            'alexivanou.russian_text.validator.credit_card',
            'alexivanou.russian_text.validator.swift',
            'alexivanou.russian_text.validator.iban',
        ),
        'enable_phone_formatter' => array('alexivanou.russian_text.phone_formatter'),
        'enable_name_parser' => array('alexivanou.russian_text.name_parser'),
        'enable_russian_date_formatter' => array('alexivanou.russian_text.russian_date_formatter'),
        'enable_text_helper' => array('alexivanou.russian_text.text_helper'),
        'enable_twig_plural' => array('alexivanou.russian_text.twig.plural'),
        'enable_twig_numeral' => array('alexivanou.russian_text.twig.numeral'),
        'enable_twig_name' => array('alexivanou.russian_text.twig.name'),
        'enable_twig_declension' => array('alexivanou.russian_text.twig.declension'),
        'enable_twig_time' => array('alexivanou.russian_text.twig.time'),
        'enable_twig_utility' => array('alexivanou.russian_text.twig.utility'),
        'enable_twig_validation' => array('alexivanou.russian_text.twig.validation'),
    );

    private static $aliasToService = array(
        'AlexIvanou\\RussianTextBundle\\Service\\PluralizerInterface' => 'alexivanou.russian_text.pluralizer',
        'AlexIvanou\\RussianTextBundle\\Service\\NumberSpellerInterface' => 'alexivanou.russian_text.number_speller',
        'AlexIvanou\\RussianTextBundle\\Service\\MoneySpellerInterface' => 'alexivanou.russian_text.money_speller',
        'AlexIvanou\\RussianTextBundle\\Service\\NameInflectorInterface' => 'alexivanou.russian_text.name_inflector',
        'AlexIvanou\\RussianTextBundle\\Service\\NounDeclinerInterface' => 'alexivanou.russian_text.noun_decliner',
        'AlexIvanou\\RussianTextBundle\\Service\\TimeSpellerInterface' => 'alexivanou.russian_text.time_speller',
        'AlexIvanou\\RussianTextBundle\\Service\\TransliteratorInterface' => 'alexivanou.russian_text.transliterator',
        'AlexIvanou\\RussianTextBundle\\Service\\IdentifierValidatorInterface' => 'alexivanou.russian_text.identifier_validator',
        'AlexIvanou\\RussianTextBundle\\Service\\PhoneFormatterInterface' => 'alexivanou.russian_text.phone_formatter',
        'AlexIvanou\\RussianTextBundle\\Service\\NameParserInterface' => 'alexivanou.russian_text.name_parser',
        'AlexIvanou\\RussianTextBundle\\Service\\RussianDateFormatterInterface' => 'alexivanou.russian_text.russian_date_formatter',
        'AlexIvanou\\RussianTextBundle\\Service\\TextHelperInterface' => 'alexivanou.russian_text.text_helper',
    );

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $disabledServices = array();

        foreach (self::$flagToServices as $flag => $serviceIds) {
            if (!$config[$flag]) {
                foreach ($serviceIds as $id) {
                    $disabledServices[$id] = true;
                }
            }
        }

        if ($container->hasDefinition('alexivanou.russian_text.twig.declension')) {
            $def = $container->findDefinition('alexivanou.russian_text.twig.declension');
            $args = array();

            if (!isset($disabledServices['alexivanou.russian_text.noun_decliner'])
                && $container->hasDefinition('alexivanou.russian_text.noun_decliner')) {
                $args[] = new Reference('alexivanou.russian_text.noun_decliner');
            } else {
                $args[] = null;
            }

            if (!isset($disabledServices['alexivanou.russian_text.adjective_decliner'])
                && $container->hasDefinition('alexivanou.russian_text.adjective_decliner')) {
                $args[] = new Reference('alexivanou.russian_text.adjective_decliner');
            } else {
                $args[] = null;
            }

            if (!isset($disabledServices['alexivanou.russian_text.geo_inflector'])
                && $container->hasDefinition('alexivanou.russian_text.geo_inflector')) {
                $args[] = new Reference('alexivanou.russian_text.geo_inflector');
            } else {
                $args[] = null;
            }

            $def->setArguments($args);
        }

        foreach ($disabledServices as $id => $_) {
            if ($container->hasDefinition($id)) {
                $container->removeDefinition($id);
            }
        }

        foreach (self::$aliasToService as $alias => $serviceId) {
            if (isset($disabledServices[$serviceId]) && $container->hasAlias($alias)) {
                $container->removeAlias($alias);
            }
        }
    }

    public function getAlias(): string
    {
        return 'russian_text';
    }
}
