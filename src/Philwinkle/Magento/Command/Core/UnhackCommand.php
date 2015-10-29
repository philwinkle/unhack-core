<?php

namespace Philwinkle\Magento\Command\Core;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Philwinkle\Detect;

class UnhackCommand extends \N98\Magento\Command\AbstractMagentoCommand
{
	protected $_version = null;

	/** @var InputInterface */
	protected $_input;

	protected function configure()
	{
		$this->setName('core:unhack')
			->addArgument('files', InputArgument::REQUIRED, 'The list of file names and line numbers')
			->setDescription('Unhack the core');
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$this->_input = $input;
		$this->_output = $output;

		$this->detectMagento($output);
		$this->initMagento();

		$lines = $this->_parseLines();
		foreach ($lines as $lineNumber => $filePath) {
			$file = new \SplFileObject($filePath, "r");
			$detect = new Detect($file, $lineNumber);
			print_r($detect);
		}
	}

	protected function _parseLines()
	{
		$input = $this->_input->getArgument('files');
		$lines = explode("\r\n", $input);

		$results = array();
		foreach ($lines as $line) {
			$parts = explode(":", $line);
			if (count($parts) != 2) {
				continue;
			}

			$results[$parts[1]] = $parts[0];
		}

		return $results;
	}
}