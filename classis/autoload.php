<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 21.09.16
 * Time: 15:25
 */
spl_autoload_register(function($name){
    require_once __DIR__.'/'.$name.'.php';
});