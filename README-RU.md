# ZF2-ASM

Простое приложение реализующее сайт для отчетов и отображения данных в реальном времени, разработанное на базе `Zend framework 2`.
С документацией по `Zend framework 2` можно познакомиться на сайте [Zend-Learn](http://framework.zend.com/learn/). 
Примеры установки приложения приведены для `ОС "Windows"` и веб сервера `Nginx`.

#### Основные характеристики приложения:
- расширяется с помощью конфигурационных файлов, расположенных в `config/`;
- использует БД типа SqlLite `data/db/system.db`;
- для просмотра видео используется плагин для jQuery [Divbox](https://code.google.com/archive/p/divbox/)
- для отображения графиков используется JavaScript библиотека [Highstock](https://www.highcharts.com/products/highstock/);  
- также добавлены плагины для работы с массивами, строками и др. `module/Application/src/Application/Service`;

## Инсталяция

### Предварительные требования

- [PHP](http://php.net) version >= 5.3
- веб сервер [Apache2](https://httpd.apache.org/download.cgi), [Nginx](http://nginx.org/ru/) или похожие
- [Composer](https://getcomposer.org/)

### Развертывание

1. Клонировать [zf2-asm](https://github.com/bsa-git/zf2-asm) проект с помощью git.
2. Выполнить `composer install`.
3. Сконфигурируйте веб сервер, чтобы точка входа была `public/index.php`.
4. Установите, если необходимо, соответсвующие права на запись в `path/to/project/var`.
5. Введите адрес сайта в броузер (пр. http://zf2-asm.ru/)
