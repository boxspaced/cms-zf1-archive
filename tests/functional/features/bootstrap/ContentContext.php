<?php

use Behat\Behat\Context\BehatContext;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;

class ContentContext extends BehatContext
{

    /**
     * @return MinkContext
     */
    public function getMinkContext()
    {
        return $this->getMainContext()->getMinkContext();
    }

    /**
     * @return Session
     */
    public function getSession()
    {
        return $this->getMinkContext()->getSession();
    }

    /**
     * @return string
     */
    protected function getCurrentModule()
    {
        $url = parse_url($this->getSession()->getCurrentUrl());
        return explode('/', trim($url['path'], '/'))[0];
    }

    /**
     * @param string $name
     */
    public function visitContent($name)
    {
        $this->getMinkContext()->visitPage(sprintf('/%s', $name));
    }

    /**
     * @When /^I create (item|block):$/
     */
    public function create($module, TableNode $table)
    {
        $fields = $table->getRowsHash();

        if ($module == 'block') {
            $this->getMinkContext()->clickLink('Site');
            $this->getMinkContext()->clickLink('Blocks');
        } else {
            $this->getMinkContext()->clickLink('Workflow');
            $this->getMinkContext()->clickLink('Authoring');
        }

        $this->getMinkContext()->clickLink('Create new');
        $this->getMinkContext()->fillField('name', $fields['name']);
        $this->getMinkContext()->selectOption('typeId', $fields['type']);
        $this->getMinkContext()->pressButton('Create');
    }

    /**
     * @When /^I create and save (item|block):$/
     */
    public function createAndSave($module, TableNode $table)
    {
        $fields = $table->getRowsHash();

        $this->create($module, $table);
        $this->fillCurrentEditForm($fields);
        $this->getMinkContext()->pressButton('Save');
        $this->getMinkContext()->assertPageAddress('/workflow/authoring');
    }

    /**
     * @When /^I create and publish (item|block):$/
     */
    public function createAndPublish($module, TableNode $table)
    {
        $fields = $table->getRowsHash();

        $this->create($module, $table);
        $this->fillCurrentEditForm($fields);
        $this->getMinkContext()->pressButton('Publish');

        if ('author' === $this->getMainContext()->getLoggedInAs()) {

            $this->getMinkContext()->assertPageAddress('/workflow/authoring');
            return;
        }

        $this->fillCurrentPublishForm($fields);
        $this->getMinkContext()->pressButton('Publish');
        $this->getMinkContext()->assertPageAddress('block' === $module ? '/block' : $fields['name']);
    }

    /**
     * @When /^I create and publish (item) at top-level from menu manager:$/
     */
    public function createAndPublishAtTopLevelFromMenuManager($module, TableNode $table)
    {
        $fields = $table->getRowsHash();

        $this->getMinkContext()->visitPage('/menu');
        $this->getMinkContext()->clickLink('Create new at top level');
        $this->createAndPublishToProvisionalLocation($fields);
    }

    /**
     * @When /^I create and publish (item) beneath "([^"]*)" from menu manager:$/
     */
    public function createAndPublishBeneathFromMenuManager($module, $beneath, TableNode $table)
    {
        $fields = $table->getRowsHash();

        $this->getMinkContext()->visitPage('/menu');
        $row = $this->getMinkContext()->getElement('xpath', sprintf('//tr[contains(.,"%s")]', $beneath));
        $row->clickLink('Create new beneath');
        $this->createAndPublishToProvisionalLocation($fields, $beneath);
    }

    /**
     * @param string $type
     * @param string $name
     * @param string $shouldBeBeneath
     * @return void
     */
    protected function createAndPublishToProvisionalLocation(array $fields, $shouldBeBeneath = null)
    {
        $this->getMinkContext()->fillField('name', $fields['name']);
        $this->getMinkContext()->selectOption('typeId', $fields['type']);
        $this->getMinkContext()->pressButton('Create');
        $this->fillCurrentEditForm($fields);
        $this->getMinkContext()->pressButton('Publish');
        $this->getMinkContext()->assertCheckboxChecked('useProvisional');
        $this->getMinkContext()->assertPageContainsText($shouldBeBeneath ? "{$shouldBeBeneath} <-- Beneath" : 'Top level');
        $this->fillCurrentPublishForm($fields);
        $this->getMinkContext()->pressButton('Publish');
        $this->getMinkContext()->assertPageAddress($fields['name']);
    }

    /**
     * @When /^I create and preview (item) (content|publishing):$/
     */
    public function createAndPreview($module, $preview, TableNode $table)
    {
        $fields = $table->getRowsHash();

        $this->create($module, $table);
        $this->fillCurrentEditForm($fields);

        if ('content' === $preview) {

            $this->getMinkContext()->pressButton('Preview');
            $this->getMinkContext()->clickLink('testing-only');

        } else {

            $this->getMinkContext()->pressButton('Publish');
            $this->fillCurrentPublishForm($fields);
            $this->getMinkContext()->pressButton('Preview');
        }

        $this->switchToPreview($fields['name'], $preview);
    }

    /**
     * @param string $name
     * @param string $type
     * @return void
     */
    protected function switchToPreview($name, $type)
    {
        $this->getMinkContext()->switchToWindowWithNoName(1);
        $this->getMinkContext()->assertUrlContains(
            sprintf(
                '/%s?preview=%s',
                $name,
                $type
            )
        );
    }

    /**
     * @return void
     */
    protected function fillCurrentEditForm()
    {
        $module = $this->getCurrentModule();
        $title = $this->getSession()->getPage()->find('css', 'div.title');
        $type = $this->getTypeFromTitle($title->getText());

        $this->getMainContext()->removeWysiwygEditors();

        switch ($module) {
            case 'item':
                $this->fillItemEditForm($type);
                break;
            case 'block':
                $this->fillBlockEditForm($type);
                break;
            default:
                throw new InvalidArgumentException("Unknown module: {$module}");
        }
    }

