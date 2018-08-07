<?php

namespace jspapp\DiscordHandler;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
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

	/**
	 * Number of requests remaining within the rate limit.
	 * @var int
	 */
	private static $rateLimitRemaining;

	/**
	 * Epoch time at which the rate limit resets.
	 * @var int
	 */
	private static $rateLimitReset;

	public function __construct($webhook, $level = Logger::ERROR, bool $bubble = true)
	{
		$this->webhook = $webhook;
		$this->client = new Client();

		parent::__construct($level, $bubble);
	}

	protected function write(array $record)
	{
		if (self::$rateLimitRemaining == 0 && self::$rateLimitReset !== null) {
			$this->waitUntil(self::$rateLimitReset);
		}

		try {
			$response = $this->send($record);
		} catch (ClientException $ex) {
			$response = $ex->getResponse();
			$retryAfter = $response->getHeader('Retry-After')[0];

			sleep($retryAfter);
			$this->send($record);
		}

		self::$rateLimitRemaining = $response->getHeader('X-RateLimit-Remaining')[0];
		self::$rateLimitReset = $response->getHeader('X-RateLimit-Reset')[0];

		print_r(self::$rateLimitRemaining.PHP_EOL);
		print_r(self::$rateLimitReset.PHP_EOL);
	}

	private function send(array $record) {
		return $this->client->request('POST', $this->webhook, [
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

	private function waitUntil($timestamp)
	{
		time_sleep_until($timestamp);
	}
}