<?php
namespace suver\logger\graylog;

use yii\helpers\ArrayHelper;
use Gelf;

class UdpTransport extends Logger implements TransportIntarface
{
	const CHUNK_GELF_ID = "\x1e\x0f";
	const CHUNK_MAX_COUNT = 128; // as per GELF spec
	const CHUNK_SIZE_LAN = 8154;
	const CHUNK_SIZE_WAN = 1420;

	const DEFAULT_HOST = "127.0.0.1";
	const DEFAULT_PORT = 12201;

	/**
	 * @var Udp Host
	 */
	public $host;

	/**
	 * @var Udp port
	 */
	public $port;

	/**
	 * @var Chunk lan size
	 */
	public $chunk;

	/**
	 * UdpTransport constructor.
	 * @param array $transportOptions
	 */
	public function __construct($transportOptions = [])
	{
		if (!empty($transportOptions)) {
			$this->loadOptions($transportOptions);
		}
	}

	/**
	 * @param $transportOptions
	 */
	public function loadOptions($transportOptions)
	{
		$this->host = ArrayHelper::getValue($transportOptions, 'host', self::DEFAULT_HOST);
		$this->port = ArrayHelper::getValue($transportOptions, 'port', self::DEFAULT_PORT);
		$this->chunk = ArrayHelper::getValue($transportOptions, 'chunk', self::CHUNK_SIZE_LAN);
	}

	/**
	 * @param $type
	 * @return bool
	 */
	public function checkType($type)
	{
		return $type == 'udp';
	}

	/**
	 * @return Gelf\Transport\UdpTransport
	 */
	public function getTransport()
	{
		return new Gelf\Transport\UdpTransport($this->host, $this->port, $this->chunk);
	}
}