    /**
     * @param string $title
     * @return string
     */
    protected function getTypeFromTitle($title)
    {
        $out = null;
        preg_match_all('/:\s(.*)\s-/', $title, $out);
        return isset($out[1][0]) ? $out[1][0] : null;
    }

    /**
     * @param string $type
     * @return void
     */
    protected function fillItemEditForm($type)
    {
        $page = $this->getSession()->getPage();
        $name = $page->find('css', 'div.title i')->getText();

        switch ($type) {

            case 'article':

                $navText = $page->findField('navText')->getValue();
                if (!$navText) {
                    $navText = sprintf('%s-v0-navText', $name);
                }

                $title = $page->findField('title')->getValue();
                if (!$title) {
                    $title = sprintf('%s-v0-title', $name);
                }

                $intro = $page->findField('parts[1][intro]')->getValue();
                if (!$intro) {
                    $intro = sprintf('%s-v0-intro', $name);
                }

                $body = $page->findField('parts[1][body]')->getValue();
                if (!$body) {
                    $body = sprintf('%s-v0-body', $name);
                }

                $this->getMinkContext()->fillField(
                    'navText',
                    $this->incrementVersion($navText)
                );
                $this->getMinkContext()->fillField(
                    'title',
                    $this->incrementVersion($title)
                );
                $this->getMinkContext()->fillField(
                    'parts[1][intro]',
                    $this->incrementVersion($intro)
                );
                $this->getMinkContext()->fillField(
                    'parts[1][body]',
                    $this->incrementVersion($body)
                );

                break;

            default:
                throw new InvalidArgumentException("Unknown type: {$type}");
        }
    }

    /**
     * @param string $string
     * @return string
     */
    protected function incrementVersion($string)
    {
        return preg_replace_callback('/v(\d+)/', function($matches) {
            return 'v' . ++$matches[1];
        }, $string);
    }

    /**
     * @param string $type
     * @return void
     */
    protected function fillBlockEditForm($type)
    {
        $page = $this->getSession()->getPage();
        $name = $page->find('css', 'div.title i')->getText();

        switch ($type) {

            case 'html':

                $html = $page->findField('fields[html]')->getValue();
                if (!$html) {
                    $html = sprintf('%s-v0-html', $name);
                }

                $this->getMinkContext()->fillField(
                    'fields[html]',
                    $this->incrementVersion($html)
                );

                break;

            default:
                throw new InvalidArgumentException("Unknown type: {$type}");
        }
    }

    /**
     * @return void
     */
    protected function fillCurrentPublishForm(array $fields = [])
    {
        $module = $this->getCurrentModule();

        switch ($module) {

            case 'item':
                $this->autoFillItemPublishForm($fields);
                break;
            case 'block':
                $this->autoFillBlockPublishForm($fields);
                break;
            default:
                throw new InvalidArgumentException("Unknown module: {$module}");
        }
    }

    /**
     * @return void
     */
    protected function autoFillItemPublishForm(array $fields = [])
    {
        $this->autoFillField('liveFrom', '2000-01-01 00:00:00', $fields);
        $this->autoFillField('expiresEnd', '2038-01-19 00:00:00', $fields);
        $this->autoFillField('name', '', $fields);
        $this->autoSelectOption('colourScheme', 'dark-blue', $fields);
        $this->autoSelectOption('templateId', 'testing-only', $fields);

        try {
            $this->getMinkContext()->assertCheckboxChecked('useProvisional');
        } catch (Exception $e) {

            $this->autoSelectOption('to', 'Standalone', $fields);

            if (isset($fields['to']) && 'Menu' === $fields['to']) {
                $this->autoSelectOption('beneathMenuItemId', 'Top level', $fields);
            }
        }

        foreach ($fields as $name => $value) {

            $position = preg_replace('/\d.*$/', '', $name);

            switch ($position) {

                case 'leftColumn':
                case 'rightColumn':

                    $add = $this->getMinkContext()->getElement(
                        'xpath',
                        sprintf(
                            '//a[contains(@onclick,"%s")]',
                            $position
                        )
                    );

                    $add->click();

                    $select = $this->getMinkContext()->getElement(
                        'xpath',
                        sprintf(
                            '//table[contains(@id,"%s")]//tr[last()]//select',
                            $position
                        )
                    );

                    $select->selectOption($value);
                    break;

                case 'mainImage':
                case 'lowerPromo':

                    $this->getMinkContext()->selectOption(
                        sprintf(
                            'free-blocks-%s-id',
                            strtolower(Zend_Filter::filterStatic($position, 'Word_CamelCaseToDash'))
                        ),
                        $value
                    );

                default:
                    // No default
            }
        }
    }

    /**
     * @param string $field
     * @param string $default
     * @param array $fields
     */
    protected function autoFillField($field, $default, array $fields = [])
    {
        $element = $this->getMinkContext()->getField($field);

        if ('' === $element->getValue()) {
            $this->getMinkContext()->fillField($field, $default);
        }

        if (isset($fields[$field])) {

            switch ($field) {

                case 'liveFrom':
                case 'expiresEnd':

                    $fields[$field] = (new DateTime($fields[$field]))->format('Y-m-d H:i:s');
                    break;

                default:
                    // No default
            }

            $this->getMinkContext()->fillField($field, $fields[$field]);
        }
    }

