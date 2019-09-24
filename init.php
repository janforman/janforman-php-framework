<?php
if (stristr(htmlentities($_SERVER ['PHP_SELF']), 'init.php')) {
    exit();
}

require './config.php';
mb_internal_encoding('UTF-8');
$GLOBALS['mysqli'] = mysqli_connect($mariadb['ip'], $mariadb['name'], $mariadb['pass'], $mariadb['db']);
if (!$GLOBALS['mysqli']) {
    log_error('mariadb disconnected');
}
require './legacy.php';

if ('https://'.$_SERVER ['HTTP_HOST'] != domain) {
    header('Location: '.domain.$_SERVER ['REQUEST_URI']);
    exit();
}
if (strpos($_SERVER ['HTTP_ACCEPT_ENCODING'], 'x-gzip') !== false) {
    define('ENCODING', 'x-gzip');
}
if (strpos($_SERVER ['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
    define('ENCODING', 'gzip');
}

// <timedebug>
$starttime = explode(' ', microtime());
$starttime = $starttime [1] + $starttime [0];
// </timedebug>

// <online>
$past = time()- 3600;
sql_query("DELETE FROM p_session WHERE time < $past");
if(username != 'username') {
        $name = username;
} else {
        $name = $_SERVER['REMOTE_ADDR'];
        $guest = 1;
}
$result = sql_query('SELECT time FROM p_session WHERE username='$name'");
if(sql_fetch_array($result)) {
        sql_query("UPDATE p_session SET username='$name', time='" . time(). "', host_addr='" . $_SERVER['REMOTE_ADDR'] . "', local_addr='', guest='$guest' WHERE username='$name'");
} else {
        sql_query("INSERT INTO p_session (username, time, host_addr, local_addr, guest) VALUES ('$name', '" . time(). "', '" . $_SERVER['REMOTE_ADDR'] . "', '', '$guest')");
}
unset($past);
unset($name);
unset($guest);
define('online',sql_num_rows(sql_query("SELECT time FROM p_session")));
// </online>

// <language>
$newlang = $_GET['newlang'];
$lang = $_COOKIE['lang'];
if(isset($newlang)) {
if(file_exists('./lang/' . $newlang . '.lng')) {
$language = $newlang;
setcookie('lang', $language, time()+ 31536000, null, null, null, true);
} else {
setcookie('lang', $language, time()+ 31536000, null, null, null, true);
}
} elseif(isset($lang)) {
if(file_exists('./lang/' . $lang . '.lng')) {
$language = $lang;
} else {
$language = 'english';
}
} else {
$language = 'english';
if(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2)== '') {
$language = 'czech';
}
if(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2)== 'cs') {
$language = 'czech';
}
if(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2)== 'sk') {
$language = 'czech';
}
if(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2)== 'de') {
$language = 'german';
}
setcookie('lang', $language, time()+ 31536000, null, null, null, true);
}
include './lang/' . $language . '.lng';
define('language', $language);
unset($newlang);
unset($lang);
unset($language);
// </language>


// <0min cache>
if ($GLOBALS ['cache'] == '') {
    $GLOBALS ['cache'] = '0';
}
if (ENCODING != 'ENCODING' && file_exists('./cache/'.md5(username.language.$_SERVER ['REQUEST_URI'])) && filectime('./cache/'.md5(username.language.$_SERVER ['REQUEST_URI'])) > time() - $GLOBALS ['cache']) {
    header('Content-Encoding: '.ENCODING);
    readfile('./cache/'.md5(username.language.$_SERVER ['REQUEST_URI']));
    exit();
}
// </0min cache>

//////////////////////////// Functions ////////////////////////////////////////////////////////////////////////////////////////////
// <templates>
function template_start($title, $css)
{
    ob_start();
    ob_implicit_flush(0);
    echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"\n    \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
    echo "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n";
    echo "<head>\n";
    echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\"/>\n";
    echo "<meta http-equiv=\"cache-control\" content=\"no-cache\"/>\n<meta http-equiv=\"pragma\" content=\"no-cache\"/>\n";
    echo "<meta http-equiv=\"Content-Script-Type\" content=\"text/javascript\"/>\n";
    echo "<meta name='viewport' content='width=1000, maximum-scale=1.0, user-scalable=yes'/>\n";
    echo "<meta http-equiv=\"Content-Style-Type\" content=\"text/css\"/>\n";
    echo "<meta name=\"theme-color\" content=\"#3366cc\" /><meta name=\"msapplication-TileColor\" content=\"#3366cc\" />\n";
    echo "<link rel='manifest' href='/manifest.json'>";
    echo '<title>'.$title."</title>\n";
    echo '<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>'."\n";
    echo '<link rel="stylesheet" href="/css/default.css?r=' . version . '" type="text/css"/>'."\n";
    if($css) { echo "<link rel=\"stylesheet\" href=\"/css/$css\" type=\"text/css\"/>\n"; }
    echo '<link rel="stylesheet" href="/css/jquery-ui.css">';
    echo '<link rel="stylesheet" href="/css/fontawesome-5.3.1/all.min.css">';
    echo '<script src="/js/jquery-1.10.2.min.js"></script>';
    echo '<script src="/js/ui/1.10.2/jquery-ui.js"></script>';
    echo "\n</head>\n<body>\n";
}

function template($template, $data)
{
    if (file_exists('./templates/'.$template)) {
        require './templates/'.$template;
    } else {
        log_error('template-'.$template);
    }
}

