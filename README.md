# yafproject
##### PHP扩展安装及php.ini设置
https://github.com/wang-xuemin/pecl

##### nginx配置
```ssh
server {
    listen 80;
    server_name www.yaf.com yaf.com;
    root /Users/wangxuemin/eclipse-workspace/yafproject/;
    index index.php index.html index.htm;
    access_log  /Users/wangxuemin/nginx/log/yaf/access.log;
    charset utf-8;

    location /status {
        access_log off;
    }

    if (!-e $request_filename) {
        rewrite ^/(.*) /index.php?$1 last;
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    error_page   500 502 503 504  /50x.html;
    location = /50x.html {
        root   html;
    }

    location ~ \.php$ {
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
        include        fastcgi_params;
    }

}
```