    /**
     * @param string $field
     * @param string $default
     * @param array $fields
     */
    protected function autoSelectOption($field, $default, array $fields = [])
    {
        $element = $this->getMinkContext()->getField($field);

        if ('' === $element->getValue()) {
            $this->getMinkContext()->selectOption($field, $default);
        }

        if (isset($fields[$field])) {
            $this->getMinkContext()->selectOption($field, $fields[$field]);
        }
    }

    /**
     * @return void
     */
    protected function autoFillBlockPublishForm(array $fields = [])
    {
        $this->autoFillField('liveFrom', '2000-01-01 00:00:00', $fields);
        $this->autoFillField('expiresEnd', '2038-01-19 00:00:00', $fields);
    }

    /**
     * @When /^I edit and save the (item|block) named "([^"]*)"$/
     * @When /^I edit and save the (item|block) named "([^"]*)":$/
     */
    public function editAndSave($module, $name, TableNode $table = null)
    {
        $fields = !is_null($table) ? $table->getRowsHash() : [];

        if ('block' === $module) {
            $this->followBlockManagerItemLink('Edit', $name);
        } else {
            $this->visitContent($name);
            $this->getMinkContext()->clickLink('Edit');
        }

        $this->fillCurrentEditForm($fields);
        $this->getMinkContext()->pressButton('Save');
        $this->getMinkContext()->assertPageAddress('/workflow/authoring');
    }

    /**
     * @When /^I edit and publish the (item|block) named "([^"]*)"$/
     * @When /^I edit and publish the (item|block) named "([^"]*)":$/
     */
    public function editAndPublish($module, $name, TableNode $table = null)
    {
        $fields = !is_null($table) ? $table->getRowsHash() : [];

        if ('block' === $module) {
            $this->followBlockManagerItemLink('Edit', $name);
            $finalPageAddress = '/block';
        } else {
            $this->visitContent($name);
            $this->getMinkContext()->clickLink('Edit');
            $finalPageAddress = $name;
        }

        $this->fillCurrentEditForm($fields);
        $this->getMinkContext()->pressButton('Publish');

        if ('publisher' === $this->getMainContext()->getLoggedInAs()) {
            $this->getMinkContext()->assertPageAddress($finalPageAddress);
        } else {
            $this->getMinkContext()->assertPageAddress('/workflow/authoring');
        }
    }

    /**
     * @When /^I edit and preview the (item) named "([^"]*)"$/
     * @When /^I edit and preview the (item) named "([^"]*)":$/
     */
    public function editAndPreview($module, $name, TableNode $table = null)
    {
        $fields = !is_null($table) ? $table->getRowsHash() : [];

        $this->visitContent($name);
        $this->getMinkContext()->clickLink('Edit');
        $this->fillCurrentEditForm($fields);
        $this->getMinkContext()->pressButton('Preview');
        $this->switchToPreview($name, 'content');
    }

    /**
     * @When /^I edit publishing and publish the (item|block) named "([^"]*)":$/
     */
    public function editPublishingAndPublish($module, $name, TableNode $table)
    {
        $fields = $table->getRowsHash();

        if ('block' === $module) {
            $this->followBlockManagerItemLink('Edit publishing', $name);
        } else {
            $this->editPublishing($name);
        }

        $this->fillCurrentPublishForm($fields);
        $this->getMinkContext()->pressButton('Publish');

        if ('block' === $module) {
            $this->getMinkContext()->assertPageAddress('/block');
        } else {
            $this->getMinkContext()->assertPageAddress(isset($fields['name']) ? $fields['name'] : $name);
            $this->getMinkContext()->assertPageNotContainsText('Page Not Found');
        }
    }

    /**
     * @When /^I edit publishing and preview the (item) named "([^"]*)":$/
     */
    public function editPublishingAndPreview($module, $name, TableNode $table)
    {
        $fields = $table->getRowsHash();

        $this->editPublishing($name);
        $this->fillCurrentPublishForm($fields);
        $this->getMinkContext()->pressButton('Preview');
        $this->switchToPreview($name, 'publishing');
    }

    /**
     * @param type $name
     * @return void
     */
    protected function editPublishing($name)
    {
        $this->visitContent($name);
        $this->getMinkContext()->clickLink('Edit publishing');
        $this->getMinkContext()->assertPageContainsText('Publish:');
    }

    /**
     * @todo delete content via frontend when possible
     *
     * @When /^I delete the item named "([^"]*)"$/
     */
    public function deleteContent($name)
    {
        try {
            $this->attemptDeleteFromMenuManager($name);
        } catch (Exception $e) {
            $this->getMinkContext()->clickLink('Standalone content');
            $this->getMinkContext()->clickLink('Delete');
            $this->getMinkContext()->pressButton('Confirm delete');
        }
    }

    /**
     * @When /^I delete the block named "([^"]*)"$/
     */
    public function deleteBlock($name)
    {
        $this->followBlockManagerItemLink('Delete', $name);
        $this->getMinkContext()->pressButton('Confirm delete');
    }

    /**
     * @When /^I attempt to delete the item named "([^"]*)"$/
     */
    public function attemptDeleteContent($name)
    {
        $this->visitContent($name);

        $this->getMinkContext()->getElement(
            'xpath',
            '//img[contains(@alt,"Dustbin icon")]'
        )->click();

        $page = $this->getSession()->getPage();

        if ($page->hasButton('Confirm delete')) {
            $page->pressButton('Confirm delete');
        }
    }

    /**
     * @When /^I edit and save the (item|block) named "([^"]*)" from my authoring workflow$/
     * @When /^I edit and save the (item|block) named "([^"]*)" from my authoring workflow:$/
     */
    public function editAndSaveFromAuthoringWorkflow($module, $name, TableNode $table = null)
    {
        $fields = !is_null($table) ? $table->getRowsHash() : [];

        $this->followWorkflowItemLink('authoring', 'Edit', $name);
        $this->fillCurrentEditForm($fields);
        $this->getMinkContext()->pressButton('Save');
        $this->getMinkContext()->assertPageAddress('/workflow/authoring');
    }

