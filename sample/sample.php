<?php

require __DIR__.'/../vendor/autoload.php';

$webhook = '';

$logger = new Monolog\Logger('local');
$logger->pushHandler(new jspapp\DiscordHandler\DiscordHandler($webhook));

$logger->error('User created.', ['Name' => 'Test User', 'Id' => 1]);
