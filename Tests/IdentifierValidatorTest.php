<?php

namespace AlexIvanou\RussianTextBundle\Tests;

use AlexIvanou\RussianTextBundle\Service\IdentifierValidator;
use AlexIvanou\RussianTextBundle\Service\Validator\InnValidator;
use AlexIvanou\RussianTextBundle\Service\Validator\KppValidator;
use AlexIvanou\RussianTextBundle\Service\Validator\OgrnValidator;
use AlexIvanou\RussianTextBundle\Service\Validator\SnilsValidator;
use AlexIvanou\RussianTextBundle\Service\Validator\OkpoValidator;
use AlexIvanou\RussianTextBundle\Service\Validator\BikValidator;
use AlexIvanou\RussianTextBundle\Service\Validator\UnpValidator;
use AlexIvanou\RussianTextBundle\Service\Validator\PersonalNumberValidator;
use AlexIvanou\RussianTextBundle\Service\Validator\PassportRfValidator;
use AlexIvanou\RussianTextBundle\Service\Validator\PassportRbValidator;
use AlexIvanou\RussianTextBundle\Service\Validator\CreditCardValidator;
use AlexIvanou\RussianTextBundle\Service\Validator\SwiftValidator;
use AlexIvanou\RussianTextBundle\Service\Validator\IbanValidator;
use PHPUnit\Framework\TestCase;

class IdentifierValidatorTest extends TestCase
{
    private $validator;

    protected function setUp(): void
    {
        $this->validator = new IdentifierValidator();
        $this->validator->addValidator(new InnValidator());
        $this->validator->addValidator(new KppValidator());
        $this->validator->addValidator(new OgrnValidator());
        $this->validator->addValidator(new SnilsValidator());
        $this->validator->addValidator(new OkpoValidator());
        $this->validator->addValidator(new BikValidator());
        $this->validator->addValidator(new UnpValidator());
        $this->validator->addValidator(new PersonalNumberValidator());
        $this->validator->addValidator(new PassportRfValidator());
        $this->validator->addValidator(new PassportRbValidator());
        $this->validator->addValidator(new CreditCardValidator());
        $this->validator->addValidator(new SwiftValidator());
        $this->validator->addValidator(new IbanValidator());
    }

    public function testSwiftValid8chars()
    {
        $this->assertTrue($this->validator->swift()->isValid('DEUTDEFF'));
    }

    public function testSwiftValid11chars()
    {
        $this->assertTrue($this->validator->swift()->isValid('DEUTDEFF500'));
    }

    public function testSwiftValidDefaultBranch()
    {
        $this->assertTrue($this->validator->swift()->isValid('SABRRUMMXXX'));
    }

    public function testSwiftInvalidTooShort()
    {
        $this->assertFalse($this->validator->swift()->isValid('DEUTF'));
    }

    public function testSwiftInvalidDigits()
    {
        $this->assertFalse($this->validator->swift()->isValid('12345678'));
    }

    public function testSwiftInvalidChars()
    {
        $this->assertFalse($this->validator->swift()->isValid('DEUT@EFF'));
    }

    public function testSwiftLowercase()
    {
        $this->assertTrue($this->validator->swift()->isValid('deutdeff'));
    }

    public function testIbanValid()
    {
        $this->assertTrue($this->validator->iban()->isValid('DE44500105175407324931'));
    }

    public function testIbanValidWithSpaces()
    {
        $this->assertTrue($this->validator->iban()->isValid('DE44 5001 0517 5407 3249 31'));
    }

    public function testIbanValidRussian()
    {
        $this->assertTrue($this->validator->iban()->isValid('RU0904452560000000000001234567890'));
    }

    public function testIbanInvalidCheckDigits()
    {
        $this->assertFalse($this->validator->iban()->isValid('DE00500105175407324931'));
    }

    public function testIbanInvalidTooShort()
    {
        $this->assertFalse($this->validator->iban()->isValid('DE44'));
    }

    public function testIbanInvalidTooLong()
    {
        $this->assertFalse($this->validator->iban()->isValid(str_repeat('A', 35)));
    }

    public function testIbanInvalidChars()
    {
        $this->assertFalse($this->validator->iban()->isValid('DE44 5001 0517 5407 @@@@ 31'));
    }

    public function testInnValid10()
    {
        $this->assertTrue($this->validator->inn()->isValid('7707083893'));
    }

    public function testInnValid12()
    {
        $this->assertTrue($this->validator->inn()->isValid('500100732259'));
    }

    public function testInnInvalid()
    {
        $this->assertFalse($this->validator->inn()->isValid('1234567890'));
    }

    public function testKppValid()
    {
        $this->assertTrue($this->validator->kpp()->isValid('770801001'));
    }

    public function testKppInvalid()
    {
        $this->assertFalse($this->validator->kpp()->isValid('1234'));
    }

    public function testOgrnValid13()
    {
        $this->assertTrue($this->validator->ogrn()->isValid('1027700132195'));
    }

    public function testOgrnInvalid()
    {
        $this->assertFalse($this->validator->ogrn()->isValid('1234567890123'));
    }

    public function testSnilsValid()
    {
        $this->assertTrue($this->validator->snils()->isValid('08765430300'));
    }

    public function testSnilsInvalid()
    {
        $this->assertFalse($this->validator->snils()->isValid('12345678901'));
    }

    public function testCreditCardValid()
    {
        $this->assertTrue($this->validator->creditCard()->isValid('4111111111111111'));
    }

    public function testCreditCardInvalid()
    {
        $this->assertFalse($this->validator->creditCard()->isValid('1234567890123456'));
    }

    public function testBikValid()
    {
        $this->assertTrue($this->validator->bik()->isValid('044525225'));
    }

    public function testOkpoValid()
    {
        $this->assertTrue($this->validator->okpo()->isValid('33123456'));
    }

    public function testUnpValid()
    {
        $this->assertTrue($this->validator->unp()->isValid('123456786'));
    }

    public function testPassportRfValid()
    {
        $this->assertTrue($this->validator->passportRF()->isValid('4510123456'));
    }

    public function testPassportRbValid()
    {
        $this->assertTrue($this->validator->passportRB()->isValid('MP1234567'));
    }

    public function testPersonalNumberValid()
    {
        $this->assertTrue($this->validator->personalNumber()->isValid('ABCD1234567890'));
    }

    public function testIbanBelarusValid()
    {
        $this->assertTrue($this->validator->iban()->isValid('BY13NBRB3600900000002Z00AB00'));
    }

    public function testIbanKazakhstanValid()
    {
        $this->assertTrue($this->validator->iban()->isValid('KZ86125KZT5004100100'));
    }
}
