<?php

namespace AlexIvanou\RussianTextBundle\Twig\Extension;

use AlexIvanou\RussianTextBundle\Service\NounDeclinerInterface;
use AlexIvanou\RussianTextBundle\Service\AdjectiveDeclinerInterface;
use AlexIvanou\RussianTextBundle\Service\GeographicalNameInflectorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DeclensionExtension extends AbstractExtension
{
    private $nounDecliner;
    private $adjectiveDecliner;
    private $geoInflector;

    public function __construct(
        NounDeclinerInterface $nounDecliner = null,
        AdjectiveDeclinerInterface $adjectiveDecliner = null,
        GeographicalNameInflectorInterface $geoInflector = null
    ) {
        $this->nounDecliner = $nounDecliner;
        $this->adjectiveDecliner = $adjectiveDecliner;
        $this->geoInflector = $geoInflector;
    }

    public function getFilters(): array
    {
        $filters = array();

        if ($this->nounDecliner !== null) {
            $filters[] = new TwigFilter('noun_case', array($this, 'nounCaseFilter'));
            $filters[] = new TwigFilter('noun_plural', array($this, 'nounPluralFilter'));
        }

        if ($this->adjectiveDecliner !== null) {
            $filters[] = new TwigFilter('adj_case', array($this, 'adjectiveCaseFilter'));
        }

        if ($this->geoInflector !== null) {
            $filters[] = new TwigFilter('geo_case', array($this, 'geoCaseFilter'));
        }

        return $filters;
    }

    public function nounCaseFilter($word, $case, $animateness = false)
    {
        return $this->nounDecliner->decline($word, $case, $animateness);
    }

    public function nounPluralFilter($word, $count = 2, $animateness = false)
    {
        return $this->nounDecliner->pluralize($word, $count, $animateness);
    }

    public function adjectiveCaseFilter($adjective, $case, $gender = 'm', $animateness = false)
    {
        return $this->adjectiveDecliner->decline($adjective, $case, $gender, $animateness);
    }

    public function geoCaseFilter($name, $case)
    {
        return $this->geoInflector->inflect($name, $case);
    }
}
