<?php

namespace App\Console;

use Doctrine;
use Entity;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BlogInstall extends Command {

	/** @var \Kdyby\Doctrine\EntityManager @inject */
	public $em;

	protected function configure() {
		$this->setName('blog:install')->setDescription('Install database schema (set-up DB credentials in config.local.neon).');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		try {
			$schemaTool = new Doctrine\ORM\Tools\SchemaTool($this->em);
			$schemaTool->createSchema($this->em->getMetadataFactory()->getAllMetadata());
			$post = new Entity\Post;
			$post->title = 'Vítejte na svém novém blogu!';
			$post->slug = 'vitejte-na-svem-novem-blogu';
			$post->body = 'Instalace proběhla úspěšně. Jupí! (-:';
			$post->date = new \DateTime;
			$post->publish_date = new \DateTime;
			$this->em->persist($post);
			$this->em->flush();
			$output->writeLn('<info>[OK] - BLOG:INSTALL</info>');
			return 0; // zero return code means everything is ok
		} catch (\Exception $exc) {
			$output->writeLn('<error>BLOG:INSTALL - ' . $exc->getMessage() . '</error>');
			return 1; // non-zero return code means error
		}
	}

}
