<?php

namespace Philwinkle\Magento\Command\Core;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use KJ\Magento\Util\Comparison\Item;

class UnhackCommand extends \N98\Magento\Command\AbstractMagentoCommand
{
	protected $_version = null;

	protected function configure()
	{
		$this
			->setName('core:unhack')
			->setDescription('Unhack the core');
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$this->_input = $input;
		$this->_output = $output;

		$output->writeln("Do eeet");
	}
}