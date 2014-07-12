<?php

class PostsListener extends Nette\Object implements Kdyby\Events\Subscriber {

	private $postsMirror;

	public function __construct(App\PostsMirror $postsMirror) {
		$this->postsMirror = $postsMirror;
	}

	public function getSubscribedEvents() {
		return array(
			'App\Posts::onSave',
			'App\Posts::onDelete',
		);
	}

	public function onSave(Entity\Post $entity) {
		$this->postsMirror->save($entity);
	}

	public function onDelete(Entity\Post $entity) {
		$this->postsMirror->delete($entity);
	}

}
