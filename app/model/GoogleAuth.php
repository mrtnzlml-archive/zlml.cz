<?php

namespace Model;

class GoogleAuth extends \Nette\Object implements \Nette\Security\IAuthenticator {

	/** @var \Model\Google @inject */
	public $google;

	public function authenticate(array $data) {
		$info = end($data);
		$user = $this->users->findOneBy(['google_id' => $info->id]);
		$user = array(
			'name'=>'Martin',
			'google_id' => $info->id,
			'mail' => 'mrtnzlml@gmail.com',
			'role' => '',
		);

		// If user with this email exists, link the accounts
		if (!$user) {
			$user = $this->users->findOneBy(['email' => $info->email]);
			if ($user) {
				$user->google_id = $info->id;
				$user->update();
			}
		}

		// Otherwise, register new user
		if (!$user) {
			$user = $this->users->insert([
				'name' => $info->name,
				'google_id' => $info->id,
				'mail' => $info->email,
				'role' => '',
			]);
		}

		return new \Nette\Security\Identity($user->id, explode(';', $user->role), []);
	}

}