    /**
     * @When /^I edit and publish the (item|block) named "([^"]*)" from my authoring workflow$/
     * @When /^I edit and publish the (item|block) named "([^"]*)" from my authoring workflow:$/
     */
    public function editAndPublishFromAuthoringWorkflow($module, $name, TableNode $table = null)
    {
        $fields = !is_null($table) ? $table->getRowsHash() : [];

        $this->getMinkContext()->visitPage('/workflow/authoring');

        $row = $this->getMinkContext()->getElement('xpath', sprintf('//tr[contains(.,"%s")]', $name));

        $isUpdate = (1 === preg_match('/Version: Update/ui', $row->getText()));

        $row->clickLink('Edit');
        $this->fillCurrentEditForm($fields);
        $this->getMinkContext()->pressButton('Publish');

        if ('publisher' === $this->getMainContext()->getLoggedInAs() && !$isUpdate) {
            $this->fillCurrentPublishForm($fields);
            $this->getMinkContext()->pressButton('Publish');
        }

        if ('author' === $this->getMainContext()->getLoggedInAs() || $isUpdate) {
            $this->getMinkContext()->assertPageAddress('/workflow/authoring');
        } else {
            $this->getMinkContext()->assertPageAddress('block' === $module ? '/block' : $name);
        }
    }

    /**
     * @When /^I delete the item named "([^"]*)" from my authoring workflow$/
     * @When /^I delete the block named "([^"]*)" from my authoring workflow$/
     */
    public function deleteFromAuthoringWorkflow($name)
    {
        $this->followWorkflowItemLink('authoring', 'Delete', $name);
        $this->getMinkContext()->pressButton('Confirm delete');
    }

    /**
     * @When /^I delete the item named "([^"]*)" from my publishing workflow$/
     * @When /^I delete the block named "([^"]*)" from my publishing workflow$/
     */
    public function deleteFromPublishingWorkflow($name)
    {
        $this->followWorkflowItemLink('publishing', 'Delete', $name);
        $this->getMinkContext()->pressButton('Confirm delete');
    }

    /**
     * @When /^I send the item named "([^"]*)" back to author from my publishing workflow$/
     * @When /^I send the block named "([^"]*)" back to author from my publishing workflow$/
     */
    public function sendBackToAuthorFromPublishingWorkflow($name)
    {
        $this->followWorkflowItemLink('publishing', 'Send back to author', $name);
    }

    /**
     * @When /^I publish the item named "([^"]*)" from my publishing workflow$/
     * @When /^I publish the block named "([^"]*)" from my publishing workflow$/
     */
    public function publishFromPublishingWorkflow($name)
    {
        $this->getMinkContext()->visitPage('/workflow/publishing');

        $row = $this->getMinkContext()->getElement('xpath', sprintf('//tr[contains(.,"%s")]', $name));

        $text = $row->getText();
        $regex  = '/Version: Update/ui';

        if (preg_match($regex, $text)) {

            $row->clickLink('Publish');
            $this->getMinkContext()->pressButton('Confirm update');
            $this->getMinkContext()->assertPageContainsText('Update successful');

        } else {

            $row->clickLink('Go to publishing options');
            $this->fillCurrentPublishForm();
            $this->getMinkContext()->pressButton('Publish');
        }
    }

    /**
     * @When /^I preview the item named "([^"]*)" from my publishing workflow$/
     */
    public function previewFromPublishingWorkflow($name)
    {
        $this->getMinkContext()->visitPage('/workflow/publishing');

        $row = $this->getMinkContext()->getElement('xpath', sprintf('//tr[contains(.,"%s")]', $name));

        $text = $row->getText();
        $regex  = '/Version: Update/ui';

        if (preg_match($regex, $text)) {
            $link = $row->find('xpath', '//a[contains(.,"Preview")]');
            $link->click();
        } else {
            $row->pressButton('Preview');
            $this->getMinkContext()->clickLink('with testing-only template');
        }

        $this->switchToPreview($name, 'publishing');
    }

    /**
     * @param string $workflow
     * @param string $link
     * @param string $name
     * @return void
     */
    protected function followWorkflowItemLink($workflow, $link, $name)
    {
        $this->getMinkContext()->visitPage(sprintf('/workflow/%s', $workflow));
        $this->getMinkContext()->assertPageContainsText(sprintf('Index of: %s', ucfirst($workflow)));
        $row = $this->getMinkContext()->getElement('xpath', sprintf('//tr[contains(.,"%s")]', $name));
        $row->clickLink($link);
    }

    /**
     * @param string $link
     * @param string $name
     * @return void
     */
    protected function followBlockManagerItemLink($link, $name)
    {
        $this->getMinkContext()->visitPage('/block');
        $this->getMinkContext()->assertPageContainsText('Index of: blocks');
        $row = $this->getMinkContext()->getElement('xpath', sprintf('//tr[contains(.,"%s")]', $name));
        $row->clickLink($link);
    }

    /**
     * @When /^I shuffle the content named "([^"]*)" (up|down) from menu manager$/
     */
    public function shuffleFromMenuManager($name, $direction)
    {
        $this->getMinkContext()->visitPage('/menu');
        $row = $this->getMinkContext()->getElement('xpath', sprintf('//tr[contains(.,"%s")]', $name));
        $row->clickLink(sprintf('Shuffle %s', $direction));
        $this->getMinkContext()->assertPageContainsText('Item moved successfully');
    }