function template_end()
{
    global $starttime;
    echo "\n</body>\n</html>";
    // <debug>
    $endtime = explode(' ', microtime());
    $endtime = $endtime [1] + $endtime [0];
    $totaltime = round(($endtime - $starttime), 3);
    $memory = round(memory_get_usage() / 1024);
    if ($GLOBALS ['cache'] == '0') {
        echo "\n\n<!-- janforman.com-framework/time:".$totaltime.'s/'.$memory.'kb/cache off/online:'.online." -->\n";
    } else {
        echo "\n\n<!-- janforman.com-framework/time:".$totaltime.'s/'.$memory.'kb/'.ENCODING.'/cached/'.date('H:i').'/expiration:'.$GLOBALS ['cache']."s -->\n";
    }
    // </debug>

    if (ENCODING != 'ENCODING') {
        header('Content-Encoding: '.ENCODING);
        $gzip_size = ob_get_length();
        $gzip_contents = ob_get_clean();
        $gzip_final = "\x1f\x8b\x08\x00\x00\x00\x00\x00".substr(gzcompress($gzip_contents, 6), 0, -4).pack('V', crc32($gzip_contents)).pack('V', $gzip_size);
        echo $gzip_final;
        if ($GLOBALS ['cache'] != '0') {
            $f = fopen('./cache/'.md5(username.language.$_SERVER ['REQUEST_URI']), 'w+');
            fwrite($f, $gzip_final);
            fclose($f);
        }
        mysqli_close();
        exit();
    } else {
        ob_end_flush();
        mysqli_close();
    }
}
// </templates>

function filterinput($string)
{
    preg_match_all("/[\@\\.\s\w\p{L}\p{N}\p{Pd}]/u", $string, $result);

    return implode('', $result[0]);
}

function log_error($reason)
{
    ob_end_clean();
    ob_start();
    if ($reason == '403') {
        header('HTTP/1.1 403 Forbidden');
        echo '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN"><HTML><HEAD><TITLE>403 Forbidden</TITLE></HEAD><BODY><H1>403 Forbidden</H1>You don\'t have permission to access this document on this server.<P><HR><ADDRESS>PHP7/janforman.com Framework at <a href="mailto:'.webmaster.'">'.$_SERVER['SERVER_NAME'].'</ADDRESS></BODY></HTML>';
        ob_end_flush();
        exit;
    } else {
        header('HTTP/1.1 503 Service Unavailable');
        echo '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN"><HTML><HEAD><TITLE>503 Service Unavailable</TITLE></HEAD><BODY><H1>503 Service Temporarily Unavailable</H1>Please come back in a few minutes. Thank you.<BR>[ERROR: '.$reason.']<P><HR><ADDRESS>PHP7/janforman.com Framework at <a href="mailto:'.webmaster.'">'.$_SERVER['SERVER_NAME'].'</ADDRESS></BODY></HTML>';
        ob_end_flush();
        exit;
    }
}

function nice_trim($str_to_count, $max_length)
{
    if (mb_strlen($str_to_count) <= $max_length) {
        return $str_to_count;
    }
    $str_to_count = mb_substr($str_to_count, 0, $max_length - 3);
    $str_to_count .= '...';

    return $str_to_count;
}

function detect_mobile()
{
    if (preg_match('/(android|iphone|ipad|ipod)/i', $_SERVER['HTTP_USER_AGENT'])) {
        return true;
    } else {
        return false;
    }
}

function remove_accents($string)
{
    if (!preg_match('/[\x80-\xff]/', $string)) {
        return $string;
    }
    $chars = array(
    chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
    chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
    chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
    chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
    chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
    chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
    chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
    chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
    chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
    chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
    chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
    chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
    chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
    chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
    chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
    chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
    chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
    chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
    chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
    chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
    chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
    chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
    chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
    chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
    chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
    chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
    chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
    chr(195).chr(191) => 'y',
    chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
    chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
    chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
    chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
    chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
    chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
    chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
    chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
    chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
    chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
    chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
    chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
    chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
    chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
    chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
    chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
    chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
    chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
    chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
    chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
    chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
    chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
    chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
    chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
    chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
    chr(196).chr(178) => 'IJ', chr(196).chr(179) => 'ij',
    chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
    chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
    chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
    chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
    chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
    chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
    chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
    chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
    chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
    chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
    chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
    chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
    chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
    chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
    chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
    chr(197).chr(146) => 'OE', chr(197).chr(147) => 'oe',
    chr(197).chr(148) => 'R', chr(197).chr(149) => 'r',
    chr(197).chr(150) => 'R', chr(197).chr(151) => 'r',
    chr(197).chr(152) => 'R', chr(197).chr(153) => 'r',
    chr(197).chr(154) => 'S', chr(197).chr(155) => 's',
    chr(197).chr(156) => 'S', chr(197).chr(157) => 's',
    chr(197).chr(158) => 'S', chr(197).chr(159) => 's',
    chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
    chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
    chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
    chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
    chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
    chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
    chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
    chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
    chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
    chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
    chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
    chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
    chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
    chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
    chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
    chr(197).chr(190) => 'z', chr(197).chr(191) => 's',
    // Euro Sign
    chr(226).chr(130).chr(172) => 'E', );
    $string = strtr($string, $chars);

    return $string;
}
