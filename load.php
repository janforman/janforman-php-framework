<?php
require './init.php';
$n = filterinput(trim($_GET['n']));
$file = filterinput(trim($_GET['file']));
$do = filterinput(trim($_GET['do']));

if (in_array($n,$enabled_modules)) {
    if (!isallowed() and !in_array($n,$public_modules)) {
        // not allowed
        header('Location: '.domain.'/?notice=auth');
        exit();
    }
    if ($file == '') {
        $file = 'index';
    }
    if (preg_match("#../#", $file)) {
        log_error('Security Alert');
    } else {
        $modpath = "./modules/$n/$file.php";
        if (file_exists("./modules/$n/lang/".language.'.php')) {
            include "./modules/$n/lang/".language.'.php';
        }
        if (file_exists($modpath)) {
            include $modpath;
        } else {
            header('Location: '.domain.'/?notice=auth');
            exit();
        }
    }
} else {
    header('Location: '.domain.'/?notice=auth');
}
