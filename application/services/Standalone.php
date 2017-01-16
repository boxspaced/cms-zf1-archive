<?php

class App_Service_Standalone
{

    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_adapter;

    /**
     * @var Zend_Auth
     */
    protected $_auth;

    /**
     * @var App_Domain_User
     */
    protected $_user;

    /**
     * @var \Boxspaced\EntityManager\EntityManager
     */
    protected $_entityManager;

    /**
     * @var App_Domain_Repository_User
     */
    protected $_userRepository;

    /**
     * @var App_Service_Assembler_Standalone
     */
    protected $_dtoAssembler;

    /**
     * @param Zend_Db_Adapter_Abstract $adapter
     * @param Zend_Auth $auth
     * @param \Boxspaced\EntityManager\EntityManager $entityManager
     * @param App_Domain_Repository_User $userRepository
     * @param App_Service_Assembler_Standalone $dtoAssembler
     */
    public function __construct(
        Zend_Db_Adapter_Abstract $adapter,
        Zend_Auth $auth,
        \Boxspaced\EntityManager\EntityManager $entityManager,
        App_Domain_Repository_User $userRepository,
        App_Service_Assembler_Standalone $dtoAssembler
    )
    {
        $this->_adapter = $adapter;
        $this->_auth = $auth;
        $this->_entityManager = $entityManager;
        $this->_userRepository = $userRepository;
        $this->_dtoAssembler = $dtoAssembler;

        if ($this->_auth->hasIdentity()) {
            $identity = $auth->getIdentity();
            $this->_user = $userRepository->getById($identity->id);
        }
    }

    /**
     * @param int $offset
     * @param int $showPerPage
     * @return App_Service_Dto_StandaloneContent[]
     */
    public function getPublishedStandalone($offset = null, $showPerPage = null)
    {
        $sql = 'SELECT everything.* '
             . $this->_createPublishedFromSql()
             . ' ORDER BY name ASC';

        if (!is_null($offset) && !is_null($showPerPage)) {
            $sql .= sprintf(' LIMIT %d, %d', $offset, $showPerPage);
        }

        $stmt = $this->_adapter->query($sql, array(
            ':offset' => $offset,
            ':showPerPage' => $showPerPage,
        ));

        $results = $stmt->fetchAll();

        $standaloneContent = array();

        foreach ($results as $result) {

            $content = $this->_entityManager->find($result['type'], $result['id']);
            $assemblableContent = $this->_getAssemblableContent($content);

            $standaloneContent[] = $this->_dtoAssembler->assembleContentDto($assemblableContent);
        }

        return $standaloneContent;
    }

    /**
     * @return int
     */
    public function countPublishedStandalone()
    {
        $sql = 'SELECT COUNT(*) AS total ' . $this->_createPublishedFromSql();

        $stmt = $this->_adapter->query($sql);

        return (int) $stmt->fetchColumn();
    }

    /**
     * @return string
     */
    protected function _createPublishedFromSql()
    {
        return "
            FROM (
                SELECT
                    'App_Domain_Item' AS type,
                    item.id,
                    route.slug AS name
                FROM item
                INNER JOIN route ON route.id = item.route_id
                WHERE
                    published_to = 'Standalone'
                    AND status = 'PUBLISHED'
            ) AS everything
        ";
    }

    /**
     * @param mixed $content
     * @return App_Service_Standalone_AssemblableContentInterface
     */
    protected function _getAssemblableContent($content)
    {
        $class = get_class($content);
        $type = array_pop(explode('_', $class));

        $adapter = 'App_Domain_Adapter_AssemblableStandalone' . $type;

        return new $adapter($content);
    }

}
