<?php

class App_Acl_Acl extends Zend_Acl
{

    /**
     * @param Zend_Auth $auth
     */
    public function __construct(Zend_Auth $auth)
    {
        // Resources
        $this->add(new Zend_Acl_Resource('default:index'));
        $this->add(new Zend_Acl_Resource('default:error'));
        $this->add(new Zend_Acl_Resource('default:account'));
        $this->add(new Zend_Acl_Resource('default:block'));
        $this->add(new Zend_Acl_Resource('default:item'));
        $this->add(new Zend_Acl_Resource('default:container'));
        $this->add(new Zend_Acl_Resource('default:form'));
        $this->add(new Zend_Acl_Resource('default:menu'));
        $this->add(new Zend_Acl_Resource('default:standalone'));
        $this->add(new Zend_Acl_Resource('default:workflow'));
        $this->add(new Zend_Acl_Resource('default:digital-gallery'));
        $this->add(new Zend_Acl_Resource('default:course'));
        $this->add(new Zend_Acl_Resource('default:whats-on'));
        $this->add(new Zend_Acl_Resource('default:search'));
        $this->add(new Zend_Acl_Resource('default:asset'));
        $this->add(new Zend_Acl_Resource('default:helpdesk'));

        // Roles
        $this->addRole(new Zend_Acl_Role('guest'));
        $this->addRole(new Zend_Acl_Role('author'), 'guest');
        $this->addRole(new Zend_Acl_Role('publisher'), 'author');
        $this->addRole(new Zend_Acl_Role('digital-gallery-manager'), 'guest');
        $this->addRole(new Zend_Acl_Role('course-manager'), 'guest');
        $this->addRole(new Zend_Acl_Role('whats-on-manager'), 'guest');
        $this->addRole(new Zend_Acl_Role('asset-manager'), 'guest');
        $this->addRole(new Zend_Acl_Role('form-manager'), 'guest');
        $this->addRole(new Zend_Acl_Role('helpdesk-user'), 'guest');
        $this->addRole(new Zend_Acl_Role('helpdesk-manager'), 'helpdesk-user');
        $this->addRole(new Zend_Acl_Role('admin'), array(
            'publisher',
            'digital-gallery-manager',
            'course-manager',
            'whats-on-manager',
            'asset-manager',
            'form-manager',
            'helpdesk-manager',
        ));

        // Access rules
        $this->deny();
        $this->allow('guest', 'default:index');
        $this->allow('guest', 'default:error');
        $this->allow('guest', 'default:account', array('login', 'logout'));
        $this->allow('guest', 'default:item', array('index'));
        $this->allow('guest', 'default:digital-gallery', array('search', 'results', 'index', 'add-to-basket', 'empty-basket', 'basket', 'download'));
        $this->allow('guest', 'default:course', array('search', 'results', 'index'));
        $this->allow('guest', 'default:whats-on', array('search', 'results', 'index'));
        $this->allow('guest', 'default:search', array('simple'));
        $this->allow('guest', 'default:form', array(
            'contact-us',
            'learning-enquiry',
            'it-contact',
            'stock-suggestion',
            'volunteer-application',
            'online-evaluation-for-partners',
            'school-work-experience-application',
            'placement-application',
            'teens-music-trivia-competition',
            'thank-you',
        ));

        $this->allow('author', 'default:account', array('index'));
        $this->allow('author', 'default:block', array('create', 'edit', 'index'));
        $this->allow('author', 'default:item', array('create', 'edit'));
        $this->allow('author', 'default:menu', array('index', 'internal-links'));
        $this->allow('author', 'default:workflow', array('authoring', 'authoring-delete'));
        $this->allow('author', 'default:standalone', array('index'));

        $this->allow('publisher', 'default:block', array('publish', 'delete', 'publish-update'));
        $this->allow('publisher', 'default:item', array('publish', 'delete', 'publish-update'));
        $this->allow('publisher', 'default:workflow', array('publishing', 'send-back', 'publishing-delete'));
        $this->allow('publisher', 'default:menu', array('shuffle'));

        $this->allow('digital-gallery-manager', 'default:account', array('index'));
        $this->allow('digital-gallery-manager', 'default:digital-gallery', array('manage', 'upload', 'edit', 'delete', 'categories',
            'create-category', 'edit-category', 'delete-category',
            'publish', 'import', 'reindex'));

        $this->allow('course-manager', 'default:account', array('index'));
        $this->allow('course-manager', 'default:course', array('manage', 'publish', 'import', 'reindex'));

        $this->allow('whats-on-manager', 'default:account', array('index'));
        $this->allow('whats-on-manager', 'default:whats-on', array('manage', 'publish', 'import', 'reindex'));

        $this->allow('asset-manager', 'default:account', array('index'));
        $this->allow('asset-manager', 'default:asset');

        $this->allow('form-manager', 'default:account', array('index'));
        $this->allow('form-manager', 'default:form', array('manage', 'publish'));

        $this->allow('helpdesk-user', 'default:account', array('index'));
        $this->allow('helpdesk-user', 'default:helpdesk', array('index', 'create-ticket', 'view-ticket', 'view-attachment'));
        $this->allow('helpdesk-manager', 'default:helpdesk', array('resolve-ticket'));
    }

    /**
     * @param string[] $roles
     * @param string $resource
     * @param string $action
     * @return bool
     */
    public function isAllowedMultiRoles(array $roles, $resource, $action)
    {
        foreach ($roles as $role) {

            if (parent::isAllowed($role, $resource, $action)) {
                return true;
            }
        }
        
        return false;
    }

}
