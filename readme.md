Source code of zlml.cz [2.0]
====================================

[![Build Status](https://travis-ci.org/mrtnzlml/zlml.cz.svg?branch=master)](https://travis-ci.org/mrtnzlml/zlml.cz)

It needs at least PHP 7.0. The lower version of PHP is not supported, sorry - move on.

Installing
----------
The best way to install this project is to clone this repository and follow file `.travis.yml`.

Make directories `temp`, `log`, `www/dist`, `www/chunks` and `www/uploads` writable.
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

Deployment
----------

	ansible-playbook ansible/deploy-production.yml
	ansible-playbook ansible/deploy-production.yml --list-hosts
	ansible all -m ping

    ssh ec2-user@34.195.224.88 -i ansible/LightsailDefaultPrivateKey.pem

Encrypting multiple files for Fravis-CI
---------------------------------------
https://docs.travis-ci.com/user/encrypting-files/#Encrypting-multiple-files

    tar cvf secrets.tar config/config.local.neon.production ansible/LightsailDefaultPrivateKey.pem
    travis encrypt-file secrets.tar