    /**
     * @When /^I attempt to shuffle the content named "([^"]*)" (up|down) from menu manager$/
     */
    public function attemptShuffleFromMenuManager($name, $direction)
    {
        $this->getMinkContext()->visitPage('/menu');

        $icon = $this->getMinkContext()->getElement(
            'xpath',
            sprintf('//tr[contains(.,"%s")]//span[contains(@class,"sprite-arrow-%s")]', $name, $direction)
        );

        $icon->click();
    }

    /**
     * @When /^I attempt to delete the item named "([^"]*)" from menu manager$/
     */
    public function attemptDeleteFromMenuManager($name)
    {
        $this->getMinkContext()->visitPage('/menu');

        $icon = $this->getMinkContext()->getElement(
            'xpath',
            sprintf('//tr[contains(.,"%s")]//span[contains(@class,"sprite-dustbin")]', $name)
        );

        $icon->click();

        $page = $this->getSession()->getPage();

        if ($page->hasButton('Confirm delete')) {
            $page->pressButton('Confirm delete');
        }
    }

    /**
     * @When /^I shuffle the block named "([^"]*)" (up|down) in the "([^"]*)" block sequence within the item named "([^"]*)"$/
     */
    public function shuffleBlockSequence($blockName, $direction, $sequenceName, $contentName)
    {
        $this->editPublishing($contentName);

        $icon = $this->getMinkContext()->getElement(
            'xpath',
            sprintf(
                '//table[contains(@id,"%s")]//select//option[@selected="selected"][contains(.,"%s")]/ancestor::tr//img[@title="Shuffle %s"]',
                $sequenceName,
                $blockName,
                $direction
            )
        );
        $icon->click();

        $this->getMinkContext()->pressButton('Publish');
    }

    /**
     * @When /^I change the block named "([^"]*)" to the block named "([^"]*)" in the "([^"]*)" block sequence within the item named "([^"]*)"$/
     */
    public function changeBlockInBlockSequence($fromBlockName, $toBlockName, $sequenceName, $contentName)
    {
        $this->editPublishing($contentName);

        $select = $this->getMinkContext()->getElement(
            'xpath',
            sprintf(
                '//table[contains(@id,"%s")]//select//option[@selected="selected"][contains(.,"%s")]/ancestor::select',
                $sequenceName,
                $fromBlockName
            )
        );
        $select->selectOption($toBlockName);

        $this->getMinkContext()->pressButton('Publish');
    }

    /**
     * @When /^I remove the block named "([^"]*)" from the "([^"]*)" block sequence within the item named "([^"]*)"$/
     */
    public function removeBlockFromBlockSequence($blockName, $sequenceName, $contentName)
    {
        $this->changeBlockInBlockSequence($blockName, '', $sequenceName, $contentName);
    }

    /**
     * @When /^I delete the block named "([^"]*)" from the "([^"]*)" block sequence within the item named "([^"]*)"$/
     */
    public function deleteBlockFromBlockSequence($blockName, $sequenceName, $contentName)
    {
        $this->editPublishing($contentName);

        $icon = $this->getMinkContext()->getElement(
            'xpath',
            sprintf(
                '//table[contains(@id,"%s")]//select//option[@selected="selected"][contains(.,"%s")]/ancestor::tr//img[@title="Delete"]',
                $sequenceName,
                $blockName
            )
        );
        $icon->click();

        $this->getMinkContext()->pressButton('Publish');
    }

    /**
     * @Then /^I should see in my (authoring|publishing) workflow:$/
     */
    public function assertWorkflowContains($workflow, TableNode $expected)
    {
        if ('authoring' === $workflow) {
            $this->getMainContext()->login('author');
        } else {
            $this->getMainContext()->login('publisher');
        }

        $this->getMinkContext()->visitPage(sprintf('/workflow/%s', $workflow));

        $this->getMinkContext()->assertPageContainsText(sprintf('Index of: %s', ucfirst($workflow)));

        $shouldSee = new TableNode();

        $header = ['name', 'type', 'version'];

        if ('authoring' === $workflow) {
            $header[] = 'stage';
        }

        $shouldSee->addRow($header);

        foreach ($expected->getHash() as $expectedRow) {

            $row = array(
                'name' => $expectedRow['name'],
                'type' => sprintf('Type: %s', $expectedRow['type']),
                'version' => sprintf('Version: %s', ucfirst($expectedRow['version'])),
            );

            if ($workflow == 'authoring') {
                $row['stage'] = sprintf('Workflow stage: %s', ucfirst($expectedRow['stage']));
            }

            $shouldSee->addRow($row);
        }

        $this->getMinkContext()->assertTableContains('css', 'table', $shouldSee);
    }

    /**
     * @Then /^I should not see in my (authoring|publishing) workflow:$/
     */
    public function assertWorkflowNotContains($workflow, TableNode $table)
    {
        if ('authoring' === $workflow) {
            $this->getMainContext()->login('author');
        } else {
            $this->getMainContext()->login('publisher');
        }

        $this->getMinkContext()->visitPage(sprintf('/workflow/%s', $workflow));

        $this->getMinkContext()->assertPageContainsText(sprintf('Index of: %s', ucfirst($workflow)));
        foreach ($table->getHash() as $row) {
            $this->getMinkContext()->assertPageNotContainsText($row['name']);
        }
    }

    /**
     * @Then /^I should not see the item named "([^"]*)" anywhere in workflow$/
     * @Then /^I should not see the block named "([^"]*)" anywhere in workflow$/
     */
    public function assertNotAnywhereInWorkflow($name)
    {
        $this->getMainContext()->login('author');
        $this->getMinkContext()->visitPage('/workflow/authoring');
        $this->getMinkContext()->assertPageContainsText('Index of: Authoring');
        $this->getMinkContext()->assertPageNotContainsText($name);

        $this->getMainContext()->login('publisher');
        $this->getMinkContext()->visitPage('/workflow/publishing');
        $this->getMinkContext()->assertPageContainsText('Index of: Publishing');
        $this->getMinkContext()->assertPageNotContainsText($name);
    }

