<?php

class ZendExt_View_Helper_FormLink extends Zend_View_Helper_FormElement
{

    /**
     * @param string $name
     * @param mixed $value
     * @param array $attribs
     * @return string
     */
    public function formLink($name, $value = null, array $attribs = null)
    {
        $html = $this->view->formText($name, $value, $attribs);
        $html .= '<input type="button" class="link-browse-button" value="Browse server..." />';
        return $html;
    }

}
