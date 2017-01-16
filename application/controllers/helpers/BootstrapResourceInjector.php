<?php

/**
 * @todo rename Bootstrap and have other bootstrap helper methods here
 */
class Controller_Helper_BootstrapResourceInjector extends Zend_Controller_Action_Helper_Abstract
{

    /**
     * @todo rename injectResources
     * @return void
     */
    public function inject()
    {
        $bootstrap = $this->_actionController->getInvokeArg('bootstrap');
        $controllerProps = get_class_vars(get_class($this->_actionController));

        foreach ($controllerProps as $k => $v) {

            if ($bootstrap->hasResource($k)) {
                $this->_actionController->$k = $bootstrap->getResource($k);
            } elseif ($bootstrap->hasPluginResource($k)) {
                $this->_actionController->$k = $bootstrap->getPluginResource($k);
            }
        }
    }

    /**
     * @return void
     */
    public function direct()
    {
        return $this->inject();
    }

}
