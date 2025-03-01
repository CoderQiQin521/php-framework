使用此框架搭建的演示站,打开浏览器访问: http://phpframework.makebasis.com/

Open the browser to access the demo station built with this framework: http://phpframework.makebasis.com/

## Database support
The framework supports databases using PHP_POD as the underlying driver. It can connect to MySQL, MSSQL, Oracle, SQLite, PostgreSQL, and Sybase, and also supports configuring multiple database connections.

## Controller
The framework adopts object-oriented programming (OOP) to access and call member functions, making it easy to create actions to handle transactions, all running under a namespace.

## Template parsing
The framework comes with a built-in HTML template parsing engine to meet the needs of program development.

## 📦 Install
```apacheconf
git clone https://github.com/CoderQiQin521/php-framework.git
```
```apacheconf
composer install
```

## ⌨️ 部署
- 运行目录修改为public
- 修改config/database
- 配置伪静态, 参考以下

apache
```
<IfModule mod_rewrite.c>
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ index.php [L,E=PATH_INFO:$1]
</IfModule>
```

nginx
```apacheconf
location / {  
	try_files $uri $uri/ /index.php$is_args$query_string;  
}  
```
![img](http://phpframework.makebasis.com/img/jb_beam.png)


Thank you to JetBrains for supporting open-source projects.
