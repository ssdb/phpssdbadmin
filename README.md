phpssdbadmin
============

## 安装

编辑 `app/config/config.php`:

	'ssdb' => array(
		'host' => '127.0.0.1',
		'port' => '8888',
	),

将 `host` 和 `port` 修改成正确的值.

然后编辑你的 Nginx 配置文件, 加入一条 URL 重写规则:

	location /phpssdbadmin {
		try_files $uri $uri/ /phpssdbadmin/index.php?$args;
	}

__注意: 如果你的 nginx.conf 没有配置 `index index.php;`, 请加上.__

如果你使用的是 Apache 的话, 你可以试试这条 URL 重写规则.

	<IfModule mod_rewrite.c>
	RewriteEngine On
	RewriteBase /phpssdbadmin/
	RewriteCond %{REQUEST_FILENAME} !-f 
	RewriteCond %{REQUEST_FILENAME} !-d 
	RewriteRule . /phpssdbadmin/index.php [L] 
	</IfModule>



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

__Your nginx.conf must set `index index.php`.__

If you are using Apache, try this URL rewrite rule:

	<IfModule mod_rewrite.c>
	RewriteEngine On
	RewriteBase /phpssdbadmin/
	RewriteCond %{REQUEST_FILENAME} !-f 
	RewriteCond %{REQUEST_FILENAME} !-d 
	RewriteRule . /phpssdbadmin/index.php [L] 
	</IfModule>



## Screeshots

![](./imgs/phpssdbadmin-index.png)

![](./imgs/phpssdbadmin-hash.png)

