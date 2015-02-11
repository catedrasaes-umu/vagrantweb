vagrant-web
============

This project works as a front-end for [Vagrant](http://www.vagrantup.com/). It is focused to be used with the plugin [Vagrant-Node](https://github.com/fjsanpedro/vagrant-node)(minimun version 1.1.3). 

Through this web app you could be able to manage remote nodes running [Vagrant](http://www.vagrantup.com/). Some of the functionallity that this web apps provides are:

* Run/Stop/Suspend/Resume virtual machines
* Manage snapshots in each virtual machines
* Upload/Delete boxes
* Add/Delete new virtual machines to each node
* Manage node config files

In [VagrantWeb Overview](https://www.youtube.com/watch?v=PslrNMAl_vU) you can watch an global overview of all the functionality.

The requirements to install this web app are the following:

* PHPv5
* MySql
* Curl
* php5-curl
* php5-mysql

Checkout the [Installation Video Tutorial](https://www.youtube.com/watch?v=FzZ3gkv7Rdo)

Special thanks to the following authors for their useful plugins:

* [groupgridview](http://www.yiiframework.com/extension/groupgridview/) developed by  [Vitalets](http://www.yiiframework.com/user/56359/)
* [restclient](http://www.yiiframework.com/extension/restclient/) developed by [dinhtrung](http://www.yiiframework.com/user/26195/)
* [geshi-highlighter](http://www.yiiframework.com/extension/geshi-highlighter/) developed by [Francis.TM](http://www.yiiframework.
com/user/4808/)
* [CSVExport Extension](http://www.yiiframework.com/extension/csvexport/) developed by [nsbucky](http://www.yiiframework.com/user/5409/)


This plugin has been developed in the context of the [Catedra SAES](http://www.catedrasaes.org) of the University of Murcia(Spain).

##Installation
Just clone the repository under yout web server document root and configure your web server to add this site.

* chgrp -R www-data /var/www/html/vagrantweb/
* chmod -R g+w /var/www/html/vagrantweb/assets/
* chmod -R g+w /var/www/html/vagrantweb/protected/runtime/
* chmod -R g+w /var/www/html/vagrantweb/protected/config/



##Usage

* Access to the web (http://127.0.0.1/vagrantweb) with the login name `admin` and password `catedrasae$`
* Go to button `Create Node` in order to add your first node
* The node must be running [Vagrant](http://www.vagrantup.com/) and the plugin [Vagrant-Node](https://github.com/fjsanpedro/vagrant-node) (minimun version 1.1.3)
* Fill in the data, including the node password in order to login properly.
* Enjoy



