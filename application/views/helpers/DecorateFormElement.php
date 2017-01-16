<?php

class App_View_Helper_DecorateFormElement extends Zend_View_Helper_Abstract
{

    /**
     * @param Zend_Form_Element $element
     * @param bool $skipDecoration
     * @return Zend_Form_Element
     */
    public function decorateFormElement(Zend_Form_Element $element, $skipDecoration = false)
    {
        // Set input type classes
        $currentClass = $element->getAttrib('class');

        switch ($element->getType()) {

            case 'Zend_Form_Element_Checkbox':
            case 'Zend_Form_Element_Radio':
            case 'Zend_Form_Element_Submit':
            case 'Zend_Form_Element_Text':

                $explode = explode('_', $element->getType());
                $class = strtolower($explode[3]);

                $element->setAttrib('class', ($currentClass ? $currentClass . ' ' . $class : $class));
                break;

            case 'Zend_Form_Element_MultiCheckbox':

                $element->setAttrib('class', ($currentClass ? $currentClass . ' checkbox' : 'checkbox'));
                break;

            default:
                // No default
        }

        // Additional input classes
        if ($element->hasErrors()) {
            $currentClass = $element->getAttrib('class');
            $element->setAttrib('class', ($currentClass ? $currentClass . ' errored' : 'errored'));
        }

        // Additional element classes
        $additionalHtmlTagClass = '';

        if ($element->getType() === 'Zend_Form_Element_Hidden') {
            $additionalHtmlTagClass .= ' hidden';
        }

        // Set ID
        $element->setAttrib('id', $this->_nameToId($element->getFullyQualifiedName()));

        // Decorate element
        if (true === $skipDecoration) {
            return $this->_setDefaultDecorators($element);
        }

        if (in_array($element->getType(), array(
            'Zend_Form_Element_Hidden',
            'Zend_Form_Element_Hash',
        ))) {
            return $this->_setDefaultDecorators($element);
        }

        return $this->_setDecorators($element, $additionalHtmlTagClass);
    }

    /**
     * @param Zend_Form_Element $element
     * @return Zend_Form_Element
     */
    protected function _setDefaultDecorators(Zend_Form_Element $element)
    {
        $element->setDecorators(
            array(
                'ViewHelper',
                array(
                    'Errors',
                    array(
                        'elementStart' => '<div class="validation-error">',
                        'elementSeparator' => '</div><div class="validation-error">',
                        'elementEnd' => '</div>',
                        'placement' => Zend_Form_Decorator_Abstract::PREPEND,
                    ),
                ),
            )
        );

        return $element;
    }

    /**
     * @param Zend_Form_Element $element
     * @param string $additionalHtmlTagClass
     * @return Zend_Form_Element
     */
    protected function _setDecorators(Zend_Form_Element $element, $additionalHtmlTagClass = '')
    {
        $viewHelper = 'ViewHelper';

        if ($element->getType() === 'Zend_Form_Element_File') {
            $viewHelper = 'File';
        }

        $element->setDecorators(
            array(
                $viewHelper,
                array(
                    array(
                        'input-wrapper' => 'HtmlTag',
                    ),
                    array(
                        'tag' => 'div',
                        'class' => 'form-inputs line-up',
                    )
                ),
                array(
                    'Errors',
                    array(
                        'elementStart' => '<div class="validation-error line-up">',
                        'elementSeparator' => '</div><div class="validation-error line-up">',
                        'elementEnd' => '</div>',
                        'placement' => Zend_Form_Decorator_Abstract::PREPEND,
                    ),
                ),
                array(
                    'Description',
                    array(
                        'tag' => 'div',
                        'class' => 'form-tip line-up',
                        'escape' => false,
                    ),
                ),
                array(
                    array(
                        'clear' => 'HtmlTag',
                    ),
                    array(
                        'tag' => 'div',
                        'class' => 'clear',
                        'placement' => Zend_Form_Decorator_Abstract::APPEND,
                    ),
                ),
                array(
                    'Label',
                    array(
                        'class' => 'lined-up',
                        'requiredSuffix' => '<img src="/images/required_star.png" class="required-star" alt="Required">',
                        'escape' => false,
                    ),
                ),
                array(
                    array(
                        'wrapper' => 'HtmlTag',
                    ),
                    array(
                        'tag' => 'div',
                        'id' => $this->_nameToId($element->getFullyQualifiedName()) . '-element',
                        'class' => 'form-element' . $additionalHtmlTagClass,
                    ),
                ),
            )
        );

        return $element;
    }

    /**
     * @param string $name
     * @return string
     */
    protected function _nameToId($name)
    {
        $name = str_replace(array('[', ']'), '-', $name);
        $name = str_replace('--', '-', $name);
        $name = rtrim($name, '-');
        $name = strtolower(Zend_Filter::filterStatic($name, 'Word_CamelCaseToDash'));

        return $name;
    }

}