    /**
     * @Then /^the (item|block) named "([^"]*)" should be published at version (\d+)$/
     */
    public function assertPublishedAtVersion($module, $name, $version)
    {
        if ('block' === $module) {
            $this->assertBlockPublishedAtVersion($name, $version);
        } else {
            $this->assertContentPublishedAtVersion($name, $version);
        }
    }

    /**
     * @param string $blockName
     * @param int $version
     * @return void
     */
    protected function assertBlockPublishedAtVersion($blockName, $version)
    {
        $this->getMainContext()->login('publisher');

        $this->getMinkContext()->visitPage('/block');
        $this->getMinkContext()->assertPageContainsText('Index of: blocks');
        $this->getMinkContext()->assertPageContainsText($blockName);

        $contentName = 'block-test-page';

        $fields = new TableNode();
        $fields->addRow(['name', $contentName]);
        $fields->addRow(['type', 'article']);
        $fields->addRow(['rightColumn', $blockName]);

        $this->createAndPublish('item', $fields);

        $this->getMainContext()->logout();
        $this->visitContent($contentName);
        $this->getMinkContext()->assertPageContainsText(sprintf('%s-v%d', $blockName, $version));
    }

    /**
     * @param string $name
     * @param int $version
     * @return void
     */
    protected function assertContentPublishedAtVersion($name, $version)
    {
        $this->visitContent($name);

        $this->getMinkContext()->assertPageNotContainsText('Page Not Found');
        $this->getMinkContext()->assertPageContainsText(sprintf('%s-v%d-title', $name, $version));
    }

    /**
     * @Then /^the item named "([^"]*)" should be published with the "([^"]*)" colour scheme$/
     */
    public function assertPublishedWithColourScheme($name, $colourScheme)
    {
        $this->visitContent($name);

        $this->getMinkContext()->assertPageNotContainsText('Page Not Found');
        $this->getMinkContext()->getElement(
            'xpath',
            sprintf('//link[@href="/css/%s-colour-scheme.css"]', $colourScheme)
        );
    }

    /**
     * @Then /^the item named "([^"]*)" should be published with the "([^"]*)" template$/
     */
    public function assertPublishedWithTemplate($name, $template)
    {
        $this->visitContent($name);

        $this->getMinkContext()->assertPageNotContainsText('Page Not Found');
        $this->getMinkContext()->getElement(
            'xpath',
            sprintf('//meta[@name="cms:template"][@content="%s"]', $template)
        );
    }

    /**
     * @Then /^the block named "([^"]*)" should not be available to assign$/
     */
    public function assertBlockNotAvailableToAssign($name)
    {
        $this->getMainContext()->login('publisher');

        $this->getMinkContext()->visitPage('/block');
        $this->getMinkContext()->assertPageContainsText('Index of: blocks');
        $this->getMinkContext()->assertPageNotContainsText($name);

        $contentName = 'does-not-matter-what-the-name-is';

        $fields = new TableNode();
        $fields->addRow(['name', $contentName]);
        $fields->addRow(['type', 'article']);
        $this->createAndPublish('item', $fields);

        $this->editPublishing($contentName);
        $this->getMinkContext()->clickLink('Add block');
        // @todo check block select options doesn't contain the block
        $this->getMinkContext()->assertPageNotContainsText($name);
    }

    /**
     * @Then /^the (item|block) named "([^"]*)" should not be published$/
     */
    public function assertNotPublished($module, $name)
    {
        if ('block' === $module) {
            $this->assertBlockNotPublished($name);
        } else {
            $this->assertContentNotPublished($name);
        }
    }

    /**
     * @param string $name
     */
    protected function assertContentNotPublished($name)
    {
        $this->visitContent($name);

        $this->getMinkContext()->assertPageContainsText('Page Not Found');
    }

    /**
     * @param string $blockName
     */
    protected function assertBlockNotPublished($blockName)
    {
        $this->getMainContext()->login('publisher');

        $contentName = 'block-test-page';

        $fields = new TableNode();
        $fields->addRow(['name', $contentName]);
        $fields->addRow(['type', 'article']);
        $fields->addRow(['rightColumn', $blockName]);

        $this->createAndPublish('item', $fields);

        $this->getMinkContext()->assertPageNotContainsText($blockName);
    }

    /**
     * @Then /^the (item) named "([^"]*)" should be previewing at version (\d+)$/
     */
    public function assertPreviewOfVersion($module, $name, $version)
    {
        $this->switchToPreview($name, '');

        $this->getMinkContext()->assertPageNotContainsText('Page Not Found');
        $this->getMinkContext()->assertPageContainsText(sprintf('%s-v%d-title', $name, $version));
    }

    /**
     * @Then /^the (item) named "([^"]*)" should be previewing with the "([^"]*)" colour scheme$/
     */
    public function assertPreviewingWithColourScheme($module, $name, $colourScheme)
    {
        $this->switchToPreview($name, '');

        $this->getMinkContext()->getElement(
            'xpath',
            sprintf('//link[@href="/css/%s-colour-scheme.css"]', $colourScheme)
        );
    }

    /**
     * @Then /^the (item) named "([^"]*)" should be previewing with the "([^"]*)" template$/
     */
    public function assertPreviewingWithTemplate($module, $name, $template)
    {
        $this->switchToPreview($name, '');

        $this->getMinkContext()->getElement(
            'xpath',
            sprintf('//meta[@name="cms:template"][@content="%s"]', $template)
        );
    }

