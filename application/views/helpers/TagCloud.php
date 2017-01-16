<?php

class App_View_Helper_TagCloud extends Zend_View_Helper_Abstract
{

    /**
     * @param App_Service_Dto_TagCloud $tagCloud
     * @return string
     */
    public function tagCloud(App_Service_Dto_TagCloud $tagCloud)
    {
        echo $this->view->partial('digital-gallery/_tag-cloud.phtml', array(
            'keywords' => $tagCloud->keywords,
        ));
    }

}
