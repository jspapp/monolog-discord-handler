<?php

require '../vendor/autoload.php';

$webhook = 'https://discordapp.com/api/webhooks/[your webhook here]';

$logger = new Monolog\Logger('log');
$logger->pushHandler(new jspapp\DiscordHandler\DiscordHandler(
	[
		$webhook,
	],
	Monolog\Logger::DEBUG
));

$logger->info('User created', ['user_id' => 1, 'ip' => '127.0.0.1']);