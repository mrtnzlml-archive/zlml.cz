<?php

use Kdyby\Aop;

class SecurityAspect extends Nette\Object {

	/** @var \Nette\Security\User */
	private $user;
	/** @var Doctrine\Common\Annotations\Reader */
	private $reader;

	public function __construct(Nette\Security\User $user, Doctrine\Common\Annotations\Reader $reader) {
		$this->user = $user;
		$this->reader = $reader;
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
	 * @Aop\Before("methodAnnotatedWith(Secure\Reads)")
	 */
	public function secureReads(Aop\JoinPoint\BeforeMethod $before) {
		$reads = $this->reader->getMethodAnnotation($before->getTargetReflection(), 'Secure\Reads');
		if (!$this->user->isInRole($reads->allow)) {
			$action = $before->getTargetReflection()->getName();
//			throw new \Nette\Security\AuthenticationException("You are NOT allowed to call this function ($action).");
		}
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
