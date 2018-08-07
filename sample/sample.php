<?php

use jspapp\DiscordHandler\DiscordHandler;

require '../vendor/autoload.php';

$webhook = 'https://discordapp.com/api/webhooks/xxxxxxxxxxxxxxxxxx/yyyyyyyyyyyyyyyyyyyyyy';

$logger = new Monolog\Logger('local');
$logger->pushHandler(new DiscordHandler($webhook));

$logger->error('User created.', ['user_id' => 1, 'ip' => '127.0.0.1']);
