<?php

namespace Philwinkle\Magento\Command\Core;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Philwinkle\CoreHack;
use Philwinkle\Generator;

class UnhackCommand extends \N98\Magento\Command\AbstractMagentoCommand
{
	protected $_version = null;

	/** @var InputInterface */
	protected $_input;

	/** @var  OutputInterface */
	protected $_output;

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
			$this->_output->writeln("Unhacking " . $filePath . ":" . $lineNumber);
			$file = new \SplFileObject($filePath, "r");
			$coreHack = new CoreHack($file, $lineNumber);
			$hacks[$coreHack->className][] = $coreHack;
		}

		(new Generator)->run($hacks);
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

			if($fileName === 'index.php') {
				continue;
			}

			$results[(int)$parts[1]] = $fileName;
		}

		return $results;
	}
}