#!/bin/bash

config_class_path="/var/www/html/relogin2/classes/Config.class.php"


if [[ ! -e "$config_class_path" ]]; then
  touch "$config_class_path"
  chown www-data:www-data "$config_class_path"
fi

exec php-fpm
