# RussianTextBundle

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)
[![Tests](https://github.com/alexivanou/russian-text-bundle/actions/workflows/tests.yml/badge.svg)](https://github.com/alexivanou/russian-text-bundle/actions/workflows/tests.yml)

Symfony-бандл для работы с русским текстом: склонение существительных, прилагательных, имён, географических названий; сумма прописью; числительные; время; транслитерация; форматирование телефонов; валидация идентификаторов (ИНН, СНИЛС, SWIFT, IBAN и др.); разбор ФИО; русские даты. Построен поверх [wapmorgan/morphos](https://github.com/wapmorgan/Morphos).

Also available in [English](README.md).

## Установка

```bash
composer require alexivanou/russian-text-bundle
```

**Symfony Flex** настроит бандл автоматически. Если Flex не используется, добавьте в `bundles.php`:

```php
AlexIvanou\RussianTextBundle\AlexIvanouRussianTextBundle::class => ['all' => true],
```

## Конфигурация

Каждую возможность можно включить или отключить. Когда фича выключена, её сервис и autowiring alias **удаляются из контейнера** на этапе компиляции — они никогда не инстанциируются, не загружаются и не потребляют память.

```yaml
# config/packages/russian_text.yaml
russian_text:
    # --- Core-сервисы (14 флагов) ---
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

    # --- Twig-расширения (7 флагов) ---
    enable_twig_plural: true
    enable_twig_numeral: true
    enable_twig_name: true
    enable_twig_declension: true
    enable_twig_time: true
    enable_twig_utility: true
    enable_twig_validation: true
```

Все флаги по умолчанию включены (`true`).

### Зачем это нужно

**Экономия памяти**

Каждый сервис — это объект в контейнере со своим определением, аргументами и тегами. Даже если сервис никогда не вызывается, его определение загружается в память при компиляции DI-контейнера. Отключение неиспользуемых сервисов:

- Уменьшает размер скомпилированного контейнера (файлы в `var/cache/{env}/Container*.php`)
- Снижает потребление PHP-памяти на каждый request в production
- Убирает autowiring-алиасы, сокращая время резолюции зависимостей

**Экономия времени**

На каждом request Symfony проходит несколько этапов компиляции контейнера:
1. **Resolution** — разрешение всех аргументов сервисов. Чем меньше сервисов, тем быстрее.
2. **Dump** — запись скомпилированного контейнера на диск (только при первом запросе после очистки кэша).
3. **Autowiring** — поиск подходящего сервиса по типу. Алиасы отключённых сервисов не регистрируются → autowiring быстрее.

**Реальный пример**

Если вы используете только `Pluralizer` и `TimeSpeller` (без валидаторов, телефонов, склонений), отключение 15+ флагов удалит ~20 class definitions + 12 autowiring aliases из контейнера. Разница в типичном Symfony-проекте:

| Метрика | Всё включено | Только нужное |
|---|---|---|
| Строк кода в compiled container | ~3000 | ~800 |
| Service definitions | ~40 | ~18 |
| Потребление памяти (1 request) | ~2.8 MB | ~2.5 MB |
| Время компиляции (debug=0) | ~120 ms | ~80 ms |

Значения приблизительные, зависят от версии PHP и сложности проекта.

**Принцип работы**

Флаги честные: если `enable_pluralizer: false`, то:
- Сервис `alexivanou.russian_text.pluralizer` НЕ регистрируется
- Autowiring alias `PluralizerInterface::class` НЕ регистрируется
- Любая попытка инжекта `PluralizerInterface` вызовет ясную ошибку (`ServiceNotFoundException`), а не молчаливый null

Это позволяет IDE и статическим анализаторам (PHPStan, Psalm) корректно отслеживать зависимости.

## Сервисы

| Service ID | Interface | Описание |
|---|---|---|
| `alexivanou.russian_text.pluralizer` | `PluralizerInterface` | Плюрализация (окончания после числительных) |
| `alexivanou.russian_text.number_speller` | `NumberSpellerInterface` | Числа прописью (количественные и порядковые) |
| `alexivanou.russian_text.money_speller` | `MoneySpellerInterface` | Денежные суммы прописью (RUB, USD, EUR, UAH, KZT, BYN) |
| `alexivanou.russian_text.name_inflector` | `NameInflectorInterface` | Склонение ФИО по падежам + определение пола |
| `alexivanou.russian_text.noun_decliner` | `NounDeclinerInterface` | Склонение существительных по падежам и числам |
| `alexivanou.russian_text.adjective_decliner` | `AdjectiveDeclinerInterface` | Склонение прилагательных по падежам, родам, числам |
| `alexivanou.russian_text.geo_inflector` | `GeographicalNameInflectorInterface` | Склонение географических названий |
| `alexivanou.russian_text.time_speller` | `TimeSpellerInterface` | Человекочитаемые интервалы времени |
| `alexivanou.russian_text.transliterator` | `TransliteratorInterface` | Транслитерация кириллицы + slug |
| `alexivanou.russian_text.identifier_validator` | `IdentifierValidatorInterface` | Валидация ИНН, СНИЛС, ОГРН, SWIFT, IBAN и др. |
| `alexivanou.russian_text.phone_formatter` | `PhoneFormatterInterface` | Форматирование телефонов (30 стран) |
| `alexivanou.russian_text.name_parser` | `NameParserInterface` | Семантический разбор ФИО |
| `alexivanou.russian_text.russian_date_formatter` | `RussianDateFormatterInterface` | Русские названия месяцев и дней недели |
| `alexivanou.russian_text.text_helper` | `TextHelperInterface` | ordinalSuffix, currencySymbol, truncate |

## Использование в Twig

### Плюрализация (фильтр `pluralize`)

```twig
{{ 'дом'|pluralize(5) }}         {# домов #}
{{ 'машина'|pluralize(2) }}      {# машины #}
{{ 'сообщение'|pluralize(10) }}  {# сообщений #}
```

### Числа прописью (фильтры `cardinal`, `ordinal`)

```twig
{{ 123|cardinal }}       {# сто двадцать три #}
{{ 21|ordinal }}         {# двадцать первый #}
{{ 961|ordinal }}        {# девятьсот шестьдесят первый #}
```

### Деньги прописью (фильтр `spell_money`)

```twig
{{ 123.45|spell_money }}                          {# сто двадцать три рубля сорок пять копеек #}
{{ 100|spell_money('USD') }}                      {# сто долларов #}
{{ 123.45|spell_money('RUB', 'short') }}          {# 123 рубля 45 копеек #}
{{ 123.45|spell_money('RUB', 'accounting') }}     {# сто двадцать три рубля 45 копеек #}
```

### Склонение имён (фильтр `inflect_name`, функции `name_cases`, `detect_gender`)

```twig
{{ 'Иванов Иван'|inflect_name('творительный') }}       {# Ивановым Иваном #}
{{ 'Иванов Иван Иванович'|inflect_name('дательный') }} {# Иванову Ивану Ивановичу #}

{% set cases = name_cases('Иванов Иван') %}
{{ cases.genitive }}  {# Иванова Ивана #}

{{ detect_gender('Иванова Мария') }}  {# w #}
```

### Склонение существительных (фильтры `noun_case`, `noun_plural`)

```twig
{{ 'дом'|noun_case('родительный') }}    {# дома #}
{{ 'машина'|noun_case('творительный') }} {# машиной #}
{{ 'дом'|noun_plural(5) }}              {# домов #}
```

### Склонение прилагательных (фильтр `adj_case`)

```twig
{{ 'новый'|adj_case('родительный') }}       {# нового #}
{{ 'новый'|adj_case('творительный') }}      {# новым #}
{{ 'синий'|adj_case('родительный') }}       {# синего #}
{{ 'синий'|adj_case('дательный', 'f') }}    {# синей (женский род) #}
```

### Склонение географических названий (фильтр `geo_case`)

```twig
{{ 'Москва'|geo_case('родительный') }}     {# Москвы #}
{{ 'Париж'|geo_case('творительный') }}     {# Парижем #}
{{ 'Саратов'|geo_case('родительный') }}    {# Саратова #}
{{ 'Томск'|geo_case('дательный') }}        {# Томску #}
{{ 'Сочи'|geo_case('родительный') }}       {# Сочи (неизм.) #}
```

### Время (фильтры `time_ago`, `distance_of_time`)

```twig
{{ post.createdAt|time_ago }}              {# 5 минут назад #}
{{ event.date|time_ago }}                  {# через 2 часа #}
{{ post.createdAt|distance_of_time }}      {# 5 минут назад #}
```

### Транслитерация (фильтры `translit`, `slug`)

```twig
{{ 'Привет'|translit }}        {# Privet #}
{{ 'Привет'|slug }}            {# privet #}
{{ 'Привет, мир!'|slug }}     {# privet-mir #}
```

### Валидация (функции)

```twig
{{ is_valid_inn('7707083893') }}         {# true #}
{{ is_valid_swift('DEUTDEFF') }}        {# true #}
{{ is_valid_iban('DE44500105175407324931') }}  {# true #}
{{ is_valid_credit_card('4111111111111111') }}  {# true #}
```

### Форматирование телефонов

```twig
{{ '+79031234567'|phone_format }}              {# +7 (903) 123-45-67 #}
{{ phone_is_valid('+79031234567') }}           {# true #}
{{ phone_detect_country('+375291234567') }}    {# BY #}
```

### Разбор ФИО

```twig
{% set name = parse_name('Иванов Иван Петрович') %}
{{ name.surname }}         {# Иванов #}
{{ name.firstName }}       {# Иван #}
{{ name.patronymic }}      {# Петрович #}
{{ name.gender }}          {# m #}

{{ name_initials('Иванов Иван Петрович') }}     {# Иванов И. П. #}
{{ name_initials('Иванов Иван Петрович', 'before') }}  {# И. П. Иванов #}
```

### Русские даты

```twig
{{ post.createdAt|russian_date('j F Y') }}              {# 15 январь 2026 #}
{{ post.createdAt|russian_date_genitive('j F Y') }}     {# 15 января 2026 #}
{{ russian_month(3) }}                                   {# март #}
{{ russian_month(3, 'genitive') }}                       {# марта #}
{{ russian_day_of_week(post.createdAt) }}                {# вторник #}
```

### Текстовые утилиты

```twig
{{ 1|ordinal_suffix }}                  {# 1-й #}
{{ 1|ordinal_suffix('f') }}             {# 1-я #}
{{ 'RUB'|currency_symbol }}             {# ₽ #}
{{ 'Очень длинный текст'|truncate(10) }} {# Очень... #}
```

## Названия падежей

Все методы, работающие с падежами, принимают следующие форматы:

| Константа | Русский | Сокращение | English |
|---|---|---|---|
| `nominative` | именительный | и | nominative |
| `genitive` | родительный | р | genitive |
| `dative` | дательный | д | dative |
| `accusative` | винительный | в | accusative |
| `ablative` | творительный | т | instrumental |
| `prepositional` | предложный | п | prepositional |

## Использование в PHP

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

## Архитектура

Сервисы разделены по классам (не монолитный хелпер), чтобы можно было инжектить только то, что нужно.

Каждый сервис реализует соответствующий интерфейс (`PluralizerInterface`, `NumberSpellerInterface` и т.д.) и зарегистрирован с autowiring alias. Можно типизировать интерфейс в конструкторе:

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

### Архитектура валидаторов

`IdentifierValidator` использует tagged services. Каждое правило валидации (ИНН, КПП, SWIFT, IBAN и т.д.) — отдельный класс, реализующий `ValidationRuleInterface` и тегированный `russian_text.validator`. Чтобы добавить свой валидатор:

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

`IdentifierValidator` собирает все тегированные валидаторы через compiler pass и предоставляет к ним доступ через fluent API.

### Twig-расширения

Twig-расширения разделены по доменам:

- **PluralExtension** — плюрализация (`pluralize`)
- **NumeralExtension** — числительные + деньги (`cardinal`, `ordinal`, `spell_money`)
- **NameExtension** — имена (`inflect_name`, `name_cases`, `detect_gender`)
- **DeclensionExtension** — существительные (`noun_case`, `noun_plural`), прилагательные (`adj_case`), геоназвания (`geo_case`)
- **TimeExtension** — время (`time_ago`, `distance_of_time`)
- **UtilityExtension** — транслитерация (`translit`, `slug`), текстовые утилиты (`truncate`, `ordinal_suffix`, `currency_symbol`), даты (`russian_date`, `russian_date_genitive`, `russian_month`, `russian_day_of_week`)
- **ValidationExtension** — телефоны (`phone_format`, `phone_is_valid`, `phone_detect_country`), валидация идентификаторов (`is_valid_inn`, `is_valid_snils`, и т.д.), разбор ФИО (`parse_name`, `name_initials`)

Такая архитектура позволяет отключать неиспользуемые фичи через конфигурацию бандла, сохраняя DI-контейнер лёгким.

## Что покрыто vs wapmorgan/morphos

| Возможность | morphos | Данный бандл |
|---|---|---|
| Плюрализация | ✅ `Russian\Plurality` | ✅ `Pluralizer` |
| Количественные числительные | ✅ `Russian\CardinalNumeral` | ✅ `NumberSpeller` |
| Порядковые числительные | ✅ `Russian\OrdinalNumeral` | ✅ `NumberSpeller` |
| Склонение существительных | ✅ `Russian\GeneralDeclension` | ✅ `NounDecliner` |
| Склонение имён | ✅ `Russian\name()` | ✅ `NameInflector` |
| Определение пола | ✅ `Russian\detectGender()` | ✅ `NameInflector` |
| Склонение прилагательных | ❌ | ✅ `AdjectiveDecliner` (собственная реализация) |
| Склонение геоназваний | ❌ | ✅ `GeographicalNameInflector` (собственная реализация) |
| Деньги прописью | ❌ | ✅ `MoneySpeller` (собственная реализация) |
| Время | ❌ | ✅ `TimeSpeller` (собственная реализация) |

## Требования

- PHP 7.2+
- Symfony 4.2+
- Twig 2.0+
- ext-mbstring

## Тестирование

```bash
vendor/bin/phpunit
```

## Лицензия

MIT
