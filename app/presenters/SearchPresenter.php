<?php declare(strict_types = 1);

namespace App;

use Nette\Utils\Strings;

class SearchPresenter extends BasePresenter
{

	/** @var \Model\Posts @inject */
	public $posts;

	/** @var \Model\Tags @inject */
	public $tags;

	public function renderDefault($search)
	{
		//FIXME tagy ::: 'publish_date <=' => new \DateTime()
		$string = Strings::lower(Strings::normalize($search));
		$string = Strings::replace($string, '/[^\d\w]/u', ' ');
		$words = Strings::split(Strings::trim($string), '/\s+/u');
		$words = array_unique(array_filter($words, function ($word) {
			return Strings::length($word) > 1;
		}));
		$words = array_map(function ($word) {
			return Strings::toAscii($word);
		}, $words);
		$string = implode(' ', $words);

		$this->template->tag = $this->tags->findOneBy(['name' => $string]);
		$result = $this->posts->fulltextSearch($string);
		if (count($result) == 0) {
			$this->template->search = $search;
			$this->template->error = 'Nic nebylo nalezeno';
		} else {
			$this->template->search = $search;
			$this->template->result = $result;
		}
	}

}
