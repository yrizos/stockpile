<?php

$memcache = new Memcache();
$memcache->connect('127.0.0.1');

$memcache->set('hello', 1234, 0, 4);

$r = $memcache->get('asdasd');

var_dump($r);

//echo $memcache->get('hello');
