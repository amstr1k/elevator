Elevators
=========

[Demo]

Реализован функционал имитации работы 4-х лифтов в 10-ти этажном здании.

Возможности:

- возможность увидеть расположение всех лифтов в системе;
- возможность увидеть активные заказы лифтов;
- возможность добавить заказ лифта на этаж.


Разворачивание проекта
======================

**Зависимости**

- ubuntu >= 12.04
- php-fpm >= 5.3
- nginx
- mysql >= 5.1

Выполнить команду ```git clone git@github.com:amstr1k/elevator.git ```

Создать конфиг nginx

```
server {

    set $host_path "/путь/до/папки/проекта";

    server_name  elevator.dev;
    root   $host_path;
    set $yii_bootstrap "index.php";

    charset utf-8;

    location / {
        index  index.html $yii_bootstrap;
        try_files $uri $uri/ /$yii_bootstrap?$args;
    }

    location ~ ^/(protected|framework|themes/\w+/views) {
        deny  all;
    }

    location ~ \.(js|css|png|jpg|gif|swf|ico|pdf|mov|fla|zip|rar)$ {
        try_files $uri =404;
    }

    location ~ \.php {
        fastcgi_split_path_info  ^(.+\.php)(.*)$;

        set $fsn /$yii_bootstrap;
        if (-f $document_root$fastcgi_script_name){
            set $fsn $fastcgi_script_name;
        }

        fastcgi_pass   127.0.0.1:9000;
        include fastcgi_params;
        fastcgi_param  SCRIPT_FILENAME  $document_root$fsn;

        fastcgi_param  PATH_INFO        $fastcgi_path_info;
        fastcgi_param  PATH_TRANSLATED  $document_root$fsn;
    }

    location ~ /\. {
        deny all;
        access_log off;
        log_not_found off;
    }
}
```
- отредактировать файл ```/etc/hosts``` добавив запись ```127.0.0.1       elevator.dev```

- создать базу данных **elevator** и сделать дамп схемы(``` mysql dump -uИмя_пользователя -pПароль < путь/до/проекта/protected/data/shema.sql```)

- отредактировать конфигурацию прокта ```путь/до/проекта/protected/config/main.php```

```
'db'=>array(
    'connectionString' => 'mysql:host=localhost;dbname=elevator',
	'emulatePrepare' => true,
	'username' => 'пользователь_БД',
	'password' => 'пароль',
	'charset' => 'utf8',
),
```

- перезапусть nginx и php ``` sudo service nginx restart```
```sudo service php5-fpm restart```


[demo]:http://aslive.info/
