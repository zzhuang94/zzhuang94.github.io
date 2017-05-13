<?php
// generate time axis

$dev = 'file:///C:/Users/zzhuang94/my_dockers/centos/zzhuang94.github.io/build/html/';
$pro = 'https://zzhuang94.github.io/build/html/';
$link_prefix = (isset($argv[1]) && $argv[1]) == 'pro' ? $pro : $dev;

// modify index.rst
exec("sed -i '\$d' source/index.rst");
$time_axis_link = '.. _时间轴: ' . $link_prefix . 'time_axis.html';
exec("echo '$time_axis_link' >> source/index.rst");

// sort blogs and generate source/time_axis.rst
$body_str = '';
$foot_str = '';
exec('ls -t `find source -name "*.rst" ! -name "index.rst" ! -name "time_axis.rst"`', $all_blog);
foreach ($all_blog as $b) {
    $title = getTitle($b);
    $body_str .= "- `$title`_\n";
    $link = getLink($b, $link_prefix);
    $foot_str .= ".. _$title: $link\n";
}
exec("sed -i '4,\$d' source/time_axis.rst");
exec("echo '" . $body_str . "\n" . $foot_str ."' >> source/time_axis.rst");

exec('make html');

function getLink($file, $link_prefix)
{
    $html_file = ltrim($file, 'source/');
    $html_file = rtrim($html_file, '.rst');
    return $link_prefix . $html_file . '.html';
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
    return "$ctime" . $title;
}
