<?php

/**
 * @todo merge into Navigation helper and rename all methods as appropriate
 */
class Controller_Helper_AdminControls extends Zend_Controller_Action_Helper_Abstract
{

    /**
     * @var Zend_Auth
     */
    protected $_auth;

    /**
     * @var App_Acl_Acl
     */
    protected $_acl;

    /**
     * @var Zend_View
     */
    protected $_view;

    /**
     * @var Zend_Config
     */
    protected $_config;

    /**
     * @return void
     */
    public function init()
    {
        $front = Zend_Controller_Front::getInstance();
        $bootstrap = $front->getParam('bootstrap');
        $container = $bootstrap->getContainer();
        $controller = $this->getActionController();

        $this->_view = $controller->view;
        $this->_auth = $container['Auth'];
        $this->_acl = $container['Acl'];
        $this->_config = $bootstrap->getResource('config');
    }

    /**
     * @todo Rename createFrontendAdminControl
     * @param DateTime $liveFrom
     * @param DateTime $expiresEnd
     * @param string $typeName
     * @return string
     */
    public function createFrontendControl(DateTime $liveFrom, DateTime $expiresEnd, $typeName)
    {
        if ($this->_auth->hasIdentity()) {

            $identity = $this->_auth->getIdentity();
            $moduleName = $this->getRequest()->getModuleName();
            $controllerName = $this->getRequest()->getControllerName();

            $scheme = $this->getRequest()->getScheme();
            $host = $this->getRequest()->getHttpHost();

            $requestUri = $this->getRequest()->getRequestUri();
            $from = urlencode($scheme . '://' . $host . $requestUri);

            $lifespanState = $this->calcLifeSpanState($liveFrom, $expiresEnd);
            $lifespanTitle = $this->calcLifeSpanTitle($liveFrom, $expiresEnd);

            return $this->_view->partial(
                'shared/_frontend-control.phtml',
                array(
                    'id' => $this->getRequest()->getParam('id'),
                    'host' => $this->_config->settings->host,
                    'secureHost' => $this->_config->settings->secureHost,
                    'controllerName' => $controllerName,
                    'from' => $from,
                    'lifespanState' => $lifespanState,
                    'lifespanTitle' => $lifespanTitle,
                    'typeName' => $typeName,
                    'allowEdit' => $this->_acl->isAllowedMultiRoles(
                        (array) $identity->roles,
                        $moduleName . ':' . $controllerName,
                        'edit'
                    ),
                    'allowPublish' => $this->_acl->isAllowedMultiRoles(
                        (array) $identity->roles,
                        $moduleName . ':' . $controllerName,
                        'publish'
                    ),
                    'allowDelete' => $this->_acl->isAllowedMultiRoles(
                        (array) $identity->roles,
                        $moduleName . ':' . $controllerName,
                        'delete'
                    ),
                )
            );
        }
    }

    /**
     * @param string $moduleName
     * @param string $pageName
     * @param int $id
     * @param bool $hasBlocks
     * @return string
     */
    public function createFrontendModulePageControl(
        $moduleName,
        $pageName,
        $id,
        $hasBlocks = false
    )
    {
        if ($this->_auth->hasIdentity()) {

            $identity = $this->_auth->getIdentity();

            $canPublish = $this->_acl->isAllowedMultiRoles(
                (array) $identity->roles,
                'default:' . $moduleName,
                'publish'
            );

            $allowPublish = ($hasBlocks && $canPublish);

            return $this->_view->partial(
                'shared/_frontend-module-page-control.phtml',
                array(
                    'id' => $id,
                    'secureHost' => $this->_config->settings->secureHost,
                    'controllerName' => $moduleName,
                    'moduleName' => $moduleName,
                    'pageName' => $pageName,
                    'allowPublish' => $allowPublish,
                )
            );
        }
    }

