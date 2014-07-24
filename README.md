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

## Screeshots

![](./imgs/phpssdbadmin-index.png)

![](./imgs/phpssdbadmin-hash.png)

