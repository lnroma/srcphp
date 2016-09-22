#!/usr/bin/php
<?php
require_once('classis/autoload.php');

$request = new Request();

if(isset($argv[1]) && $argv[1] == '--getToken') {
    echo $request->getOauthLink();die;
}

$home = $_SERVER['HOME'];
$config = include($home . '/.config/scrphp/config.php');
$nameScreenshot = date('Y_m_d_G_i_s_') . 'screen.png';

system('scrot -s /tmp/'.$nameScreenshot);

$result = $request
    ->setToken($config['token'])
    ->setFileNameOnDisk($nameScreenshot)
    ->setPathToFile('/tmp/'.$nameScreenshot)
    ->upload()
    ->publicateFile();

$url = $result['public_url'];

echo $url.PHP_EOL;



