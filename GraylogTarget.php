<?php
/**
 * @copyright Copyright (c) 2014 Roman Ovchinnikov
 * @link https://github.com/RomeroMsk
 * @version 1.0.1
 */
namespace suver\logger;

use suver\logger\graylog\AmqpTransport;
use suver\logger\graylog\TcpTransport;
use suver\logger\graylog\UdpTransport;
use yii\log\Target;
use yii\log\Logger;
use Psr\Log\LogLevel;

/**
 * GraylogTarget sends log to Graylog2 (in GELF format)
 *
 * @author Suver <suver@inbox.ru>
 * @link https://github.com/suver/yii2-logger
 */
class GraylogTarget extends Target
{
	/**
	 * @var string Graylog2 transport type
	 */
	public $type = 'tcp';

	/**
	 * @var string default facility name
	 */
	public $facility = 'yii2-logs';

	/**
	 * @var boolean whether to add authenticated user username to additional fields
	 */
	public $addUsername = false;

	/**
	 * @var array transport options
	 */
	public $transport = [];

	private $transportList = [];

	/**
	 * @var array graylog levels
	 */
	private $_levels = [
		Logger::LEVEL_TRACE => LogLevel::DEBUG,
		Logger::LEVEL_PROFILE_BEGIN => LogLevel::DEBUG,
		Logger::LEVEL_PROFILE_END => LogLevel::DEBUG,
		Logger::LEVEL_INFO => LogLevel::INFO,
		Logger::LEVEL_WARNING => LogLevel::WARNING,
		Logger::LEVEL_ERROR => LogLevel::ERROR,
	];

	/**
	 * Sends log messages to Graylog2 input
	 */
	public function export()
	{
		$this->addTransport(new UdpTransport());
		$this->addTransport(new TcpTransport());
		$this->addTransport(new AmqpTransport());

		foreach ($this->transportList as $transportObject)
		{
			if ($transportObject->checkType($this->type))
			{
				$transportObject->loadOptions($this->transport);
				$transportObject->export($this->_levels, $this);
			}
		}
	}

	private function addTransport($transportObject)
	{
		$this->transportList[] = $transportObject;
	}
}
