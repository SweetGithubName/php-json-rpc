# Prequisites

This example shows how you might create a web API, where clients communicate
with a remote API server over HTTP.

In order to run the example, you'll need support for the URL "http://php-json-rpc/".
If you're using Linux in your development environment, you can set this up
by editing two files:

## File 1:

`sudo gedit /etc/hosts`
```
127.0.0.1   localhost php-json-rpc
```

## File 2:

`sudo gedit /etc/apache2/sites-available/php-json-rpc.conf`
```
 <VirtualHost *:80>
	ServerName php-json-rpc
	ServerAdmin webmaster@localhost

	# CHANGE THIS PATH #
	DocumentRoot /var/www/datto/php-json-rpc/examples/Http

	# CHANGE THIS PATH #
	<Directory /var/www/datto/php-json-rpc/examples/Http>
		Options Indexes FollowSymLinks MultiViews
		AllowOverride All
		Require all granted
	</Directory>

	ErrorLog ${APACHE_LOG_DIR}/error.log
	CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

`sudo ln -s /etc/apache2/sites-available/api.conf /etc/apache2/sites-enabled/api.conf`

`sudo service apache2 restart`
