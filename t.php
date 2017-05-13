<?php

$body_str = '';
$foot_str = '';
exec('find source -name "*.rst" ! -name "index.rst" ! -name "time_axis.rst"', $all_blog);
foreach ($all_blog as $b) {
    $title = getTitle($b);
    $body_str .= "- `$title`_\n";
    $foot_str .= ".. _$title: ";
}

function getCtime($file)
{
    exec("stat $file", $stat);
    $ctime = $stat[6];
    $k1 = strpos($ctime, ' ');
    $ctime = substr($ctime, $k1 + 1, 19);
    return $ctime;
}

function getTitle($file)
{
    $ctime = getCtime($file);
    $t_k = strrpos($file, '/');
    $index_file = substr($file, 0, $t_k) . '/index.rst';
    exec("head -1 $index_file", $i_title);
    exec("head -1 $file", $title);
    $title = '【'. $i_title[0] . '】' . $title[0];
    return $title . "[$ctime]";
}
