<?php
namespace suver\logger\graylog;

interface TransportIntarface
{
	public function loadOptions($transportOptions);

	public function checkType($type);

	public function export();
}