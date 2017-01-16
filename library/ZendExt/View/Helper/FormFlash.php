<?php

class ZendExt_View_Helper_FormFlash extends Zend_View_Helper_FormElement
{

    /**
     * @param string $name
     * @param mixed $value
     * @param array $attribs
     * @return string
     */
    public function formFlash($name, $value = null, array $attribs = null)
    {
        $html = $this->view->formText($name, $value, $attribs);
        $html .= '<input type="button" class="flash-browse-button" value="Browse server..." />';
        return $html;
    }

}
