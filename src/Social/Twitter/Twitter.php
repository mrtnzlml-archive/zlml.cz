<?php declare(strict_types = 1);

namespace App\Social\Twitter;

use Nette\Caching\Cache;
use Nette\Caching\IStorage;

class Twitter
{

	/** @var \Twitter */
	private $twitterClient;

	/**
	 * @var \Nette\Caching\Cache
	 */
	private $cache;

	public function __construct(
		string $consumerKey,
		string $consumerSecret,
		string $accessToken,
		string $accessTokenSecret,
		IStorage $cacheStorage
	) {
		$this->twitterClient = new \Twitter($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);
		$this->cache = new Cache($cacheStorage, 'App.Twitter');
	}

	public function getPersonalProfileInfo()
	{
		try {
			return $this->cache->load('profileInfo', function (& $dependencies) {
				$dependencies = [
					Cache::EXPIRE => '2 hours',
				];

				$data = $this->twitterClient->request('account/verify_credentials', 'GET');
				return new ProfileInfo($data->followers_count, $data->profile_image_url_https);
			});
		} catch (\TwitterException $exc) {
			\Tracy\Debugger::log(get_class($exc) . ': ' . $exc->getMessage());
			return new NullProfileInfo;
		}
	}

}
