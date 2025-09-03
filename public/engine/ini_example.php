<?php

require_once __DIR__ . '/inc/User.php';
require_once __DIR__ . '/api/exchange/Currency.php';

use inc\User;

global $main_user;
$main_user = new User();
global $language;
$language = 'en';
