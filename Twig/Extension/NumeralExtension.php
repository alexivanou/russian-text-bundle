<?php

namespace AlexIvanou\RussianTextBundle\Twig\Extension;

use AlexIvanou\RussianTextBundle\Service\NumberSpellerInterface;
use AlexIvanou\RussianTextBundle\Service\MoneySpellerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class NumeralExtension extends AbstractExtension
{
    private $numberSpeller;
    private $moneySpeller;

    public function __construct(NumberSpellerInterface $numberSpeller, MoneySpellerInterface $moneySpeller)
    {
        $this->numberSpeller = $numberSpeller;
        $this->moneySpeller = $moneySpeller;
    }

    public function getFilters(): array
    {
        return array(
            new TwigFilter('cardinal', array($this, 'cardinalFilter')),
            new TwigFilter('ordinal', array($this, 'ordinalFilter')),
            new TwigFilter('spell_money', array($this, 'spellMoneyFilter')),
        );
    }

    public function cardinalFilter($number, $gender = null)
    {
        return $this->numberSpeller->cardinal($number, $gender);
    }

    public function ordinalFilter($number, $gender = null)
    {
        return $this->numberSpeller->ordinal($number, $gender);
    }

    public function spellMoneyFilter($value, $currency = null, $format = null)
    {
        return $this->moneySpeller->spell($value, $currency, $format);
    }
}