    /**
     * @Then /^the item named "([^"]*)" should be sent back to the author$/
     * @Then /^the block named "([^"]*)" should be sent back to the author$/
     */
    public function assertSentBackToAuthor($name)
    {
        $this->getMainContext()->login('author');
        $this->getMinkContext()->visitPage('/workflow/authoring');
        $this->getMinkContext()->assertPageContainsText('Index of: Authoring');
        $this->getMinkContext()->assertPageContainsText($name);

        $this->getMainContext()->login('publisher');
        $this->getMinkContext()->visitPage('/workflow/publishing');
        $this->getMinkContext()->assertPageContainsText('Index of: Publishing');
        $this->getMinkContext()->assertPageNotContainsText($name);
    }

    /**
     * @Then /^the (block|item) named "([^"]*)" should have expired "([^"]*)"$/
     */
    public function assertExpired($module, $name, $date)
    {
        $date = new DateTime($date);
        $status = sprintf('Expired on %s', $date->format('j F Y'));

        if ('block' === $module) {
            $this->assertBlockLifespanStatusContains($name, $status);
        } else {
            $this->assertContentLifespanStatusContains($name, $status);
        }
    }

    /**
     * @Then /^the (block|item) named "([^"]*)" should be offline and due to come online "([^"]*)"$/
     */
    public function assertOfflineDueToComeOnline($module, $name, $date)
    {
        $date = new DateTime($date);
        $status = sprintf('Offline - due to come online %s', $date->format('j F Y'));

        if ('block' === $module) {
            $this->assertBlockLifespanStatusContains($name, $status);
        } else {
            $this->assertContentLifespanStatusContains($name, $status);
        }
    }

    /**
     * @Then /^the (block|item) named "([^"]*)" should be online and never expiring$/
     */
    public function assertOnlineNeverExpiring($module, $name)
    {
        $status = 'Online - never expiring';

        if ('block' === $module) {
            $this->assertBlockLifespanStatusContains($name, $status);
        } else {
            $this->assertContentLifespanStatusContains($name, $status);
        }
    }

    /**
     * @Then /^the (block|item) named "([^"]*)" should be online and expiring "([^"]*)"$/
     */
    public function assertOnlineDueToExpire($module, $name, $date)
    {
        $date = new DateTime($date);
        $status = sprintf('Online - expires %s', $date->format('j F Y'));

        if ('block' === $module) {
            $this->assertBlockLifespanStatusContains($name, $status);
        } else {
            $this->assertContentLifespanStatusContains($name, $status);
        }
    }

    /**
     * @param string $name
     * @param string $text
     */
    protected function assertContentLifespanStatusContains($name, $text)
    {
        $this->getMainContext()->login('publisher');

        $this->visitContent($name);

        $this->getMinkContext()->assertElementAttributeContains(
            'css',
            '#admin-control img:nth-child(1)',
            'title',
            $text
        );
    }

    /**
     * @param string $name
     * @param string $text
     */
    protected function assertBlockLifespanStatusContains($name, $text)
    {
        $this->getMainContext()->login('publisher');

        $this->getMinkContext()->visitPage('/block');

        $this->getMinkContext()->assertElementAttributeContains(
            'xpath',
            sprintf('//tr[contains(.,"%s")]//span[contains(@class,"sprite-lightbulb")]', $name),
            'title',
            $text
        );
    }

    /**
     * @Then /^I should not see "([^"]*)" anywhere in system$/
     */
    public function assertNotSeeAnywhereInSystem($name)
    {
        $this->getMainContext()->login('author');
        $this->getMinkContext()->visitPage('/workflow/authoring');
        $this->getMinkContext()->assertPageContainsText('Index of: Authoring');
        $this->getMinkContext()->assertPageNotContainsText($name);

        $this->getMainContext()->login('publisher');
        $this->getMinkContext()->visitPage('/workflow/publishing');
        $this->getMinkContext()->assertPageContainsText('Index of: Publishing');
        $this->getMinkContext()->assertPageNotContainsText($name);

        $this->getMinkContext()->visitPage('/standalone');
        $this->getMinkContext()->assertPageContainsText('Index of: standalone content');
        $this->getMinkContext()->assertPageNotContainsText($name);

        $this->getMinkContext()->visitPage('/menu');
        $this->getMinkContext()->assertPageContainsText('Index of: menu items');
        $this->getMinkContext()->assertPageNotContainsText($name);

        $this->getMinkContext()->visitPage('/block');
        $this->getMinkContext()->assertPageContainsText('Index of: blocks');
        $this->getMinkContext()->assertPageNotContainsText($name);

        $this->getMainContext()->logout();
        $this->visitContent($name);
        $this->getMinkContext()->assertPageContainsText('Page Not Found');
    }

    /**
     * @Then /^I should see the content named "([^"]*)" in the top-level menu$/
     */
    public function assertInTopLevelMenu($name)
    {
        $this->getMinkContext()->iAmOnHomepage();
        $locator = sprintf('//*[@id="main-nav"]//a[@href="/%s"]', $name);
        $this->getMinkContext()->getElement('xpath', $locator)->click();

        $this->getMinkContext()->assertPageAddress($name);
        $this->assertContentPublishedAtVersion($name, 1);
    }

    /**
     * @Then /^I should see the content named "([^"]*)" in the sub menu of the content named "([^"]*)"$/
     */
    public function assertInSubMenu($name, $of)
    {
        $this->getMinkContext()->visitPage($of);

        $locator = sprintf('//*[@id="sub-nav"]//a[@href="/%s"]', $name);
        $this->getMinkContext()->getElement('xpath', $locator)->click();

        $this->getMinkContext()->assertPageAddress($name);
        $this->assertContentPublishedAtVersion($name, 1);

        // Go back to parent via breadcrumbs
        $locator = sprintf('//*[@id="breadcrumbs"]//a[@href="/%s"]', $of);
        $this->getMinkContext()->getElement('xpath', $locator)->click();

        $this->getMinkContext()->assertPageAddress($of);
    }

