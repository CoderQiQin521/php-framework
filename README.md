使用此框架搭建的演示站,打开浏览器访问: http://phpframework.makebasis.com/

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

欢迎star
