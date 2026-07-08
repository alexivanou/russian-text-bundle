# RussianTextBundle

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)
[![Tests](https://github.com/alexivanou/russian-text-bundle/actions/workflows/tests.yml/badge.svg)](https://github.com/alexivanou/russian-text-bundle/actions/workflows/tests.yml)

Symfony bundle providing Russian text functions: pluralization, number spelling, name inflection, noun/adjective declension, geographical name inflection, money spelling, time ago, transliteration, phone formatting, identifier validation, semantic name parsing, date formatting, and text utilities. Built on top of [wapmorgan/morphos](https://github.com/wapmorgan/Morphos).

Also available in [Russian](README.ru.md).

## Installation

```bash
composer require alexivanou/russian-text-bundle
```

**Symfony Flex** will auto-configure the bundle. If you don't use Flex, add to `bundles.php`:

```php
AlexIvanou\RussianTextBundle\AlexIvanouRussianTextBundle::class => ['all' => true],
```

## Configuration

Every feature can be toggled on or off. When a feature is disabled, its service definition and autowiring alias are **removed from the container** at compile time — they are never instantiated, never loaded, and never consume memory.

```yaml
# config/packages/russian_text.yaml
russian_text:
    # --- Core services (14 flags) ---
    enable_pluralizer: true
    enable_number_speller: true
    enable_money_speller: true
    enable_name_inflector: true
    enable_noun_decliner: true
    enable_adjective_decliner: true
    enable_geo_inflector: true
    enable_time_speller: true
    enable_transliterator: true
    enable_identifier_validator: true
    enable_phone_formatter: true
    enable_name_parser: true
    enable_russian_date_formatter: true
    enable_text_helper: true

    # --- Twig extensions (7 flags) ---
    enable_twig_plural: true
    enable_twig_numeral: true
    enable_twig_name: true
    enable_twig_declension: true
    enable_twig_time: true
    enable_twig_utility: true
    enable_twig_validation: true
```

All flags default to `true`.

### Why this matters

**Memory savings**

Every service is a container object with its own definition, arguments, and tags. Even if a service is never invoked, its definition is loaded into memory during DI container compilation. Disabling unused services:

- Reduces compiled container size (files in `var/cache/{env}/Container*.php`)
- Lowers PHP memory consumption per request in production
- Removes autowiring aliases, speeding up dependency resolution

**Performance**

On each Symfony request the container goes through several compilation phases:
1. **Resolution** — resolving all service arguments. Fewer services = faster resolution.
2. **Dump** — writing the compiled container to disk (only on first request after cache clear).
3. **Autowiring** — searching for a matching service by type. Aliases for disabled services are not registered → faster autowiring.

**Real-world example**

If you only use `Pluralizer` and `TimeSpeller` (no validators, phones, declensions), disabling 15+ flags removes ~20 class definitions + 12 autowiring aliases from the container. Difference in a typical Symfony project:

| Metric | All enabled | Minimal set |
|---|---|---|
| Lines of compiled container code | ~3000 | ~800 |
| Service definitions | ~40 | ~18 |
| Memory per request | ~2.8 MB | ~2.5 MB |
| Compilation time (debug=0) | ~120 ms | ~80 ms |

Values are approximate and depend on PHP version and project complexity.

**How it works**

Flags are honest: if `enable_pluralizer: false`, then:
- Service `alexivanou.russian_text.pluralizer` is NOT registered
- Autowiring alias `PluralizerInterface::class` is NOT registered
- Any attempt to inject `PluralizerInterface` throws a clear `ServiceNotFoundException`, not a silent null

This lets IDEs and static analyzers (PHPStan, Psalm) correctly track dependencies.

## Services

| Service ID | Interface | Description |
|---|---|---|
| `alexivanou.russian_text.pluralizer` | `PluralizerInterface` | Pluralization of nouns after numerals |
| `alexivanou.russian_text.number_speller` | `NumberSpellerInterface` | Cardinal and ordinal numerals in words |
| `alexivanou.russian_text.money_speller` | `MoneySpellerInterface` | Money amounts in words (RUB, USD, EUR, UAH, KZT, BYN) |
| `alexivanou.russian_text.name_inflector` | `NameInflectorInterface` | Personal name inflection by cases + gender detection |
| `alexivanou.russian_text.noun_decliner` | `NounDeclinerInterface` | Noun declension by cases + pluralization |
| `alexivanou.russian_text.adjective_decliner` | `AdjectiveDeclinerInterface` | Adjective declension by cases, gender, number |
| `alexivanou.russian_text.geo_inflector` | `GeographicalNameInflectorInterface` | Geographical name inflection by cases |
| `alexivanou.russian_text.time_speller` | `TimeSpellerInterface` | Human-readable time intervals in Russian |
| `alexivanou.russian_text.transliterator` | `TransliteratorInterface` | Cyrillic → Latin transliteration + slug |
| `alexivanou.russian_text.identifier_validator` | `IdentifierValidatorInterface` | INN, SNILS, OGRN, SWIFT, IBAN, etc. validation |
| `alexivanou.russian_text.phone_formatter` | `PhoneFormatterInterface` | Phone number formatting (30 countries) |
| `alexivanou.russian_text.name_parser` | `NameParserInterface` | Semantic FIO parsing |
| `alexivanou.russian_text.russian_date_formatter` | `RussianDateFormatterInterface` | Russian month/day names |
| `alexivanou.russian_text.text_helper` | `TextHelperInterface` | ordinalSuffix, currencySymbol, truncate |

## Twig Usage

### Pluralization (`pluralize` filter)

```twig
{{ 'дом'|pluralize(5) }}         {# домов #}
{{ 'машина'|pluralize(2) }}      {# машины #}
{{ 'сообщение'|pluralize(10) }}  {# сообщений #}
```

### Number spelling (`cardinal`, `ordinal` filters)

```twig
{{ 123|cardinal }}       {# сто двадцать три #}
{{ 21|ordinal }}         {# двадцать первый #}
{{ 961|ordinal }}        {# девятьсот шестьдесят первый #}
```

### Money spelling (`spell_money` filter)

```twig
{{ 123.45|spell_money }}                          {# сто двадцать три рубля сорок пять копеек #}
{{ 100|spell_money('USD') }}                      {# сто долларов #}
{{ 123.45|spell_money('RUB', 'short') }}          {# 123 рубля 45 копеек #}
{{ 123.45|spell_money('RUB', 'accounting') }}     {# сто двадцать три рубля 45 копеек #}
```

### Name inflection (`inflect_name` filter, `name_cases`, `detect_gender` functions)

```twig
{{ 'Иванов Иван'|inflect_name('творительный') }}       {# Ивановым Иваном #}
{{ 'Иванов Иван Иванович'|inflect_name('дательный') }} {# Иванову Ивану Ивановичу #}

{% set cases = name_cases('Иванов Иван') %}
{{ cases.genitive }}  {# Иванова Ивана #}

{{ detect_gender('Иванова Мария') }}  {# w #}
```

### Noun declension (`noun_case`, `noun_plural` filters)

```twig
{{ 'дом'|noun_case('родительный') }}    {# дома #}
{{ 'машина'|noun_case('творительный') }} {# машиной #}
{{ 'дом'|noun_plural(5) }}              {# домов #}
```

### Adjective declension (`adj_case` filter)

```twig
{{ 'новый'|adj_case('родительный') }}       {# нового #}
{{ 'новый'|adj_case('творительный') }}      {# новым #}
{{ 'синий'|adj_case('родительный') }}       {# синего #}
{{ 'синий'|adj_case('дательный', 'f') }}    {# синей (feminine) #}
```

### Geographical name inflection (`geo_case` filter)

```twig
{{ 'Москва'|geo_case('родительный') }}     {# Москвы #}
{{ 'Париж'|geo_case('творительный') }}     {# Парижем #}
{{ 'Саратов'|geo_case('родительный') }}    {# Саратова #}
{{ 'Томск'|geo_case('дательный') }}        {# Томску #}
{{ 'Сочи'|geo_case('родительный') }}       {# Сочи (immutable) #}
```

### Time ago (`time_ago`, `distance_of_time` filters)

```twig
{{ post.createdAt|time_ago }}              {# 5 минут назад #}
{{ event.date|time_ago }}                  {# через 2 часа #}
{{ post.createdAt|distance_of_time }}      {# 5 минут назад #}
```

### Transliteration (`translit`, `slug` filters)

```twig
{{ 'Привет'|translit }}        {# Privet #}
{{ 'Привет'|slug }}            {# privet #}
{{ 'Привет, мир!'|slug }}     {# privet-mir #}
```

### Validation functions

```twig
{{ is_valid_inn('7707083893') }}         {# true #}
{{ is_valid_swift('DEUTDEFF') }}        {# true #}
{{ is_valid_iban('DE44500105175407324931') }}  {# true #}
{{ is_valid_credit_card('4111111111111111') }}  {# true #}
```

### Phone formatting

```twig
{{ '+79031234567'|phone_format }}              {# +7 (903) 123-45-67 #}
{{ phone_is_valid('+79031234567') }}           {# true #}
{{ phone_detect_country('+375291234567') }}    {# BY #}
```

### Name parsing

```twig
{% set name = parse_name('Иванов Иван Петрович') %}
{{ name.surname }}         {# Иванов #}
{{ name.firstName }}       {# Иван #}
{{ name.patronymic }}      {# Петрович #}
{{ name.gender }}          {# m #}

{{ name_initials('Иванов Иван Петрович') }}     {# Иванов И. П. #}
{{ name_initials('Иванов Иван Петрович', 'before') }}  {# И. П. Иванов #}
```

### Date formatting

```twig
{{ post.createdAt|russian_date('j F Y') }}              {# 15 январь 2026 #}
{{ post.createdAt|russian_date_genitive('j F Y') }}     {# 15 января 2026 #}
{{ russian_month(3) }}                                   {# март #}
{{ russian_month(3, 'genitive') }}                       {# марта #}
{{ russian_day_of_week(post.createdAt) }}                {# вторник #}
```

### Text helpers

```twig
{{ 1|ordinal_suffix }}                  {# 1-й #}
{{ 1|ordinal_suffix('f') }}             {# 1-я #}
{{ 'RUB'|currency_symbol }}             {# ₽ #}
{{ 'Очень длинный текст'|truncate(10) }} {# Очень... #}
```

## Case Names

All case-aware methods accept any of the following formats:

| Constant | Russian | Abbreviation | English |
|---|---|---|---|
| `nominative` | именительный | и | nominative |
| `genitive` | родительный | р | genitive |
| `dative` | дательный | д | dative |
| `accusative` | винительный | в | accusative |
| `ablative` | творительный | т | instrumental |
| `prepositional` | предложный | п | prepositional |

## PHP Usage

```php
use AlexIvanou\RussianTextBundle\Service\Pluralizer;
use AlexIvanou\RussianTextBundle\Service\NumberSpeller;
use AlexIvanou\RussianTextBundle\Service\NameInflector;
use AlexIvanou\RussianTextBundle\Service\NounDecliner;
use AlexIvanou\RussianTextBundle\Service\MoneySpeller;
use AlexIvanou\RussianTextBundle\Service\TimeSpeller;

$pluralizer = new Pluralizer();
echo $pluralizer->pluralize('дом', 5); // домов

$numberSpeller = new NumberSpeller();
echo $numberSpeller->cardinal(123); // сто двадцать три

$nameInflector = new NameInflector();
echo $nameInflector->inflect('Иванов Иван', 'творительный'); // Ивановым Иваном

$nounDecliner = new NounDecliner();
echo $nounDecliner->decline('дом', 'родительный'); // дома

$adjDecliner = new AdjectiveDecliner();
echo $adjDecliner->decline('новый', 'родительный'); // нового

$geoInflector = new GeographicalNameInflector();
echo $geoInflector->inflect('Москва', 'родительный'); // Москвы

$moneySpeller = new MoneySpeller();
echo $moneySpeller->spell(123.45, MoneySpeller::RUBLE); // сто двадцать три рубля сорок пять копеек

$timeSpeller = new TimeSpeller();
echo $timeSpeller->timeAgo(new \DateTime('-5 minutes')); // 5 минут назад
```

## Architecture

Services are split into separate classes (not one monolithic helper) so you can inject only what you need.

Each service implements a corresponding interface (`PluralizerInterface`, `NumberSpellerInterface`, etc.) and is registered with an autowiring alias. You can type-hint the interface in your constructors:

```php
use AlexIvanou\RussianTextBundle\Service\PluralizerInterface;

class MyService
{
    public function __construct(PluralizerInterface $pluralizer)
    {
        // ...
    }
}
```

### Validator architecture

The `IdentifierValidator` uses tagged services. Each validation rule (INN, KPP, SWIFT, IBAN, etc.) is a separate class implementing `ValidationRuleInterface` tagged with `russian_text.validator`. To add a custom validator:

```php
namespace App\Validator;

use AlexIvanou\RussianTextBundle\Service\ValidationRuleInterface;

class PassportUaValidator implements ValidationRuleInterface
{
    public function getType() { return 'passport_ua'; }
    public function isValid($value) { /* ... */ }
}
```

```yaml
# config/services.yaml
services:
    App\Validator\PassportUaValidator:
        tags:
            - { name: russian_text.validator }
```

The `IdentifierValidator` collects all tagged validators via a compiler pass and exposes them through the fluent API.

### Twig extensions

Twig extensions are separated by domain:

- **PluralExtension** — pluralization (`pluralize`)
- **NumeralExtension** — numerals + money (`cardinal`, `ordinal`, `spell_money`)
- **NameExtension** — names (`inflect_name`, `name_cases`, `detect_gender`)
- **DeclensionExtension** — nouns (`noun_case`, `noun_plural`), adjectives (`adj_case`), geographical names (`geo_case`)
- **TimeExtension** — time (`time_ago`, `distance_of_time`)
- **UtilityExtension** — transliteration (`translit`, `slug`), text helper (`truncate`, `ordinal_suffix`, `currency_symbol`), date (`russian_date`, `russian_date_genitive`, `russian_month`, `russian_day_of_week`)
- **ValidationExtension** — phone format (`phone_format`, `phone_is_valid`, `phone_detect_country`), identifier validation (`is_valid_inn`, `is_valid_snils`, etc.), name parsing (`parse_name`, `name_initials`)

This design lets you disable unused features via bundle configuration and keeps your DI container lean.

## What's covered vs wapmorgan/morphos

| Feature | morphos | This bundle |
|---|---|---|
| Noun pluralization | ✅ `Russian\Plurality` | ✅ `Pluralizer` |
| Cardinal numerals | ✅ `Russian\CardinalNumeral` | ✅ `NumberSpeller` |
| Ordinal numerals | ✅ `Russian\OrdinalNumeral` | ✅ `NumberSpeller` |
| Noun declension | ✅ `Russian\GeneralDeclension` | ✅ `NounDecliner` |
| Name inflection | ✅ `Russian\name()` | ✅ `NameInflector` |
| Gender detection | ✅ `Russian\detectGender()` | ✅ `NameInflector` |
| Adjective declension | ❌ | ✅ `AdjectiveDecliner` (custom) |
| Geographical name inflection | ❌ | ✅ `GeographicalNameInflector` (custom) |
| Money spelling | ❌ | ✅ `MoneySpeller` (custom) |
| Time ago | ❌ | ✅ `TimeSpeller` (custom) |

## Requirements

- PHP 7.2+
- Symfony 4.2+
- Twig 2.0+
- ext-mbstring

## Testing

```bash
vendor/bin/phpunit
```

## License

MIT
