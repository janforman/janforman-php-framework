# janforman-php-framework
PHP Framework with template support, languages and modules

This is my some very old work starting at 2001 so it's very ancient, but compatible with PHP7.
It can support templates, compression, cache, modules loading and transparent language support.

# nginx snippet
location / { try_files $uri $uri/ @janforman; }
location @janforman {
        rewrite ^/j-(.*) /load.php?n=$1 last;
}
