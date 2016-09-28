<?php declare(strict_types = 1);

namespace App\Listeners;

use Kdyby;
use Model;
use Nette;

class PostsListener extends Nette\Object implements Kdyby\Events\Subscriber
{

	private $posts;

	private $postsMirror;

	public function __construct(Model\Posts $posts, Model\PostsMirror $postsMirror)
	{
		$this->posts = $posts;
		$this->postsMirror = $postsMirror;
	}

	public function getSubscribedEvents()
	{
		return [
			'Model\Posts::onSave',
			'Model\Posts::onDelete',
		];
	}

	public function onSave($entityId)
	{
		if (!is_numeric($entityId)) {
			throw new \Nette\InvalidArgumentException('Argument should be numeric ID of the Entity, got ' . gettype($entityId));
		}
		$post = $this->posts->findOneBy(['id' => $entityId]);
		$entity = $this->postsMirror->findOneBy(['id' => $entityId]);
		if (!$entity) { //doesn't exist yet
			$entity = new \Entity\PostMirror;
			$entity->id = $entityId;
		}
		$entity->title = $post->title;
		$entity->body = $post->body;
		$entity->date = $post->date;
		$this->postsMirror->save($entity);
	}

	public function onDelete($entityId)
	{
		if (!is_numeric($entityId)) {
			throw new \Nette\InvalidArgumentException('Argument should be numeric ID of the Entity, got ' . gettype($entityId));
		}
		$this->postsMirror->delete($this->postsMirror->findOneBy(['id' => $entityId]));
	}

}
