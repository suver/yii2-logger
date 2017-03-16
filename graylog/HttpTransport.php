<?php
namespace suver\logger\graylog;

use yii\helpers\ArrayHelper;
use Gelf;

class HttpTransport extends Logger implements TransportIntarface
{

	public $domain;

	public $port;

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
		$this->host = ArrayHelper::getValue($transportOptions, self::DEFAULT_HOST);
		$this->port = ArrayHelper::getValue($transportOptions, self::DEFAULT_PORT);
		$this->chunk = ArrayHelper::getValue($transportOptions, self::CHUNK_SIZE_LAN);
	}

	/**
	 * @param $type
	 * @return bool
	 */
	public function checkType($type)
	{
		return $type == 'http';
	}

	/**
	 * @return Gelf\Transport\UdpTransport
	 */
	public function getTransport()
	{
		return new Gelf\Transport\UdpTransport($this->host, $this->port, $this->chunk);
	}
}