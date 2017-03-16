<?php
namespace suver\logger\graylog;

use yii\helpers\ArrayHelper;
use Gelf;

class AmqpTransport extends Logger implements TransportIntarface
{
	const DEFAULT_HOST = "localhost";
	const DEFAULT_LOGIN = 'guest';
	const DEFAULT_PASSWORD = 'guest';
	const DEFAULT_EXCHANGE = 'log';
	const DEFAULT_QUEUE = 'log';

	/**
	 * @var Amqp Host
	 */
	public $host;

	/**
	 * @var Amqp login
	 */
	public $login;

	/**
	 * @var Amqp password
	 */
	public $password;

	/**
	 * @var Exchange name
	 */
	public $exchange;

	/**
	 * @var Queue name
	 */
	public $queue;

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
		$this->host = ArrayHelper::getValue($transportOptions, self::DEFAULT_HOST);
		$this->login = ArrayHelper::getValue($transportOptions, self::DEFAULT_LOGIN);
		$this->passsword = ArrayHelper::getValue($transportOptions, self::DEFAULT_PASSWORD);
		$this->exchange = ArrayHelper::getValue($transportOptions, self::DEFAULT_EXCHANGE);
		$this->queue = ArrayHelper::getValue($transportOptions, self::DEFAULT_QUEUE);
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
		$connection = new \AMQPConnection(array(
			'host' => $this->host,
			'login' => $this->login,
			'password' => $this->password,
		));

		$connection->connect();
		$channel = new \AMQPChannel($connection);

		$exchange = new \AMQPExchange($channel);
		$exchange->setName($this->exchange);
		$exchange->setType(AMQP_EX_TYPE_FANOUT);
		$exchange->declareExchange();

		$queue = new \AMQPQueue($channel);
		$queue->setName($this->queue);
		$queue->setFlags(AMQP_DURABLE);
		$queue->declareQueue();
		$queue->bind($exchange->getName());

		return new Gelf\Transport\AmqpTransport($exchange, $queue);
	}
}