<?php declare(strict_types=1);

/**
 * Class OutputNamingConvention
 */
class OutputNamingConvention extends \Nette\Object implements \Webloader\IOutputNamingConvention
{

	private $suffix = '';

	public static function createCssConvention()
	{
		$convention = new static();
		$convention->setSuffix('.css');
		return $convention;
	}

	public static function createJsConvention()
	{
		$convention = new static();
		$convention->setSuffix('.js');
		return $convention;
	}

	public function getSuffix()
	{
		return $this->suffix;
	}

	public function setSuffix($suffix)
	{
		$this->suffix = (string)$suffix;
	}

	public function getFilename(array $files, \Webloader\Compiler $compiler)
	{
		$name = substr(md5(implode('#', $files)), 0, 6);
		return $name . $this->suffix;
	}

}
