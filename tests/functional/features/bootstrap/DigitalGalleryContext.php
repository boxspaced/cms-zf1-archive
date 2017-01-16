<?php

use Behat\Behat\Context\BehatContext;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Session;

class DigitalGalleryContext extends BehatContext
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
     * @When /^I import "([^"]*)" and "([^"]*)" via the digital gallery manager$/
     */
    public function bulkImport($csv, $zip)
    {
        $this->getMinkContext()->visitPage('/digital-gallery/import');

        $this->getMinkContext()->attachFileToField('images', $zip);

        $this->getMinkContext()->pressButton('Browse server');

        $this->getMinkContext()->wait(function($context) {
            $context->getSession()->switchToWindow('CKFinderpopup');
            return true;
        });

        $this->getMinkContext()->wait(function($context) {
            $context->getMinkContext()->switchToIFrameWithNoName(0);
            return true;
        });

        $this->getMainContext()->uploadFileInCkFinder($csv, 'documents');

        $this->getMinkContext()->wait(function($context) use ($csv) {
            $selector = sprintf('//a[contains(.,"%s")]', $csv);
            $context->getMinkContext()->getElement('xpath', $selector)->click();
            $context->getMinkContext()->getElement('xpath', $selector)->doubleClick();
            return true;
        });

        $this->getMinkContext()->switchToWindowWithNoName(0);

        $this->getMinkContext()->pressButton('Import');

        $this->getMainContext()->reindex();
    }

    /**
     * @When /^I create a new filter via the digital gallery manager:$/
     */
    public function createFilterViaManager(TableNode $table)
    {
        $this->navigateToFilters();
        $this->getMinkContext()->clickLink('Create new');

        $inputs = $table->getHash()[0];

        $this->fillFilterForm($inputs);
        $this->getMinkContext()->pressButton('Save');
    }

    /**
     * @return void
     */
    protected function navigateToFilters()
    {
        $this->navigateToManager();
        $this->getMinkContext()->clickLink('Filters');
    }

    /**
     * @todo clicking Modules doesn't open sub menu (also tried mouseOver and focus)
     *
     * @return void
     */
    protected function navigateToManager()
    {
        try {
            $this->getMinkContext()->clickLink('Site');
            $this->getMinkContext()->clickLink('Modules');
            $this->getMinkContext()->clickLink('digital-gallery');
        } catch (Exception $e) {
            $this->getMinkContext()->visitPage('/digital-gallery/manage');
        }
    }

    /**
     * @param array $inputs
     * @return void
     */
    protected function fillFilterForm(array $inputs)
    {
        $this->getMinkContext()->selectOption('type', $inputs['type']);
        $this->getMinkContext()->fillField('text', $inputs['text']);
    }

    /**
     * @When /^I edit the filter with text "([^"]*)" via the digital gallery manager:$/
     */
    public function editFilterViaManager($text, TableNode $table)
    {
        $this->navigateToFilters();

        $row = $this->getMinkContext()->getElement('xpath', sprintf('//tr[contains(.,"%s")]', $text));
        $inputs = $table->getHash()[0];

        $row->clickLink('Edit');
        $this->fillFilterForm($inputs);
        $this->getMinkContext()->pressButton('Save');
    }

    /**
     * @When /^I delete the filter with text "([^"]*)" via the digital gallery manager$/
     */
    public function deleteFilterViaManager($text)
    {
        $this->navigateToFilters();
        $row = $this->getMinkContext()->getElement('xpath', sprintf('//tr[contains(.,"%s")]', $text));
        $row->clickLink('Delete');
        $this->getMinkContext()->pressButton('Confirm delete');
    }

    /**
     * @When /^I upload a new image via the digital gallery manager:$/
     */
    public function createImageViaManager(TableNode $table)
    {
        $this->navigateToManager();
        $this->getMinkContext()->clickLink('Upload');

        $inputs = $table->getHash()[0];

        $this->fillImageForm($inputs);
        $this->getMinkContext()->pressButton('Upload');

        $this->getMainContext()->reindex();
    }

    /**
     * @param array $inputs
     * @return void
     */
    protected function fillImageForm(array $inputs)
    {
        $this->getMinkContext()->fillField('title', $inputs['title']);
        $this->getMinkContext()->fillField('keywords', $inputs['keywords']);
        $this->getMinkContext()->fillField('description', $inputs['description']);
        $this->getMinkContext()->fillField('imageNo', $inputs['imageNo']);
        $this->getMinkContext()->fillField('copyright', $inputs['copyright']);
        $this->getMinkContext()->fillField('price', $inputs['price']);

        if ($this->getSession()->getPage()->findField('image')) {
            $this->getMinkContext()->attachFileToField('image', $inputs['image']);
        }

        $categories = implode(',', [$inputs['decades'], $inputs['locations'], $inputs['subjects']]);

        foreach (explode(',', $categories) as $category) {
            $this->getMinkContext()->checkOption($category);
        }
    }

    /**
     * @When /^I edit the image titled "([^"]*)" via the digital gallery manager:$/
     */
    public function editImageViaManager($title, TableNode $table)
    {
        $this->navigateToManager();

        $row = $this->getMinkContext()->getElement('xpath', sprintf('//tr[contains(.,"%s")]', $title));
        $inputs = $table->getHash()[0];

        $row->clickLink('Edit');
        $this->fillImageForm($inputs);
        $this->getMinkContext()->pressButton('Save');

        $this->getMainContext()->reindex();
    }

    /**
     * @When /^I delete the image titled "([^"]*)" via the digital gallery manager$/
     */
    public function deleteImageViaManager($title)
    {
        $this->navigateToManager();
        $row = $this->getMinkContext()->getElement('xpath', sprintf('//tr[contains(.,"%s")]', $title));
        $row->clickLink('Delete');
        $this->getMinkContext()->pressButton('Confirm delete');

        $this->getMainContext()->reindex();
    }

    /**
     * @When /^I search for "([^"]*)" in digital gallery$/
     */
    public function keywordSearch($keyword)
    {
        $this->getMinkContext()->visitPage('/digital-gallery-search');
        $this->getMinkContext()->fillField('digital-gallery-search-q', $keyword);
        $this->getMinkContext()->pressButton('digital-gallery-search-btn');
    }

    /**
     * @When /^I browse by "([^"]*)" in digital gallery$/
     */
    public function browseBy($browse)
    {
        $this->getMinkContext()->visitPage('/digital-gallery-search');
        $this->getMinkContext()->checkOption($browse);
        $this->getMinkContext()->pressButton('digital-gallery-search-btn');
    }

    /**
     * @Then /^I should see in digital gallery manager:$/
     */
    public function assertManagerContains(TableNode $table)
    {
        $this->getMainContext()->login('digital-gallery-manager');
        $this->getMinkContext()->visitPage('/digital-gallery/manage');
        $this->getMinkContext()->assertPageContainsText('Index of: digital gallery images');
        $this->getMinkContext()->assertTableContains('css', 'table', $table);
    }

    /**
     * @Then /^I should not see in digital gallery manager:$/
     */
    public function assertManagerNotContains(TableNode $table)
    {
        $this->getMainContext()->login('digital-gallery-manager');
        $this->getMinkContext()->visitPage('/digital-gallery/manage');
        $this->getMinkContext()->assertPageContainsText('Index of: digital gallery images');
        $this->getMinkContext()->assertTableNotContains('css', 'table', $table);
    }

    /**
     * @Then /^I should see in digital gallery filters:$/
     */
    public function assertFiltersContain(TableNode $table)
    {
        $this->getMainContext()->login('digital-gallery-manager');
        $this->getMinkContext()->visitPage('/digital-gallery/categories');
        $this->getMinkContext()->assertPageContainsText('Index of: digital gallery filters');
        $this->getMinkContext()->assertTableContains('css', 'table', $table);
    }

    /**
     * @Then /^I should not see in digital gallery filters:$/
     */
    public function assertFiltersNotContain(TableNode $table)
    {
        $this->getMainContext()->login('digital-gallery-manager');
        $this->getMinkContext()->visitPage('/digital-gallery/categories');
        $this->getMinkContext()->assertPageContainsText('Index of: digital gallery filters');
        $this->getMinkContext()->assertTableNotContains('css', 'table', $table);
    }

    /**
     * @Then /^I should see digital image "([^"]*)" with detail:$/
     */
    public function assertImageContains($imageNo, TableNode $table)
    {
        $this->visitImage($imageNo);

        $this->getMinkContext()->takeScreenshot('digital-gallery-image');

        foreach ($table->getHash()[0] as $key => $detail) {

            if ('keywords' === $key) {

                foreach (explode(',', $detail) as $keyword) {
                    $this->getMinkContext()->assertPageContainsText($keyword);
                }
                continue;
            }

            if ('image' === $key) {

                $this->getMinkContext()->assertElementOnPage(sprintf('img[src^="/digital_gallery/"][src$="%s"]', $detail));
                continue;
            }

            $this->getMinkContext()->assertPageContainsText($detail);
        }
    }

    /**
     * @param type $imageNo
     */
    protected function visitImage($imageNo)
    {
        $this->keywordSearch($imageNo);

        $this->getMinkContext()->getElement(
            'xpath',
            '//ol/li[1]/a'
        )->click();
    }

    /**
     * @Then /^I should see "([^"]*)" on page (\d+) of digital gallery results$/
     */
    public function assertSearchResultsContain($text, $page)
    {
        $this->getMinkContext()->assertPageAddress('/digital-gallery/results');

        for ($i = 1; $i <= $page; $i++) {

            if ($i == 1) { // Skip first page as already on it
                continue;
            }

            $this->getMinkContext()->clickLink('Next >');
        }

        $this->getMinkContext()->assertPageContainsText($text);
    }

}
