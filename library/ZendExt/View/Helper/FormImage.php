<?php

class ZendExt_View_Helper_FormImage extends Zend_View_Helper_FormElement
{

    /**
     * @param string $name
     * @param mixed $value
     * @param array $attribs
     * @return string
     */
    public function formImage($name, $value = null, array $attribs = null)
    {
        $html = $this->view->formText($name, $value, $attribs);
        $html .= '<input type="button" class="image-browse-button" value="Browse server..." />';
        return $html;
    }

}
