<?php
class ex
{
	static private $params;
	static private $echo = false;
	
	static public function echoOn()
	{
		self::$echo = true;
	}
	
	static public function echoOff()
	{
		self::$echo = false;
	}
	
	static public function run($command, $params, &$output)
	{
		$commandEscaped = self::prepareCommand($command, $params);
		if(self::$echo)
			echo "$commandEscaped\n";
		exec($commandEscaped, $output, $returnValue);		
		return $returnValue;
	}
	
	static public function pass($command, $params)
	{
		$commandEscaped = self::prepareCommand($command, $params);
		if(self::$echo)
			echo "$commandEscaped\n";
		passthru($commandEscaped, $returnValue);		
		return $returnValue;
	}
	
	static public function prepareCommand($command, $params)
	{
		//	do all of the variable replacements
		self::$params = $params;
		$command = preg_replace_callback("/:([[:alpha:]_\d]+)/", array('Ex', 'escapeCallback'), $command);
		
		return $command;
	}
	
	private function escapeCallback($matches)
	{
		return escapeshellarg(self::$params[$matches[1]]);
	}
}
