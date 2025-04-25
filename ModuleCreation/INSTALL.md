# Installation Serveur HTTP

## Apache

Le logiciel libre [Apache HTTP Server](https://httpd.apache.org/) (**Apache**) est un serveur HTTP créé et maintenu au sein de la fondation Apache. C’est l’un des serveurs HTTP les plus populaires du World Wide Web.

La version 2 d’Apache propose entre autres le support de plusieurs plates-formes (Windows, Linux et UNIX, Solaris, BSD, MAC OS X), le support de processus légers UNIX  (threads), une nouvelle API et le support IPv6.

Apache est conçu pour prendre en charge de nombreux modules lui donnant des fonctionnalités supplémentaires : interprétation du langage Perl, PHP, Python et Ruby, serveur proxy, _Common Gateway Interface_, Server Side Includes, **réécriture d’URL**, négociation de contenu, protocoles de communication additionnels, etc ...

### Solution n°1 : paquet Ubuntu

```sh
$ sudo apt install apache2

$ apache2 -v

$ sudo systemctl status apache2.service -l --no-pager
```

- Installation de l'application PHP

```sh
$ sudo ln -s ./ModuleCreation/www /var/www/html/ergonomic-workstation
```

- Configuration de l'application PHP

```sh
$ sudo vim /etc/apache2/apache2.conf
```

En phase de développement, on peut appliquer la configuration suivante :

```conf
<Directory /var/www/html/ergonomic-workstation/>
        AllowOverride All
        Require all granted
</Directory>

...

<FilesMatch "^\.ht">
        #Require all denied
        Require all granted
</FilesMatch>
```

L'application PHP utile une reécriture de l'URL à partir du fichier [.htaccess](./www/.htaccess) :

```txt
Options +FollowSymLinks
RewriteEngine on
RewriteRule ^([a-zA-Z]*)/?([a-zA-Z]*)?/?([a-zA-Z0-9]*)?/?$ index.php?controleur=$1&action=$2&id=$3 [NC,L]
```

Il faut activer le module de reécriture :

```sh
$ sudo a2enmod rewrite
$ sudo systemctl restart apache2.service
```

Dans le fichier [config.php](./www/config.php):

```php
...
// URL
define("ROOT_PATH", "/");
define("URL_PATH", "http://" . $_SERVER['HTTP_HOST'] . "/ergonomic-workstation/");
...
```

### Solution n°2 : serveur intégré

```sh
$ php -S localhost:8000 -t ModuleCreation/www/ &
```

> Le serveur web intégré à PHP ne dispose de module de récriture d'URL !

Dans le fichier [config.php](./www/config.php):

```php
...
// URL
define("ROOT_PATH", "/");
define("URL_PATH", "http://" . $_SERVER['HTTP_HOST'] . "/");
...
```

---
&copy; 2024-2025 LaSalle Avignon
