<?php declare(strict_types = 1);

namespace App\Social\Twitter;

class ProfileInfo
{

	/**
	 * @var int
	 */
	private $followersCount;

	/**
	 * @var string
	 */
	private $profileImageUrl;

	public function __construct(int $followersCount, string $profileImageUrl)
	{
		$this->followersCount = $followersCount;
		$this->profileImageUrl = $profileImageUrl;
	}

	public function followersCount(): int
	{
		return $this->followersCount;
	}

	public function profileImageUrl(): string
	{
		return $this->profileImageUrl;
	}

}
