<?php

namespace AlexIvanou\RussianTextBundle\Twig\Extension;

use AlexIvanou\RussianTextBundle\Service\IdentifierValidatorInterface;
use AlexIvanou\RussianTextBundle\Service\PhoneFormatterInterface;
use AlexIvanou\RussianTextBundle\Service\NameParserInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class ValidationExtension extends AbstractExtension
{
    private $identifierValidator;
    private $phoneFormatter;
    private $nameParser;

    public function __construct(
        IdentifierValidatorInterface $identifierValidator = null,
        PhoneFormatterInterface $phoneFormatter = null,
        NameParserInterface $nameParser = null
    ) {
        $this->identifierValidator = $identifierValidator;
        $this->phoneFormatter = $phoneFormatter;
        $this->nameParser = $nameParser;
    }

    public function getFilters()
    {
        $filters = array();

        if ($this->phoneFormatter !== null) {
            $filters[] = new TwigFilter('phone_format', array($this, 'phoneFormatFilter'));
        }

        return $filters;
    }

    public function getFunctions()
    {
        $functions = array();

        if ($this->phoneFormatter !== null) {
            $functions[] = new TwigFunction('phone_is_valid', array($this, 'phoneIsValidFunction'));
            $functions[] = new TwigFunction('phone_detect_country', array($this, 'phoneDetectCountryFunction'));
        }

        if ($this->identifierValidator !== null) {
            $functions[] = new TwigFunction('is_valid_inn', array($this, 'isValidInnFunction'));
            $functions[] = new TwigFunction('is_valid_snils', array($this, 'isValidSnilsFunction'));
            $functions[] = new TwigFunction('is_valid_ogrn', array($this, 'isValidOgrnFunction'));
            $functions[] = new TwigFunction('is_valid_kpp', array($this, 'isValidKppFunction'));
            $functions[] = new TwigFunction('is_valid_credit_card', array($this, 'isValidCreditCardFunction'));
            $functions[] = new TwigFunction('is_valid_okpo', array($this, 'isValidOkpoFunction'));
            $functions[] = new TwigFunction('is_valid_unp', array($this, 'isValidUnpFunction'));
            $functions[] = new TwigFunction('is_valid_swift', array($this, 'isValidSwiftFunction'));
            $functions[] = new TwigFunction('is_valid_iban', array($this, 'isValidIbanFunction'));
        }

        if ($this->nameParser !== null) {
            $functions[] = new TwigFunction('parse_name', array($this, 'parseNameFunction'));
            $functions[] = new TwigFunction('name_initials', array($this, 'nameInitialsFunction'));
        }

        return $functions;
    }

    public function phoneFormatFilter($phone, $countryCode = null)
    {
        return $this->phoneFormatter->format($phone, $countryCode);
    }

    public function phoneIsValidFunction($phone, $countryCode = null)
    {
        return $this->phoneFormatter->isValid($phone, $countryCode);
    }

    public function phoneDetectCountryFunction($phone)
    {
        return $this->phoneFormatter->detectCountry($phone);
    }

    public function isValidInnFunction($value)
    {
        return $this->identifierValidator->inn()->isValid($value);
    }

    public function isValidSnilsFunction($value)
    {
        return $this->identifierValidator->snils()->isValid($value);
    }

    public function isValidOgrnFunction($value)
    {
        return $this->identifierValidator->ogrn()->isValid($value);
    }

    public function isValidKppFunction($value)
    {
        return $this->identifierValidator->kpp()->isValid($value);
    }

    public function isValidCreditCardFunction($value)
    {
        return $this->identifierValidator->creditCard()->isValid($value);
    }

    public function isValidOkpoFunction($value)
    {
        return $this->identifierValidator->okpo()->isValid($value);
    }

    public function isValidUnpFunction($value)
    {
        return $this->identifierValidator->unp()->isValid($value);
    }

    public function isValidSwiftFunction($value)
    {
        return $this->identifierValidator->swift()->isValid($value);
    }

    public function isValidIbanFunction($value)
    {
        return $this->identifierValidator->iban()->isValid($value);
    }

    public function parseNameFunction($fullName)
    {
        return $this->nameParser->parse($fullName);
    }

    public function nameInitialsFunction($fullName, $style = 'after')
    {
        return $this->nameParser->initials($fullName, $style);
    }
}
