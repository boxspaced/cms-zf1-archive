<?php

use Behat\Behat\Context\BehatContext;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;

class ModuleContext extends BehatContext
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
     * @param string $code
     */
    public function visitCourse($code)
    {
        $this->getMinkContext()->visitPage(sprintf('/course/index/code/%s', $code));
    }

    /**
     * @When /^I import "([^"]*)" via the (course|whats on) manager$/
     */
    public function importFile($file, $module)
    {
        $this->getMinkContext()->visitPage(sprintf(
            '/%s/import',
            $this->fixModule($module)
        ));
        $this->getMinkContext()->assertPageContainsText(sprintf('Import: %s', $module));
        $this->getMinkContext()->pressButton('Browse server');

        $this->getMinkContext()->wait(function($context) {
            $context->getSession()->switchToWindow('CKFinderpopup');
            return true;
        });

        $this->getMinkContext()->wait(function($context) {
            $context->getMinkContext()->switchToIFrameWithNoName(0);
            return true;
        });

        $this->getMainContext()->uploadFileInCkFinder($file, 'documents');

        $this->getMinkContext()->wait(function($context) use ($file) {
            $selector = sprintf('//a[contains(.,"%s")]', $file);
            $context->getMinkContext()->getElement('xpath', $selector)->click();
            $context->getMinkContext()->getElement('xpath', $selector)->doubleClick();
            return true;
        });

        $this->getMinkContext()->switchToWindowWithNoName(0);

        $this->getMinkContext()->pressButton('Import');
    }

    /**
     * @param string $module
     * @return string
     */
    protected function fixModule($module)
    {
        return str_replace(' ', '-', $module);
    }

    /**
     * @When /^I search for "([^"]*)" in (course|whats on) search$/
     */
    public function keywordSearch($keyword, $module)
    {
        $module = $this->fixModule($module);

        $this->visitSearchPage($module);
        $this->getMinkContext()->fillField(sprintf('%s-search-q', $module), $keyword);
        $this->getMinkContext()->pressButton(sprintf('%s-search-btn', $module));
    }

    /**
     * @param string $module
     * @return void
     */
    protected function visitSearchPage($module)
    {
        $module = $this->fixModule($module);

        if ($module == 'whats-on') {

            $this->getMinkContext()->visitPage('/whats-on');
            return;
        }

        $this->getMinkContext()->visitPage(sprintf('/%s-search', $module));
    }

    /**
     * @When /^I browse by "([^"]*)" in (course|whats on) search$/
     */
    public function browseBy($browse, $module)
    {
        $module = $this->fixModule($module);

        $this->visitSearchPage($module);
        $this->getMinkContext()->checkOption($browse);
        $this->getMinkContext()->pressButton(sprintf('%s-search-btn', $module));
    }

    /**
     * @When /^I search by specific date "([^"]*)" in (course|whats on) search$/
     */
    public function specificDateSearch($date, $module)
    {
        $date = new DateTime($date);
        $module = $this->fixModule($module);

        $this->visitSearchPage($module);

        foreach ($this->getSession()->getPage()->findAll('css', '#calendar .day') as $td) {

            if ($td->getAttribute('class') !== 'day') {
                continue;
            }

            if ($td->getText() === $date->format('j')) {

                $td->click();
                break;
            }
        }

        $this->getMinkContext()->takeScreenshot();

        $this->getMinkContext()->pressButton(sprintf('%s-date-search-btn', $module));
    }

    /**
     * @Then /^I should see on page (\d+) of (course|whats on) manager:$/
     */
    public function assertManagerContains($page, $module, TableNode $table)
    {
        $this->getMainContext()->login(sprintf('%s-manager', $this->fixModule($module)));

        $this->getMinkContext()->visitPage(sprintf(
            '/%s/manage',
            $this->fixModule($module)
        ));
        $this->getMinkContext()->assertPageContainsText(sprintf('Index of: %s', $module));

        if ('1' !== $page) {
            $this->getMinkContext()->clickLink($page);
        }

        $this->getMinkContext()->assertTableContains('css', 'table', $table);
    }

    /**
     * @Then /^I should see "([^"]*)" on page (\d+) of (course|whats on) search results$/
     */
    public function assertSearchResultsContain($text, $page, $module)
    {
        $this->getMinkContext()->assertPageAddress(sprintf(
            '/%s/results',
            $this->fixModule($module)
        ));

        for ($i = 1; $i <= $page; $i++) {

            if ($i == 1) { // Skip first page as already on it
                continue;
            }

            $this->getMinkContext()->clickLink('Next >');
        }

        $this->getMinkContext()->assertPageContainsText($text);
    }

    /**
     * @Then /^I should see course "([^"]*)" with detail:$/
     */
    public function assertCourseContains($code, TableNode $table)
    {
        $this->visitCourse($code);
        $this->getMinkContext()->assertTableContains('css', 'table.course', $table);
    }

}
