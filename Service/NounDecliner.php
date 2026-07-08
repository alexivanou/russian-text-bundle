<?php

namespace AlexIvanou\RussianTextBundle\Service;

use morphos\Russian\GeneralDeclension;
use morphos\Russian\Plurality;

class NounDecliner implements NounDeclinerInterface
{
    private $declension;
    private $plurality;

    public function __construct()
    {
        $this->declension = new GeneralDeclension();
        $this->plurality = new Plurality();
    }

    public function decline($word, $case, $animateness = false)
    {
        return $this->declension->getCase($word, $case, $animateness);
    }

    public function getCases($word, $animateness = false)
    {
        return $this->declension->getCases($word, $animateness);
    }

    public function pluralize($word, $count = 2, $animateness = false)
    {
        return Plurality::pluralize($word, $count, $animateness);
    }

    public function pluralCases($word, $animateness = false)
    {
        return $this->plurality->getCases($word, $animateness);
    }

    public function pluralCase($word, $case, $animateness = false)
    {
        return $this->plurality->getCase($word, $case, $animateness);
    }

    public function isMutable($word, $animateness = false)
    {
        return $this->declension->isMutable($word, $animateness);
    }
}
