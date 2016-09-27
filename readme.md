Source code of zlml.cz [1.3.1]
====================================

Master branch [![Build Status](https://travis-ci.org/mrtnzlml/zlml.cz.svg?branch=master)](https://travis-ci.org/mrtnzlml/zlml.cz) and Develop branch [![Build Status](https://travis-ci.org/mrtnzlml/zlml.cz.svg?branch=develop)](https://travis-ci.org/mrtnzlml/zlml.cz)

It's tested against at least PHP 5.4.29. The lower version of PHP is not supported, sorry.

What's on the background
------------------------
- [Nette Framework](http://nette.org/en/) - a popular tool for PHP web development
- [Nette Tester](http://tester.nette.org/en/) – enjoyable unit testing
- [Latte](http://latte.nette.org/en/) - amazing template engine for PHP
- [Texy](http://texy.info/en/) - is sexy
- [FSHL](http://fshl.kukulich.cz/) - free, open source, universal and very fast syntax highlighter
- [Webloader](https://github.com/janmarek/WebLoader) - component for smart CSS and JS files loading
- [Doctrine 2](http://www.doctrine-project.org/) - library primarily focused on database storage and object mapping (using [Kdyby](https://github.com/Kdyby/Doctrine))
- [Kdyby\Events](https://github.com/Kdyby/Events) - robust events system for Nette Framework
- [Nextras\SecuredLinks](https://github.com/nextras/secured-links) - for better CSRF protection
- and much more...

Installing
----------
The best way to install this project is to download latest package with dependencies using Composer:

1. Install Composer: (see http://getcomposer.org/)
2. Use Composer:

		composer create-project mrtnzlml/zlml.cz --stability=dev

3. Install Bower: (see http://bower.io/)
4. Go to the project folder and install client side dependencies:

		bower install

It's like Composer for front-end.

Make directories `temp`, `log`, `www/webtemp`, `www/chunks` and `www/uploads` writable.
Navigate your browser to the `www` directory and you will see a welcome page.
PHP 5.4 allows you run `php -S localhost:8888 -t www` to start the webserver and
then visit `http://localhost:8888` in your browser.
Port must be set according to the local computer settings.

It is CRITICAL that file `app/config/config.neon` & whole `app`, `log`
and `temp` directory are NOT accessible directly via a web browser! If you
don't protect this directory from direct web access, anybody will be able to see
your sensitive data. See [security warning](http://nette.org/security-warning).

Then you have to create database for this website. You can use Adminer tool in
`http://localhost:8888/adminer` or you can do that manually using command line:

		mysql -u root -e 'create database zlml;'
        mysql -u root -D zlml < sql/zlml.sql
        
There is also automatic (beta) installer. Just install this project using Composer and open
it in your favourite browser. I am still working on it...

Amazing administration
-----------------------
![Administration](www/img/screens/admin.png)

Awesome print experience
------------------------
![Print view](www/img/screens/print.png)

Incredible articles
-------------------
![Incredible article](www/img/screens/article.png)
