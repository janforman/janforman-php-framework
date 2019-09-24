<?php
$GLOBALS['cache'] = 0;
require './init.php';
template_start('Index', '');
template('index.tpl', '');
template_end();
