<?php declare(strict_types=1);

/**
 * FSHL 2.1.0                                  | Fast Syntax HighLighter |
 * -----------------------------------------------------------------------
 *
 * LICENSE
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 */

namespace FSHL\Lexer;

use FSHL;
use FSHL\Generator;

/**
 * TEX lexer.
 *
 * @copyright Copyright (c) 2014 Martin Zlámal
 * @license http://fshl.kukulich.cz/#license
 */
class Tex implements FSHL\Lexer
{

	/**
	 * Returns language name.
	 *
	 * @return string
	 */
	public function getLanguage()
	{
		return 'Tex';
	}

	/**
	 * Returns initial state.
	 *
	 * @return string
	 */
	public function getInitialState()
	{
		return 'OUT';
	}

	/**
	 * Returns states.
	 *
	 * @return array
	 */
	public function getStates()
	{
		return [
			'OUT' => [
				[
					'$' => ['MATH', Generator::NEXT],
					'\\' => ['FUNC', Generator::NEXT],
					'{' => ['ATTR1', Generator::NEXT],
					'[' => ['ATTR2', Generator::NEXT],
					'%' => ['COMMENT', Generator::NEXT],
					'LINE' => [Generator::STATE_SELF, Generator::NEXT],
					'TAB' => [Generator::STATE_SELF, Generator::NEXT],
				],
				Generator::STATE_FLAG_NONE,
				'tex-out',
				NULL
			],
			'MATH' => [
				[
					'LINE' => [Generator::STATE_SELF, Generator::NEXT],
					'TAB' => [Generator::STATE_SELF, Generator::NEXT],
					'$' => [Generator::STATE_RETURN, Generator::CURRENT],
				],
				Generator::STATE_FLAG_RECURSION,
				'tex-math',
				NULL
			],
			'FUNC' => [
				[
					'!ALNUM_' => [Generator::STATE_RETURN, Generator::BACK],

				],
				Generator::STATE_FLAG_RECURSION,
				'tex-func',
				NULL
			],
			'ATTR1' => [
				[
					'LINE' => [Generator::STATE_SELF, Generator::NEXT],
					'TAB' => [Generator::STATE_SELF, Generator::NEXT],
					'}' => [Generator::STATE_RETURN, Generator::CURRENT]
				],
				Generator::STATE_FLAG_RECURSION,
				'tex-attr1',
				NULL
			],
			'ATTR2' => [
				[
					'LINE' => [Generator::STATE_SELF, Generator::NEXT],
					'TAB' => [Generator::STATE_SELF, Generator::NEXT],
					']' => [Generator::STATE_RETURN, Generator::CURRENT],
				],
				Generator::STATE_FLAG_RECURSION,
				'tex-attr2',
				NULL
			],
			'COMMENT' => [
				[
					'LINE' => [Generator::STATE_RETURN, Generator::BACK],
					'TAB' => [Generator::STATE_SELF, Generator::NEXT],
				],
				Generator::STATE_FLAG_RECURSION,
				'tex-comment',
				NULL
			],
		];
	}

	/**
	 * Returns special delimiters.
	 *
	 * @return array
	 */
	public function getDelimiters()
	{
		return [];
	}

	/**
	 * Returns keywords.
	 *
	 * @return array
	 */
	public function getKeywords()
	{
		return [];
	}
}
