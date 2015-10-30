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
			->setDescription('Unhack the core');
	}

	public function execute(InputInterface $input, OutputInterface $output)
	{
		$hacks = [];
		$this->_input = $input;
		$this->_output = $output;

		$this->detectMagento($output);
		$this->initMagento();

		$lines = $this->_parseLines();
		foreach ($lines as $lineNumber => $filePath) {
			$file = new \SplFileObject($filePath, "r");
			$coreHack = new CoreHack($file, $lineNumber);
			$hacks[$coreHack->className][] = $coreHack;
		}
		$generator = Generator::run($hacks);
	}

	protected function _parseLines()
	{
		$results = array();
		while($line = fgets(STDIN)) {
			$parts = explode(":", $line);
			if (count($parts) != 2) {
				continue;
			}
			$fileName = trim($parts[0]);
			if (substr($fileName, -4) != ".php") {
				continue;
			}

			$results[(int)$parts[1]] = $fileName;
		}

		return $results;
	}
}