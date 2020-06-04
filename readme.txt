可以按照以下步骤来部署和运行程序:
1.请确保机器wangxuemin@mac已经安装了Yaf框架, 并且已经加载入PHP;
2.把yaf_skeleton目录Copy到Webserver的DocumentRoot目录下;
3.需要在php.ini里面启用如下配置，生产的代码才能正确运行：
	yaf.environ="product"
4.重启Webserver;
5.访问http://yourhost/yaf_skeleton/,出现Hellow Word!, 表示运行成功,否则请查看php错误日志;
6.nginx配置
    # yaf
	server {
		listen 80;
		server_name www.yaf.com yaf.com;
		root /Users/wangxuemin/PhpstormProjects/yaf/;
		index index.php index.html index.htm;
        access_log  /Users/wangxuemin/PhpstormProjects/yaf/log/nginx/access.log;
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
