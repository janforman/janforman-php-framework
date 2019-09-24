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
