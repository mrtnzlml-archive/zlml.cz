<?php

namespace Test;

use Entity\Page;
use Entity\Picture;
use Entity\Post;
use Entity\PostMirror;
use Entity\Tag;
use Entity\User;
use Model;
use Nette;
use Tester;

require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class SecurityAspectTest extends \CustomTestCase
{

	/** @var Model\Pages */
	private $pages;
	/** @var Model\Pictures */
	private $pictures;
	/** @var Model\Posts */
	private $posts;
	/** @var Model\PostsMirror */
	private $postsMirror;
	/** @var Model\Settings */
	private $settings;
	/** @var Model\Tags */
	private $tags;
	/** @var Model\Users */
	private $users;

	public function __construct()
	{
		$this->pages = $this->getService('Model\Pages');
		$this->pictures = $this->getService('Model\Pictures');
		$this->posts = $this->getService('Model\Posts');
		$this->postsMirror = $this->getService('Model\PostsMirror');
		$this->settings = $this->getService('Model\Settings');
		$this->tags = $this->getService('Model\Tags');
		$this->users = $this->getService('Model\Users');
	}

	public function setUp()
	{
		$this->logIn(1, 'demo');
	}

	public function testRestrictedPagesSave()
	{
		Tester\Assert::exception(function () {
			$page = new Page;
			$page->setTitle('title');
			$page->setSlug('slug');
			$page->setBody('body');
			$this->pages->save($page);
		}, 'Nette\Security\AuthenticationException', 'You are NOT allowed to call function %a%(). You need at least %a% role.');
	}

	public function testRestrictedPicturesSave()
	{
		Tester\Assert::exception(function () {
			$picture = new Picture;
			$picture->setUuid('uuid');
			$picture->setName('name');
			$this->pictures->save($picture);
		}, 'Nette\Security\AuthenticationException', 'You are NOT allowed to call function %a%(). You need at least %a% role.');
	}

	public function testRestrictedPostsSave()
	{
		Tester\Assert::exception(function () {
			$post = new Post;
			$post->setTitle('title');
			$post->setSlug('slug');
			$post->setBody('body');
			$this->posts->save($post);
		}, 'Nette\Security\AuthenticationException', 'You are NOT allowed to call function %a%(). You need at least %a% role.');
	}

	public function testRestrictedPostsMirrorSave()
	{
		Tester\Assert::exception(function () {
			$postMirror = new PostMirror;
			$postMirror->setTitle('title');
			$postMirror->setBody('body');
			$this->postsMirror->save($postMirror);
		}, 'Nette\Security\AuthenticationException', 'You are NOT allowed to call function %a%(). You need at least %a% role.');
	}

	public function testRestrictedSettingsSave()
	{
		Tester\Assert::exception(function () {
			$setting = new Nette\Utils\ArrayHash(['key' => 'value']);
			$this->settings->save($setting);
		}, 'Nette\Security\AuthenticationException', 'You are NOT allowed to call function %a%(). You need at least %a% role.');
	}

	public function testRestrictedTagsSave()
	{
		Tester\Assert::exception(function () {
			$tag = new Tag;
			$tag->setName('name');
			$tag->setColor('color');
			$this->tags->save($tag);
		}, 'Nette\Security\AuthenticationException', 'You are NOT allowed to call function %a%(). You need at least %a% role.');
	}

	public function testRestrictedUsersSave()
	{
		Tester\Assert::exception(function () {
			$user = new User;
			$user->setUsername('username');
			$user->setPassword('password');
			$user->setRole('role');
			$this->users->save($user);
		}, 'Nette\Security\AuthenticationException', 'You are NOT allowed to call function %a%(). You need at least %a% role.');
	}

	public function tearDown()
	{
		$this->logOut();
	}

}

(new SecurityAspectTest)->run();
