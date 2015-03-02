# Prequisites

This example shows how you might create a web API, where clients communicate
with a remote API server over HTTP.

In order to run the example, you'll need support for the URL "http://api/".
You can set this up by editing two files:

## File 1:

`sudo gedit /etc/hosts`
```
127.0.0.1   localhost api
```

## File 2:

`sudo gedit /etc/apache2/sites-available`
```
 <VirtualHost *:80>
	ServerName api
	ServerAdmin webmaster@localhost

	# CHANGE THIS PATH #
	DocumentRoot /home/username/Projects/datto-api/datto-api/examples/Http

	# CHANGE THIS PATH #
	<Directory /home/username/Projects/datto-api/datto-api/examples/Http>
		Options Indexes FollowSymLinks MultiViews
		AllowOverride All
		Require all granted
	</Directory>

	ErrorLog ${APACHE_LOG_DIR}/error.log
	CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

`sudo ln -s /etc/apache2/sites-available /etc/apache2/sites-enabled`

`sudo service apache2 restart`