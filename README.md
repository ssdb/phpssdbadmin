phpssdbadmin
============

SSDB Admin Tool Built with PHP.

## Install

Edit `app/config/config.conf`:

	'ssdb' => array(
		'host' => '127.0.0.1',
		'port' => '8888',
	),

Change `host` and `port` to the right values.

Then edit your Nginx configuration, add one URL rewrite rule as:

	location /phpssdbadmin {
		try_files $uri $uri/ /phpssdbadmin/index.php?$args;
	}

### php.ini configuration

__`short_open_tag` MUST be open!__

Edit php.ini, find `short_open_tag`, if there is none, add the line below, if any, modify it to

	short_open_tag = On

Attention, do not put any ';' mark or any other charcters before it!


## Screeshots

![](./imgs/phpssdbadmin-index.png)

![](./imgs/phpssdbadmin-hash.png)

