<?php
require './config.php';
mb_internal_encoding('UTF-8');
$GLOBALS['mysqli'] = mysqli_connect($mariadb['ip'], $mariadb['name'], $mariadb['pass'], $mariadb['db']);
if (!$GLOBALS['mysqli']) {
    log_error('mariadb disconnected');
}
