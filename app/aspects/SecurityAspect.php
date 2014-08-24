<?php

use Kdyby\Aop;

class SecurityAspect extends Nette\Object {

	/** @var \Nette\Security\User */
	private $user;

	public function __construct(Nette\Security\User $user) {
		$this->user = $user;
	}

	/**
	 * @Aop\Before("method(Model\*->[save]())")
	 */
	public function secureCreate(Aop\JoinPoint\BeforeMethod $before) {
//		ob_start();
//		dump("[SECURED - CREATE] " . $before->getTargetReflection());
//		dump($this->user->isAllowed('Admin', Model\Authorizator::CREATE));
	}

	/**
	 * @Aop\Before("method(Model\*->[find*|rand|count*|*search]())")
	 */
	public function secureReads(Aop\JoinPoint\BeforeMethod $before) {
//		ob_start();
//		dump("[SECURED - READ] " . $before->getTargetReflection());
//		dump($this->user->isAllowed('Admin', Model\Authorizator::READ));
	}

	/**
	 * @Aop\Before("method(Model\*->[save]())")
	 */
	public function secureUpdate(Aop\JoinPoint\BeforeMethod $before) {
//		ob_start();
//		dump("[SECURED - UPDATE] " . $before->getTargetReflection());
//		dump($this->user->isAllowed('Admin', Model\Authorizator::UPDATE));
	}

	/**
	 * @Aop\Before("method(Model\*->[delete]())")
	 */
	public function secureDelete(Aop\JoinPoint\BeforeMethod $before) {
//		ob_start();
//		dump("[SECURED - DELETE] " . $before->getTargetReflection());
//		dump($this->user->isAllowed('Admin', Model\Authorizator::DELETE));
	}

}
