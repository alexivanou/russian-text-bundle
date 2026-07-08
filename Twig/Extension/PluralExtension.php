<?php

namespace AlexIvanou\RussianTextBundle\Twig\Extension;

use AlexIvanou\RussianTextBundle\Service\PluralizerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class PluralExtension extends AbstractExtension
{
    private $pluralizer;

    public function __construct(PluralizerInterface $pluralizer)
    {
        $this->pluralizer = $pluralizer;
    }

    public function getFilters(): array
    {
        return array(
            new TwigFilter('pluralize', array($this, 'pluralizeFilter')),
        );
    }

    public function pluralizeFilter($word, $count = 2, $animateness = false)
    {
        return $this->pluralizer->pluralize($word, $count, $animateness);
    }
}
