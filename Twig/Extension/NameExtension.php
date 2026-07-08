<?php

namespace AlexIvanou\RussianTextBundle\Twig\Extension;

use AlexIvanou\RussianTextBundle\Service\NameInflectorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class NameExtension extends AbstractExtension
{
    private $nameInflector;

    public function __construct(NameInflectorInterface $nameInflector)
    {
        $this->nameInflector = $nameInflector;
    }

    public function getFilters(): array
    {
        return array(
            new TwigFilter('inflect_name', array($this, 'inflectNameFilter')),
        );
    }

    public function getFunctions(): array
    {
        return array(
            new TwigFunction('name_cases', array($this, 'nameCasesFunction')),
            new TwigFunction('detect_gender', array($this, 'detectGenderFunction')),
        );
    }

    public function inflectNameFilter($fullName, $case, $gender = null)
    {
        return $this->nameInflector->inflect($fullName, $case, $gender);
    }

    public function nameCasesFunction($fullName, $gender = null)
    {
        return $this->nameInflector->getCases($fullName, $gender);
    }

    public function detectGenderFunction($fullName)
    {
        return $this->nameInflector->detectGender($fullName);
    }
}
