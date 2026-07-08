<?php

namespace AlexIvanou\RussianTextBundle\Service;

class IdentifierValidator implements IdentifierValidatorInterface
{
    private $validators = array();
    private $type;

    public function addValidator(ValidationRuleInterface $validator)
    {
        $this->validators[$validator->getType()] = $validator;
    }

    public function inn()
    {
        $this->type = 'inn';
        return $this;
    }

    public function kpp()
    {
        $this->type = 'kpp';
        return $this;
    }

    public function ogrn()
    {
        $this->type = 'ogrn';
        return $this;
    }

    public function snils()
    {
        $this->type = 'snils';
        return $this;
    }

    public function okpo()
    {
        $this->type = 'okpo';
        return $this;
    }

    public function bik()
    {
        $this->type = 'bik';
        return $this;
    }

    public function unp()
    {
        $this->type = 'unp';
        return $this;
    }

    public function personalNumber()
    {
        $this->type = 'personal_number';
        return $this;
    }

    public function passportRF()
    {
        $this->type = 'passport_rf';
        return $this;
    }

    public function passportRB()
    {
        $this->type = 'passport_rb';
        return $this;
    }

    public function creditCard()
    {
        $this->type = 'credit_card';
        return $this;
    }

    public function swift()
    {
        $this->type = 'swift';
        return $this;
    }

    public function iban()
    {
        $this->type = 'iban';
        return $this;
    }

    public function isValid($value)
    {
        $value = preg_replace('/[\s\-_]/', '', $value);

        if ($this->type === null) {
            return false;
        }

        if (isset($this->validators[$this->type])) {
            return $this->validators[$this->type]->isValid($value);
        }

        return false;
    }
}
