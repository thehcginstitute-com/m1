<?php

/**
 * Class IWD_AdminCheckout_Model_Conflicts
 */
class IWD_AdminCheckout_Model_Conflicts extends Mage_Core_Model_Abstract
{
    /**
     * @var array
     */
    protected $rewritesModules = array();

    /**
     * @return array
     */
    function getTypes()
    {
        return array(
            'model' => Mage::helper('iwd_admin_checkout')->__('Model'),
            'block' => Mage::helper('iwd_admin_checkout')->__('Block'),
            'helper' => Mage::helper('iwd_admin_checkout')->__('Helper'),
        );
    }

    /**
     * @return array
     */
    function getRewritesClasses()
    {
        $this->rewritesModules = array();

        foreach ($this->getTypes() as $type => $typeLabel) {
            $rewritesModules = $this->_collectRewrites($type);
            foreach ($rewritesModules as $base => $rewrites) {
                if (count($rewrites) > 1) {
                    foreach ($rewrites as $class) {
                        $pos = strpos($class, 'IWD_AdminCheckout');
                        if ($pos === 0) {
                            $this->rewritesModules[$base] = $rewrites;
                            break;
                        }
                    }
                }
            }
        }

        return $this->rewritesModules;
    }

    /**
     * @param $type
     * @return array
     */
    protected function _collectRewrites($type)
    {
        $rewritesModules = array();
        $moduleConfigBase = Mage::getModel('core/config_base');
        $moduleConfig = Mage::getModel('core/config_base');

        $modules = Mage::getConfig()->getNode('modules')->children();
        foreach ($modules as $moduleName => $moduleSettings) {
            if (!$moduleSettings->is('active')) {
                continue;
            }

            $configFile = Mage::getConfig()->getModuleDir('etc', $moduleName) . DS . 'config.xml';
            $moduleConfigBase->loadFile($configFile);

            $moduleConfig->loadString('<config/>');
            $moduleConfig->extend($moduleConfigBase, true);

            $nodeType = $moduleConfig->getNode()->global->{$type . 's'};
            if (!$nodeType) {
                continue;
            }

            $nodeTypeChildren = $nodeType->children();

            foreach ($nodeTypeChildren as $nodeName => $config) {
                if (!$config->rewrite) {
                    continue;
                }

                foreach ($config->rewrite->children() as $class => $newClass) {
                    $baseClass = $this->_getClassName($type, $nodeName, $class);
                    if (!isset($rewritesModules[$baseClass])
                        || (isset($rewritesModules[$baseClass]) && !in_array($newClass, $rewritesModules[$baseClass]))
                    ) {
                        if (strpos($newClass, 'IWD_All') === 0 || strpos($newClass, 'IWD_POS') === 0) {
                            continue;
                        }

                        $rewritesModules[$baseClass][] = $newClass;
                    }
                }
            }
        }

        return $rewritesModules;
    }

    /**
     * @param $type
     * @param $group
     * @param $class
     * @return string
     */
    protected function _getClassName($type, $group, $class)
    {
        $config = Mage::getConfig()->getNode()->global->{$type . 's'}->{$group};

        $className = (!empty($config)) ? $config->getClassName() : "";
        $className = (empty($className)) ? 'mage_' . $group . '_' . $type : $className;
        $className .= (!empty($class)) ? '_' . $class : $className;

        return uc_words($className);
    }
}
