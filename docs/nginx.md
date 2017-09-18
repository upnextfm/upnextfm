The following is a base nginx configuration to run the site in dev mode. It assumes the domain dev.upnext.fm resolves to 127.0.0.1.

```
server {
        listen 80;
        server_name dev.upnext.fm;
        root /var/www/upnext.fm/web;

        set $app app_dev.php;
        index $app;

        error_log /var/log/nginx/dev.upnext.fm-error.log;
        access_log /var/log/nginx/dev.upnext.fm-access.log;

        location / {
                try_files $uri $uri/ /$app?$query_string;
        }

        location ~ \.php$ {
                fastcgi_split_path_info ^(.+\.php)(/.+)$;
                fastcgi_pass unix:/var/run/php/php7.0-fpm.sock;
                fastcgi_index $app;
                include fastcgi_params;
        }
}
```
