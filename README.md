# Обёртка для библиотек работающих с API amoCRM

## Установка

### Через composer:

```bash
$ composer require cl-lab/amo-middleware
```

## Использование

Для использования обёртки пропишите в файле:
use CleverLab\AmoCRM\<ClassName>

## Соглашения

Любая обёртка для библиотеки должна реализовывать интерфейс CleverLab\AmoCRM\Interfaces\iMiddleware

Это будет гарантировать единообразие и наличие всех необходимых методов, что позволит избежать ошшибок
при изменении обёртки в клиентском коде.

При необходимости изменить поведение метода (заменить используемую им библиотеку и т.д.)
не переписывать код имеющихся классов (Унаследовать класс от базового и ереопределить нужные методы).

## Документация в анотация

Каждый метод обёртки, а так же методы интерфейса документированны в формате phpDoc.
Так же в методах бывают указанны ссылки на документацию по оригинальному методу API.

