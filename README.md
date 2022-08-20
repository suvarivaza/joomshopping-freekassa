# Данный  плагин для Joomshopping позволяет производить оплату с помощью Freekassa

## Системные требования

* PHP > 7,2
* [Расширение curl](http://php.net/manual/en/book.curl.php)
* [Joomla](http://www.joomla.org/download.html) 3.x (плагин тестировался с версией 3.10.11)
* [JoomShopping](http://joomshopping.pro/download/component.html) 4.x (плагин тестировался с версией 4.18.7)

## <a name="get_package"></a> Получение пакета с плагином

### Самостоятельная сборка установочного пакета плагина
1. [Установите composer](http://getcomposer.org/doc/00-intro.md), если его еще нет
2. Клонируйте репозиторий с плагином: `composer create-project suvarivaza/joomshopping-freekassa --stability=dev --prefer-dist`
3. Перейдите в папку плагина: `cd joomshopping-freekassa`
4. Упакуйте плагин в архив: `composer archive --format=zip`


## Установка, настройка, удаление плагина
* [Установка и настройка плагина](instruction.pdf)


### Так же если вы используете composer на вашем сайте joomla то вы можете добавить плагин в ваш проект так:
composer require suvarivaza/easy-query-builder