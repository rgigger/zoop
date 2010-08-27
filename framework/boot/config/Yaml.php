<?php
class Yaml
{
	static function read($filename)
	{
		if(!file_exists($filename))
			trigger_error("YAML error: file '$filename' does not exist");
		return Spyc::YAMLLoad($filename);
	}
}
