<?php

class App_View_Helper_FlashMessages extends Zend_View_Helper_Abstract
{

    /**
     * @return string
     */
    public function flashMessages()
    {
        $flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');

        $flashMessages = $flashMessenger->getMessages();

        if ($flashMessenger->hasCurrentMessages()) {

            $flashMessages = array_merge(
                $flashMessages,
                $flashMessenger->getCurrentMessages()
            );

            $flashMessenger->clearCurrentMessages();
        }

        $messages = array();
        $html = '';

        if (count($flashMessages) > 0) {

            foreach ($flashMessages as $flashMessage) {

                if (!array_key_exists($flashMessage['status'], $messages)) {
                    $messages[$flashMessage['status']] = array();
                }

                array_push($messages[$flashMessage['status']], $this->view->translate($flashMessage['message']));
            }

            foreach ($messages as $status => $msgs) {

                $html = '<div class="message">';

                if ('success' !== $status) {
                    $html .= '<img src="/images/icons/cross.png" alt="Error icon" /> ';
                } else {
                    $html .= '<img src="/images/icons/check.png" alt="Success icon" /> ';
                }

                foreach ($msgs as $msg) {
                    $html .= $msg . '<br>';
                }

                $html .= '</div>';
            }

            return $html;
        }
    }

}
