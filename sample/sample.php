<?php

require __DIR__.'/../vendor/autoload.php';

use Monolog\Logger;
use jspapp\MonologDiscord\DiscordHandler;
use jspapp\MonologDiscord\Laravel\DiscordChannel;

$webhook = '';

$log = new Logger('discord');
$log->pushHandler(new DiscordHandler($webhook));

$log->error('User created.', [
	'user_id' => 1,
	'email' => 'test@example.com',
	'address' => '123 Home St.',
]);