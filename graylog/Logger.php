<?php
namespace suver\logger\graylog;

use yii\helpers\ArrayHelper;
use Gelf;
use Yii;

abstract class Logger
{

	abstract public function getTransport();

	public function export()
	{
		$transport = $this->getTransport();

		$publisher = new Gelf\Publisher($transport);
		foreach ($this->messages as $message) {
			list($text, $level, $category, $timestamp) = $message;
			$gelfMsg = new Gelf\Message;
			// Set base parameters
			$gelfMsg->setLevel(ArrayHelper::getValue($this->_levels, $level, LogLevel::INFO))
				->setTimestamp($timestamp)
				->setFacility($this->facility)
				->setAdditional('category', $category)
				->setFile('unknown')
				->setLine(0);
			// For string log message set only shortMessage
			if (is_string($text)) {
				$gelfMsg->setShortMessage($text);
			} elseif ($text instanceof \Exception) {
				$gelfMsg->setShortMessage('Exception ' . get_class($text) . ': ' . $text->getMessage());
				$gelfMsg->setFullMessage((string)$text);
				$gelfMsg->setLine($text->getLine());
				$gelfMsg->setFile($text->getFile());
			} else {
				// If log message contains special keys 'short', 'full' or 'add', will use them as shortMessage, fullMessage and additionals respectively
				$short = ArrayHelper::remove($text, 'short');
				$full = ArrayHelper::remove($text, 'full');
				$add = ArrayHelper::remove($text, 'add');
				// If 'short' is set
				if ($short !== null) {
					$gelfMsg->setShortMessage($short);
					// All remaining message is fullMessage by default
					$gelfMsg->setFullMessage(VarDumper::dumpAsString($text));
				} else {
					// Will use log message as shortMessage by default (no need to add fullMessage in this case)
					$gelfMsg->setShortMessage(VarDumper::dumpAsString($text));
				}
				// If 'full' is set will use it as fullMessage (note that all other stuff in log message will not be logged, except 'short' and 'add')
				if ($full !== null) {
					$gelfMsg->setFullMessage(VarDumper::dumpAsString($full));
				}
				// Process additionals array (only with string keys)
				if (is_array($add)) {
					foreach ($add as $key => $val) {
						if (is_string($key)) {
							if (!is_string($val)) {
								$val = VarDumper::dumpAsString($val);
							}
							$gelfMsg->setAdditional($key, $val);
						}
					}
				}
			}
			// Set 'file', 'line' and additional 'trace', if log message contains traces array
			if (isset($message[4]) && is_array($message[4])) {
				$traces = [];
				foreach ($message[4] as $index => $trace) {
					$traces[] = "{$trace['file']}:{$trace['line']}";
					if ($index === 0) {
						$gelfMsg->setFile($trace['file']);
						$gelfMsg->setLine($trace['line']);
					}
				}
				$gelfMsg->setAdditional('trace', implode("\n", $traces));
			}
			// Add username
			if (($this->addUsername) && (Yii::$app->has('user')) && ($user = Yii::$app->get('user')) && ($identity = $user->getIdentity(false))) {
				$gelfMsg->setAdditional('username', $identity->username);
			}
			// Publish message
			$publisher->publish($gelfMsg);
		}
	}
}