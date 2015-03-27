<?php

namespace App\Console;

use Doctrine;
use Entity;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BlogUpdate extends Command {

	/** @var \Kdyby\Doctrine\EntityManager @inject */
	public $em;

	protected function configure() {
		$this->setName('blog:update')->setDescription('Update database schema (set-up DB credentials in config.local.neon).');
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		try {
			$schemaTool = new Doctrine\ORM\Tools\SchemaTool($this->em);
			$schemaTool->updateSchema($this->em->getMetadataFactory()->getAllMetadata());
			$output->writeLn('<info>[OK] - BLOG:UPDATE</info>');
			return 0; // zero return code means everything is ok
		} catch (\Exception $exc) {
			$output->writeLn('<error>BLOG:UPDATE - ' . $exc->getMessage() . '</error>');
			return 1; // non-zero return code means error
		}
	}

}
