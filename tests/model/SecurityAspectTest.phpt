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

$container = require __DIR__ . '/../bootstrap.php';

/**
 * @testCase
 */
class SecurityAspectTest extends Tester\TestCase
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

	public function __construct(Nette\DI\Container $container)
	{
		$this->tester = new PresenterTester($container);
		$this->pages = $container->getByType('\Model\Pages');
		$this->pictures = $container->getByType('\Model\Pictures');
		$this->posts = $container->getByType('\Model\Posts');
		$this->postsMirror = $container->getByType('\Model\PostsMirror');
		$this->settings = $container->getByType('\Model\Settings');
		$this->tags = $container->getByType('\Model\Tags');
		$this->users = $container->getByType('\Model\Users');
	}

	public function setUp()
	{
		$this->tester->logIn(1, 'demo');
	}

	public function testRestrictedPagesSave()
	{
		Tester\Assert::exception(function () {
			$page = new Page;
			$page->title = 'title';
			$page->slug = 'slug';
			$page->body = 'body';
			$this->pages->save($page);
		}, 'Nette\Security\AuthenticationException', 'You are NOT allowed to call function %a%(). You need at least %a% role.');
	}

	public function testRestrictedPicturesSave()
	{
		Tester\Assert::exception(function () {
			$picture = new Picture;
			$picture->uuid = 'uuid';
			$picture->name = 'name';
			$this->pictures->save($picture);
		}, 'Nette\Security\AuthenticationException', 'You are NOT allowed to call function %a%(). You need at least %a% role.');
	}

	public function testRestrictedPostsSave()
	{
		Tester\Assert::exception(function () {
			$post = new Post;
			$post->title = 'title';
			$post->slug = 'slug';
			$post->body = 'body';
			$this->posts->save($post);
		}, 'Nette\Security\AuthenticationException', 'You are NOT allowed to call function %a%(). You need at least %a% role.');
	}

	public function testRestrictedPostsMirrorSave()
	{
		Tester\Assert::exception(function () {
			$postMirror = new PostMirror;
			$postMirror->title = 'title';
			$postMirror->body = 'body';
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
			$tag->name = 'name';
			$tag->color = 'color';
			$this->tags->save($tag);
		}, 'Nette\Security\AuthenticationException', 'You are NOT allowed to call function %a%(). You need at least %a% role.');
	}

	public function testRestrictedUsersSave()
	{
		Tester\Assert::exception(function () {
			$user = new User;
			$user->username = 'username';
			$user->password = 'password';
			$user->role = 'role';
			$this->users->save($user);
		}, 'Nette\Security\AuthenticationException', 'You are NOT allowed to call function %a%(). You need at least %a% role.');
	}

	public function tearDown()
	{
		$this->tester->logOut();
	}

}

$test = new SecurityAspectTest($container);
$test->run();