    /**
     * @Then /^I should see the content named "([^"]*)" (before|after) the content named "([^"]*)" in the top-level menu$/
     */
    public function assertPositionInTopLevelMenu($name, $position, $compare)
    {
        $this->getMinkContext()->iAmOnHomepage();

        $this->getMinkContext()->assertPositionInCollectionOfElements(
            'css',
            '#main-nav li a',
            "/$name",
            $position,
            "/$compare",
            'href'
        );
    }

    /**
     * @Then /^I should see the content named "([^"]*)" (before|after) the content named "([^"]*)" in the sub menu of the content named "([^"]*)"$/
     */
    public function assertPositionInSubMenu($name, $position, $compare, $of)
    {
        $this->getMinkContext()->visitPage($of);

        $this->getMinkContext()->assertPositionInCollectionOfElements(
            'css',
            '#sub-nav li a',
            "/$name",
            $position,
            "/$compare",
            'href'
        );
    }

    /**
     * @Then /^I should not be able to publish a new "([^"]*)" (item) to menu beneath content named "([^"]*)" from menu manager$/
     */
    public function assertCannotPublishNewContentBeneathFromMenuManager($type, $module, $beneath)
    {
        $this->getMainContext()->login('publisher');

        $this->getMinkContext()->visitPage('/menu');

        $this->getMinkContext()->assertElementNotContainsText("#{$beneath}", 'Create new beneath');
    }

    /**
     * @Then /^I should not be able to publish a new "([^"]*)" (item) to menu beneath content named "([^"]*)" from workflow$/
     */
    public function assertCannotPublishNewContentBeneath($type, $module, $beneath)
    {
        $this->getMainContext()->login('publisher');

        $fields = new TableNode();
        $fields->addRow(['name', 'does-not-matter-what-the-name-is']);
        $fields->addRow(['type', $type]);
        $this->create($module, $fields);

        $this->fillCurrentEditForm();
        $this->getMinkContext()->pressButton('Publish');
        $this->getMinkContext()->selectOption('to', 'Menu');

        $this->getMinkContext()->assertElementNotContainsText('#beneath-menu-item-id', $beneath);
    }

    /**
     * @Then /^I should not be able to republish the item named "([^"]*)" to menu beneath the content named "([^"]*)"$/
     */
    public function assertCannotRepublishContentBeneath($name, $beneath)
    {
        $this->getMainContext()->login('publisher');

        $this->editPublishing($name);
        $this->getMinkContext()->selectOption('to', 'Menu');

        $this->getMinkContext()->assertElementNotContainsText('#beneath-menu-item-id', $beneath);
    }

    /**
     * @Then /^the block named "([^"]*)" should be found (\d+) times within the item named "([^"]*)"$/
     */
    public function assertBlockPublishedWithinContent($blockName, $count, $contentName)
    {
        $this->visitContent($contentName);
        $this->getMinkContext()->assertElementCount('xpath', sprintf(
            '//aside[contains(@class,"block")][contains(.,"%s")]',
            $blockName
        ), $count);
    }

    /**
     * @Then /^the block named "([^"]*)" should be found (\d+) times within preview of the item named "([^"]*)"$/
     */
    public function assertBlockPreviewingWithinContent($blockName, $count, $contentName)
    {
        $this->switchToPreview($contentName, 'publishing');

        $this->getMinkContext()->assertElementCount('xpath', sprintf(
            '//aside[contains(@class,"block")][contains(.,"%s")]',
            $blockName
        ), $count);
    }

    /**
     * @Then /^the block named "([^"]*)" should not be published within the item named "([^"]*)"$/
     */
    public function assertBlockNotPublishedWithinContent($blockName, $contentName)
    {
        $this->visitContent($contentName);
        $this->getMinkContext()->assertPageNotContainsText($blockName);
    }

    /**
     * @Then /^I should see the block named "([^"]*)" (before|after) the block named "([^"]*)" in the "([^"]*)" block sequence within the item named "([^"]*)"$/
     */
    public function assertPositionInBlockSequence($blockName, $position, $compareBlockName, $sequenceName, $contentName)
    {
        $this->getMinkContext()->visitPage($contentName);

        $this->getMinkContext()->assertPositionInCollectionOfElements(
            'css',
            sprintf('aside.%s-block', strtolower(Zend_Filter::filterStatic($sequenceName, 'Word_CamelCaseToDash'))),
            $blockName,
            $position,
            $compareBlockName
        );
    }

    /**
     * @Then /^I should see the content named "([^"]*)" published to standalone$/
     */
    public function assertPublishedToStandalone($name)
    {
        $this->getMainContext()->login('publisher');

        $this->getMinkContext()->visitPage('/menu');
        $this->getMinkContext()->assertPageContainsText('Index of: menu items');
        $this->getMinkContext()->assertPageNotContainsText($name);

        $this->getMinkContext()->visitPage('/standalone');
        $this->getMinkContext()->assertPageContainsText('Index of: standalone content');
        $this->getMinkContext()->assertPageContainsText($name);
    }

    /**
     * @Then /^I should not be able to republish the item named "([^"]*)" to standalone$/
     */
    public function assertCannotRepublishToStandalone($name)
    {
        $this->getMainContext()->login('publisher');

        $this->editPublishing($name);

        $this->getMinkContext()->assertElementNotContainsText('#to', 'Standalone');
    }

}
