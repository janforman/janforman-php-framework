<?php
$GLOBALS['cache'] = 0;
require './init.php';
template_start('Index', '');
template('index', '');
template_end();
