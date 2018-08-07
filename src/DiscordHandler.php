<?php

namespace jspapp\DiscordHandler;

use GuzzleHttp\Client;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class DiscordHandler extends AbstractProcessingHandler
{
	/**
	 * Discord webhook to post to.
	 * @var string
	 */
	private $webhook;

	/**
	 * Http client to connect to Discord.
	 * @var GuzzleHttp\Client
	 */
	private $client;

	public function __construct($webhook, $level = Logger::ERROR, bool $bubble = true)
	{
		$this->webhook = $webhook;
		$this->client = new Client();

		parent::__construct($level, $bubble);
	}

	protected function write(array $record)
	{
		$this->client->request('POST', $this->webhook, [
			'json' => [
				'content' => $record['message'],
				'embeds' => $this->formatEmbeds($record),
			],
		]);
	}

	private function formatEmbeds(array $record)
	{
		$embeds = array();
		foreach ($record['context'] as $key => $value) {
			if (is_object($value) || is_array($value)) {
				$value = json_encode($value);
			}

			$embeds[] = [
				'title' => $key,
				'description' => $value,
			];
		}

		return $embeds;
	}
}