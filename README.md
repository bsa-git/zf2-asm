# ZF2-ASM 

A simple application that implements the site for reports and real-time data display 
developed based on `Zend framework 2`.
The documentation on the Zend framework can be found on the website 
[Zend-Learn](http://framework.zend.com/learn/).
Examples of the application installation are for `OS "Windows"` and web server `Nginx`.

#### Main features of the application:

- expands with configuration files located in the `config/`
- uses a database type SqlLite `data/db/system.db`
- to view the video, use the jQuery plugin [Divbox](https://code.google.com/archive/p/divbox/)
- use JavaScript library [Highstock](https://www.highcharts.com/products/highstock/) to display graphs
- also added plug-ins for working with arrays, strings, and others `module/Application/src/Application/Service`


## Installing

### Prerequisites

- [PHP](http://php.net) version >= 5.3
- [Apache2](https://httpd.apache.org/download.cgi), [Nginx](http://nginx.org/en/) web server or similar
- [Composer](https://getcomposer.org/)

### Deploying

1. Clone [zf2-asm](https://github.com/bsa-git/zf2-asm) project with git.
2. Run `composer install`.
3. Configure the Web server so that the entry point was `public/index.php`.
4. Set, if necessary, the appropriate permissions to write to `path/to/project/var`.
5. Access your project url with web browser.

