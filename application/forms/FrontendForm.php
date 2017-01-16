<?php

class App_Form_FrontendForm extends Zend_Form
{

    /**
     * @return void
     */
    public function init()
    {
        $this->_decorate();
        $this->setElementFilters(array('StringTrim', 'StripTags'));
        return parent::init();
    }

    /**
     * @return App_Form_FrontendForm
     */
    protected function _decorate()
    {
        foreach ($this->getElements() as $element) {

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
            $element->setDecorators(array(
                'Errors',
                'ViewHelper',
                array(
                    'Description',
                    array(
                        'tag' => 'span',
                        'class' => 'help-block',
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
                        'class' => 'main',
                        'requiredSuffix' => '<img src="/images/required_star.png" class="required-star" alt="Required">',
                        'escape' => false,
                    ),
                ),
                array(
                    array(
                        'wrapper' => 'HtmlTag',
                    ),
                    array(
                        'tag' => 'li',
                        'id' => $this->_nameToId($element->getFullyQualifiedName()) . '-element',
                        'class' => 'element' . $additionalHtmlTagClass,
                    ),
                ),
            ));
        }

        return $this;
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

        return strtolower(Zend_Filter::filterStatic($name, 'Word_CamelCaseToDash'));
    }

    /**
     *
     * @param array $multiOptions
     * @param string $key
     * @param string $value
     * @return array
     */
    protected function _prependEmptyMultiOption($multiOptions, $key = '', $value = '')
    {
        return array($key => $value) + $multiOptions;
    }

}
