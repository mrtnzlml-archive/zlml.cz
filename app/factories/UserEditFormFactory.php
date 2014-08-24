<?php

class UserEditFormFactory {

	private $users;

	public function __construct(Model\Users $users) {
		$this->users = $users;
	}

	public function create($id) {
		return new \Cntrl\UserEditForm($this->users, $id);
	}

}