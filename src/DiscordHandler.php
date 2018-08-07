<?php

namespace jspapp\DiscordHandler;

use GuzzleHttp\Client;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

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

	public function __construct($webhooks, $level = Logger::ERROR, bool $bubble = true)
	{
		$this->webhooks = $webhooks;
		$this->client = new Client();

		parent::__construct($level, $bubble);
	}

	protected function write(array $record)
	{
		$embeds = array();
		foreach ($record['context'] as $key => $value) {
			$embeds[] = ['title' => $key, 'description' => $value,];
		}

		foreach ($this->webhooks as $url) {
			$this->client->request('POST', $url, [
				'json' => [
					'content' => $record['message'],
					'embeds' => $embeds,
				],
			]);
		}
	}
}