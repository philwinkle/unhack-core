<?php

namespace Philwinkle;

class Generator
{
    /** @var \SimpleXmlElement */
    protected $_configXml;

    public function run($hacks)
    {
        foreach ($hacks as $className => $hacksForClass) {
            foreach ($hacksForClass as $hack) {
                $this->_updateXml($hack);
            }
        }

        $this->_writeXml();
        $this->_writeRewrites($hacks);
    }

    /**
     * @param $hack CoreHack
     */
    protected function _updateXml($hack)
    {
        if (! isset($this->_configXml)) {
            $path = __DIR__ . '/template/config.xml.tmpl';
            $this->_configXml = simplexml_load_file($path);
        }

        if (in_array($hack->type, array('block', 'model', 'helper'))) {
            $this->_updateXmlBlockModelHelper($hack);
        } elseif ($hack->type == 'controller') {
            // TBD
        }
    }

    /**
     * <blocks>
     *     <newsletter>
     *         <rewrite>
     *             <subscribe>Migrated_FromCore_Block_Subscribe</subscribe>
     *         </rewrite>
     *     </newsletter>
     * </blocks>
     *
     * @param $hack CoreHack
     */
    protected function _updateXmlBlockModelHelper($hack)
    {
        $typePlural = $hack->type . "s";
        $typeUppercase = uc_words($hack->type);

        /** @var \SimpleXmlElement $node */
        $node = $this->_configXml->global->$typePlural;
        $generatedClassName = "Migrated_FromCore_" . $typeUppercase . "_" . $hack->classNameSuffix;

        list($moduleAlias, $classAlias) = explode("/", $hack->shortCode);
        $moduleElement = isset($node->$moduleAlias) ? $node->$moduleAlias : $node->addChild($moduleAlias);
        $rewriteElement = isset($moduleElement->rewrite) ? $moduleElement->rewrite : $moduleElement->addChild("rewrite");

        if (!isset($rewriteElement->$classAlias)) {
            $rewriteElement->addChild($classAlias, $generatedClassName);
        }
    }

    protected function _writeXml()
    {  
        // Write app/etc/modules/ file
        $templateFilepath = __DIR__ . '/template/Migrated_FromCore.xml';
        $moduleRegistrationDir = \Mage::getBaseDir('app') . '/etc/modules';
        $moduleRegistrationFilepath = $moduleRegistrationDir . '/Migrated_FromCore.xml';

        copy($templateFilepath, $moduleRegistrationFilepath);
        echo "Writing $moduleRegistrationFilepath\r\n";

        // Write etc/config.xml
        $directory = \Mage::getBaseDir('app') . '/code/local/Migrated/FromCore/etc';
        $filePath = $directory . "/config.xml";
        echo "Writing $filePath\r\n";

        mkdir($directory, 0755, true);
        $this->_configXml->asXML($filePath);
    }

    protected function _writeRewrites($hacks)
    {
        foreach ($hacks as $className => $hacksForClass) {
            // shame
            $functionsForClass = "";
            $typeUppercase = "";
            $templateFilepath = "";
            $hack = "";

            foreach ($hacksForClass as $hack) {
                /** @var $hack CoreHack */
                $typeUppercase = uc_words($hack->type);
                $templateFilepath = __DIR__ . "/template/$typeUppercase.php.tmpl";
                $functionsForClass .= ltrim($hack->methodSource) . "\r\n\r\n";
            }

            $templateContents = file_get_contents($templateFilepath);
            $generatedContent = sprintf(
                $templateContents,
                $hack->classNameSuffix,
                "extends " . $hack->className,
                $hack->methodSource
            );

            $generatedFileDirectory = \Mage::getBaseDir('app') . "/code/local/Migrated/FromCore/$typeUppercase";
            $generatedFilePath = $generatedFileDirectory . "/" . $hack->classNameSuffix . ".php";
            @mkdir($generatedFileDirectory, 0755, true);

            echo "Writing $generatedFilePath\r\n";
            file_put_contents($generatedFilePath, $generatedContent);
        }
    }
}