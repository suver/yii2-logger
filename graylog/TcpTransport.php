<?php
namespace suver\logger\graylog;

use yii\helpers\ArrayHelper;
use Gelf;

class TcpTransport extends Logger implements TransportIntarface
{
	const DEFAULT_HOST = "127.0.0.1";
	const DEFAULT_PORT = 12201;

	/**
	 * @var Tcp Host
	 */
	public $host;

	/**
	 * @var Tcp port
	 */
	public $port;

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
	}

	/**
	 * @param $type
	 * @return bool
	 */
	public function checkType($type)
	{
		return $type == 'tcp';
	}

	/**
	 * @return Gelf\Transport\UdpTransport
	 */
	public function getTransport()
	{
		return new Gelf\Transport\TcpTransport($this->host, $this->port);
	}
}