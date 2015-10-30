<?php

namespace Philwinkle;

class Generator
{
    /** @var \SimpleXmlElement */
    protected $_configXml;

    public function run($hacks)
    {
        $this->_createConfigXml();

        foreach ($hacks as $className => $hacksForClass) {
            foreach ($hacksForClass as $hack) {
                $this->_runHack($hack);
            }
        }

        $this->_write();
    }

    /**
     * @param $hack CoreHack
     */
    protected function _runHack($hack)
    {
        $this->_updateXml($hack);
        // app/etc/modules/
        // detect type of hack
        // output the class to write directory
        // update config with stuff
    }

    protected function _createConfigXml()
    {
        $path = __DIR__ . '/template/config.xml.tmpl';
        $this->_configXml = simplexml_load_file($path);

        return $this;
    }

    /**
     * @param $hack CoreHack
     */
    protected function _updateXml($hack)
    {
        if (in_array($hack->type, array('block', 'model', 'helper'))) {
            $this->_updateXmlBlockModelHelper($hack);
        } elseif ($hack->type == 'controller') {

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
        $node->addChild($moduleAlias)
            ->addChild("rewrite")
            ->addChild($classAlias, $generatedClassName);
    }

    protected function _write()
    {
        $directory = \Mage::getBaseDir('app') . '/code/local/Migrated/FromCore/etc';
        $filePath = $directory . "/config.xml";
        echo "Writing $filePath\r\n";

        mkdir($directory, 0777, true);
        $this->_configXml->asXML($filePath);
    }
}