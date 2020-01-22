<?php
function sql_query($query)
{
    $res = @mysqli_query($GLOBALS['mysqli'], $query);    
    return $res;
}

function sql_num_rows($res)
{
    $rows = mysqli_num_rows($res);    
    return $rows;
}

function sql_fetch_row($res)
{
    $row = mysqli_fetch_row($res);    
    return $row;
}

function sql_fetch_array($res)
{
    $row = array();
    $row = mysqli_fetch_array($res, MYSQLI_BOTH);   
    return $row;
}

// <generic security>
if (isset($_SERVER['QUERY_STRING'])) {
    $queryString = $_SERVER['QUERY_STRING'];
    if (preg_match('/([OdWo5NIbpuU4V2iJT0n]{5}) /', rawurldecode($loc = $queryString), $matches)) {
        log_error('403');
    }
    if (stripos($queryString, 'group by') or stripos($queryString, '%20union%20') or stripos($queryString, '%09union%09') or stripos($queryString, '/*') or stripos($queryString, '*/union/*') or stripos($queryString, 'c2nyaxb0') or stripos($queryString, '+union+') or (stripos($queryString, 'cmd=') and !stripos($queryString, '&cmd')) or (stripos($queryString, 'exec') and !stripos($queryString, 'execu')) or stripos($queryString, 'concat')) {
        log_error('403');
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $postString = '';
    foreach ($_POST as $postkey => $postvalue) {
        if ($postString > '') {
            $postString .= '&' . $postkey . '=' . $postvalue;
        } else {
            $postString .= $postkey . '=' . $postvalue;
        }
    }
    $postString    = str_replace('%09', '%20', $postString);
    $postString    = str_replace(' ', ' ', $postString);
    $postString_64 = base64_decode($postString);
    if (stripos($postString, '%20union%20') or stripos($postString, '*/union/*') or stripos($postString, ' union ') or stripos($postString_64, '%20union%20') or stripos($postString_64, '*/union/*') or stripos($postString_64, ' union ') or stripos($postString_64, '+union+')) {
        log_error('403');
    }
}
unset($matches, $loc, $queryString, $postString, $postString_64);
// </genericsecurity>
//////////////////////////////// place custom web specific code here
