vagrant-web
============

This project works as a front-end for [Vagrant](http://www.vagrantup.com/). It is focused to be used with the plugin [Vagrant-Node](https://github.com/fjsanpedro/vagrant-node). 

Through this web app you could be able to manage remote nodes running [Vagrant](http://www.vagrantup.com/). Some of the functionallity that this web apps provides are:

* Run/Stop/Suspend/Resume virtual machines
* Manage snapshots in each virtual machines
* Upload/Delete boxes
* Add/Delete new virtual machines to each node
* Manage node config files

Beacuse it is still in a beta stage, this web app doesn't provides a RBAC user access. There is only a user with login name `admin` and password `catedrasaes`.

The requirements to install this web app are the following:

* PHPv5
* php5-curl
* php5-sqlite

Special thanks to the following authors for their useful plugins:

* [groupgridview](http://www.yiiframework.com/extension/groupgridview/) developed by  [Vitalets](http://www.yiiframework.com/user/56359/)
* [restclient](http://www.yiiframework.com/extension/restclient/) developed by [dinhtrung](http://www.yiiframework.com/user/26195/)
* [geshi-highlighter](http://www.yiiframework.com/extension/geshi-highlighter/) developed by [Francis.TM](http://www.yiiframework.com/user/4808/)


This plugin has been developed in the context of the [Catedra SAES](http://www.catedrasaes.org) of the University of Murcia(Spain).

##Installation
Just clone the repository under yout web server document root and configure your web server to add this site.

##Usage
Access to the web with the login name `admin` and password `catedrasaes` and enjoy.



