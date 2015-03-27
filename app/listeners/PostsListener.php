<?php

class PostsListener extends Nette\Object implements Kdyby\Events\Subscriber {

	private $posts;
	private $postsMirror;

	public function __construct(Model\Posts $posts, Model\PostsMirror $postsMirror) {
		$this->posts = $posts;
		$this->postsMirror = $postsMirror;
	}

	public function getSubscribedEvents() {
		return [
			'Model\Posts::onSave',
			'Model\Posts::onDelete',
		];
	}

	public function onSave($entity_id) {
		if (!is_numeric($entity_id)) {
			throw new Nette\InvalidArgumentException('Argument should be numeric ID of the Entity, got ' . gettype($entity_id));
		}
		$post = $this->posts->findOneBy(['id' => $entity_id]);
		$entity = $this->postsMirror->findOneBy(['id' => $entity_id]);
		if (!$entity) { //doesn't exist yet
			$entity = new Entity\PostMirror;
			$entity->id = $entity_id;
		}
		$entity->title = $post->title;
		$entity->body = $post->body;
		$entity->date = $post->date;
		$this->postsMirror->save($entity);
	}

	public function onDelete($entity_id) {
		if (!is_numeric($entity_id)) {
			throw new Nette\InvalidArgumentException('Argument should be numeric ID of the Entity, got ' . gettype($entity_id));
		}
		$this->postsMirror->delete($this->postsMirror->findOneBy(['id' => $entity_id]));
	}

}
