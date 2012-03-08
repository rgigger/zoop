<?php
/**
 * Configuration class
 * 
 * Provides methods for retrieving configuration options from a YAML config file.
 *
 */
class Config
{
	private static $info = array();
	private static $file;
	
	static public function suggest($file, $prefix = NULL)
	{
		if($prefix)
			$root = &self::getReference($prefix);
		else
			$root = &self::$info;
		
		$root = self::mergeArray(Yaml::read($file), $root, true);
	}
	
	static public function insist($file, $prefix = NULL)
	{
		if($prefix)
			$root = &self::getReference($prefix);
		else
			$root = &self::$info;
		
		$root = self::mergeArray($root, Yaml::read($file), false);
	}
	
	//
	// these should maybe be put into a generic utilities file
	//
	
	static public function mergeArray($suggested, $insisted, $insistedFirst)
	{
		return self::_mergeArray($suggested, $insisted, $insistedFirst);
	}
	
	static public function _mergeArray(&$suggested, &$insisted, $insistedFirst)
	{
		if($insistedFirst)
		{
			foreach($suggested as $key => $val)
			{
				if(is_array($val))
					self::_mergeArray($suggested[$key], $insisted[$key], $insistedFirst);
				else
					$insisted[$key] = $val;
			}

			return $insisted;
		}
		else
		{
			foreach($insisted as $key => $val)
			{
				if(is_array($val))
					self::_mergeArray($suggested[$key], $insisted[$key], $insistedFirst);
				else
					$suggested[$key] = $val;
			}

			return $suggested;
		}
	}
	
	/**
	 * Specify configuration file to use
	 *
	 * @param string $file Path and filename of the config file to use
	 */
	static function setConfigFile($file)
	{
		self::$file = $file;
	}
	
	/**
	 * Loads the config file specified by the $file member variable (or app_dir/config.yaml) 
	 *
	 */
	static function load()
	{
		self::suggest(zoop_dir . '/config.yaml', 'zoop');
		
		if(!self::$file)
			self::setConfigFile(app_dir . '/config.yaml');
		self::insist(self::$file);
		
		if(defined('instance_dir') && instance_dir)
			self::insist(instance_dir . '/config.yaml');
	}
	
	/**
	 * Returns configuration options based on a path (i.e. zoop.db or zoop.application.info)
	 *
	 * @param string $path Path for which to fetch options
	 * @return array of configuration values
	 */
	static function get($path)
	{
		if($path === '')
			return self::$info;
		
		$parts = explode('.', $path);
		$cur = self::$info;
		
		foreach($parts as $thisPart)
			if(isset($cur[$thisPart]))
				$cur = $cur[$thisPart];
			else
				return false;
		
		return $cur;
	}	
	
	static public function set($path, $value)
	{
		$ref = &self::getReference($path, false);
		$ref = $value;
	}
	
	static function &getReference($path)
	{
		$parts = explode('.', $path);
		$cur = &self::$info;
		
		foreach($parts as $thisPart)
		{
			if(isset($cur[$thisPart]))
				$cur = &$cur[$thisPart];
			else
			{
				$cur[$thisPart] = array();
				$cur = &$cur[$thisPart];
			}
		}
		
		return $cur;
	}
	
	//	functions for getting scalar values and then formatting them
	static public function getFilePath($configPath)
	{
		$config = self::get($configPath);
		assert(is_string($config));
		return Zoop::expandPath($config);
	}
}
