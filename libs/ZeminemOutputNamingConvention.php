<?php

/**
 * Class ZeminemOutputNamingConvention
 */
class ZeminemOutputNamingConvention implements \Webloader\IOutputNamingConvention {

	private $suffix = '';

	public static function createCssConvention() {
		$convention = new static();
		$convention->setSuffix('.css');
		return $convention;
	}

	public static function createJsConvention() {
		$convention = new static();
		$convention->setSuffix('.js');
		return $convention;
	}

	public function getSuffix() {
		return $this->suffix;
	}

	public function setSuffix($suffix) {
		$this->suffix = (string)$suffix;
	}

	public function getFilename(array $files, \Webloader\Compiler $compiler) {
		$name = $this->createHash($files, $compiler);
		return $name . $this->suffix;
	}

	protected function createHash(array $files, \Webloader\Compiler $compiler) {
		return md5(implode("#", $files));
	}

}
