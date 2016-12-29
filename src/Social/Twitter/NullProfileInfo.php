<?php declare(strict_types = 1);

namespace App\Social\Twitter;

class NullProfileInfo
{

	public function followersCount(): string
	{
		return '';
	}

	public function profileImageUrl()
	{
		return NULL;
	}

}
