<?php

use Kdyby\Aop;

/**
 * TODO: kontrola, jestli to všechny modelové metody (v NS) mají
 */
class SecurityAspect extends Nette\Object {

	/** @var \Nette\Security\User */
	private $user;
	/** @var Doctrine\Common\Annotations\Reader */
	private $reader;
	/** @var \Nette\Security\IAuthorizator */
	private $authorizator;

	public function __construct(Nette\Security\User $user, Doctrine\Common\Annotations\Reader $reader, Nette\Security\IAuthorizator $authorizator) {
		$this->user = $user;
		$this->reader = $reader;
		$this->authorizator = $authorizator;
	}

	/**
	 * TODO: více definicí a negace pravidla
	 *
	 * @param Aop\JoinPoint\BeforeMethod $before
	 * @throws \Nette\Security\AuthenticationException
	 *
	 * @Aop\Before("methodAnnotatedWith(Secure\Create)")
	 */
	public function secureCreate(Aop\JoinPoint\BeforeMethod $before) {
		$create = $this->reader->getMethodAnnotation($before->getTargetReflection(), 'Secure\Create');
		if (!$this->authorizator->isAtLeastInRole($create->allow, $this->user)) {
			$this->throwExcetion($before, $create->allow);
		}
	}

	/**
	 * @param Aop\JoinPoint\BeforeMethod $before
	 * @throws \Nette\Security\AuthenticationException
	 *
	 * @Aop\Before("methodAnnotatedWith(Secure\Read)")
	 */
	public function secureRead(Aop\JoinPoint\BeforeMethod $before) {
		$read = $this->reader->getMethodAnnotation($before->getTargetReflection(), 'Secure\Read');
		if (!$this->authorizator->isAtLeastInRole($read->allow, $this->user)) {
			$this->throwExcetion($before, $read->allow);
		}
	}

	/**
	 * @param Aop\JoinPoint\BeforeMethod $before
	 * @throws \Nette\Security\AuthenticationException
	 *
	 * @Aop\Before("methodAnnotatedWith(Secure\Update)")
	 */
	public function secureUpdate(Aop\JoinPoint\BeforeMethod $before) {
		$update = $this->reader->getMethodAnnotation($before->getTargetReflection(), 'Secure\Update');
		if (!$this->authorizator->isAtLeastInRole($update->allow, $this->user)) {
			$this->throwExcetion($before, $update->allow);
		}
	}

	/**
	 * @param Aop\JoinPoint\BeforeMethod $before
	 * @throws \Nette\Security\AuthenticationException
	 *
	 * @Aop\Before("methodAnnotatedWith(Secure\Delete)")
	 */
	public function secureDelete(Aop\JoinPoint\BeforeMethod $before) {
		$delete = $this->reader->getMethodAnnotation($before->getTargetReflection(), 'Secure\Delete');
		if (!$this->authorizator->isAtLeastInRole($delete->allow, $this->user)) {
			$this->throwExcetion($before, $delete->allow);
		}
	}

	/**
	 * @param Aop\JoinPoint\MethodInvocation $invocation
	 * @param $role
	 * @throws \Nette\Security\AuthenticationException
	 */
	private function throwExcetion(Aop\JoinPoint\MethodInvocation $invocation, $role) {
		$action = $invocation->getTargetReflection()->getName();
		throw new \Nette\Security\AuthenticationException("You are NOT allowed to call function $action(). You need at least $role role.");
	}

}
