<?php

namespace jpapp\DiscordHandler;

use GuzzleHttp\Client;
use Monolog\Handler\AbstractProcessingHandler;

class DiscordHandler extends AbstractProcessingHandler
{
	/**
	 * List of Discord webhooks to post to.
	 * @var Array
	 */
	private $webhooks;

	/**
	 * Http client to connect to Discord.
	 * @var GuzzleHttp\Client
	 */
	private $client;

	public function __construct($webhooks, $level = Logger::DEBUG, bool $bubble = true)
	{
		$this->webhooks = $webhooks;
		$this->client = new Client();

		parent::__construct($level, $bubble);
	}

	protected function write(array $record)
	{
		foreach ($this->webhooks as $url) {
			$this->client->request('POST', $url, [
				'content' => $record['formatted'],
			]);
		}
	}
}