    /**
     * @param bool $new
     * @return string
     */
    public function createAdminMenuControl($new = false)
    {
        if ($this->_auth->hasIdentity()) {

            $identity = $this->_auth->getIdentity();

            $digitalGallery = $this->_acl->isAllowedMultiRoles(
                (array) $identity->roles,
                'default:digital-gallery',
                'manage'
            );

            $course = $this->_acl->isAllowedMultiRoles(
                (array) $identity->roles,
                'default:course',
                'manage'
            );

            $whatsOn = $this->_acl->isAllowedMultiRoles(
                (array) $identity->roles,
                'default:whats-on',
                'manage'
            );

            $allowedModules = array();
            if ($digitalGallery) {
                $allowedModules[] = 'digital-gallery';
            }
            if ($course) {
                $allowedModules[] = 'course';
            }
            if ($whatsOn) {
                $allowedModules[] = 'whats-on';
            }

            $partial = '_admin-menu-control';
            if ($new) {
                $partial = '_new-admin-menu-control';
            }

            return $this->_view->partial(
                'shared/' . $partial . '.phtml',
                array(
                    'secureHost' => $this->_config->settings->secureHost,
                    'manageableModules' => $allowedModules,
                    'allowViewAuthoring' => $this->_acl->isAllowedMultiRoles(
                        (array) $identity->roles,
                        'default:workflow',
                        'authoring'
                    ),
                    'allowViewPublishing' => $this->_acl->isAllowedMultiRoles(
                        (array) $identity->roles,
                        'default:workflow',
                        'publishing'
                    ),
                    'allowManageAssets' => $this->_acl->isAllowedMultiRoles(
                        (array) $identity->roles,
                        'default:asset',
                        'index'
                    ),
                )
            );
        }
    }

    /**
     * @param int $beneathMenuItemId
     * @return string
     */
    public function createCreateInMenuDropdown($beneathMenuItemId = 0)
    {
        $identity = $this->_auth->getIdentity();
        $identityRoles = isset($identity->roles) ? $identity->roles : array();

        return $this->_view->partial(
            'menu/_create-in-menu-dropdown.phtml',
            array(
                'beneathMenuItemId' => $beneathMenuItemId,
                'allowCreateItem' => $this->_acl->isAllowedMultiRoles(
                    $identityRoles,
                    'default:item',
                    'create'
                ),
                'allowCreateForm' => $this->_acl->isAllowedMultiRoles(
                    $identityRoles,
                    'default:form',
                    'create'
                ),
                'allowCreateContainer' => $this->_acl->isAllowedMultiRoles(
                    $identityRoles,
                    'default:container',
                    'create'
                ),
            )
        );
    }

    /**
     * @return string
     */
    public function createCreateInWorkflowDropdown()
    {
        $identity = $this->_auth->getIdentity();
        $identityRoles = isset($identity->roles) ? $identity->roles : array();

        return $this->_view->partial(
            'workflow/_create-dropdown.phtml',
            array(
                'allowCreateItem' => $this->_acl->isAllowedMultiRoles(
                    $identityRoles,
                    'default:item',
                    'create'
                ),
                'allowCreateForm' => $this->_acl->isAllowedMultiRoles(
                    $identityRoles,
                    'default:form',
                    'create'
                ),
                'allowCreateContainer' => $this->_acl->isAllowedMultiRoles(
                    $identityRoles,
                    'default:container',
                    'create'
                ),
                'allowCreateBlock' => $this->_acl->isAllowedMultiRoles(
                    $identityRoles,
                    'default:block',
                    'create'
                ),
            )
        );
    }

    /**
     * @return string
     */
    public function createCreateStandaloneDropdown()
    {
        $identity = $this->_auth->getIdentity();
        $identityRoles = isset($identity->roles) ? $identity->roles : array();

        return $this->_view->partial(
            'standalone/_create-dropdown.phtml',
            array(
                'allowCreateItem' => $this->_acl->isAllowedMultiRoles(
                    $identityRoles,
                    'default:item',
                    'create'
                ),
                'allowCreateForm' => $this->_acl->isAllowedMultiRoles(
                    $identityRoles,
                    'default:form',
                    'create'
                ),
                'allowCreateContainer' => $this->_acl->isAllowedMultiRoles(
                    $identityRoles,
                    'default:container',
                    'create'
                ),
            )
        );
    }

    /**
     * @param DateTime $liveFrom
     * @param DateTime $expiresEnd
     * @return string
     */
    public function calcLifeSpanState(DateTime $liveFrom, DateTime $expiresEnd)
    {
        $now = new DateTime();

        if ($liveFrom < $now && $expiresEnd > $now) {
            return 'on';
        }

        return 'off';
    }

    /**
     * @param DateTime $liveFrom
     * @param DateTime $expiresEnd
     * @return string
     */
    public function calcLifeSpanTitle(DateTime $liveFrom, DateTime $expiresEnd)
    {
        $now = new DateTime();

        if ($liveFrom < $now && $expiresEnd > $now) {

            $lifespanTitle = 'Online - ';

            if ('2038-01-19' === $expiresEnd->format('Y-m-d')) {
                $lifespanTitle .= 'never expiring';
            } else {
                $lifespanTitle .= 'expires ' . $expiresEnd->format('j F Y');
            }

            return $lifespanTitle;
        }

        if ($liveFrom > $now) {
            return 'Offline - due to come online ' . $liveFrom->format('j F Y');
        }

        return 'Expired on ' . $expiresEnd->format('j F Y');
    }

}
