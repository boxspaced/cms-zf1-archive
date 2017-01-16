<?php

use Behat\Mink\Exception\ExpectationException;
use Behat\Mink\Exception\ElementHtmlException;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Element\ElementInterface;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Driver\Selenium2Driver;

class MinkContext extends Behat\MinkExtension\Context\MinkContext
{

    /**
     * @var array
     */
    private $parameters;

    /**
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        $this->setParameters($parameters);
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     * @return MinkContext
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
        return $this;
    }

    /**
     * @param string $label
     * @return string
     */
    public function takeScreenshot($label = null)
    {
        if (!$this->driverSupportsJavascript()) {
            return;
        }

        $image = $this->getSession()->getDriver()->getScreenshot();
        $filename = $this->generateFilenameForOutput('screenshot_path', $label, 'png');

        file_put_contents($filename, $image);

        return $filename;
    }

    /**
     * @param string $label
     * @return string
     */
    public function dumpResponse($label = null)
    {
        $response = $this->getSession()->getPage()->getContent();
        $filename = $this->generateFilenameForOutput('response_path', $label, 'html');

        file_put_contents($filename, $response);

        return $filename;
    }

    /**
     * @param string $pathName
     * @param string $label
     * @param string $extension
     * @return string
     */
    protected function generateFilenameForOutput($pathName, $label, $extension)
    {
        return implode(DIRECTORY_SEPARATOR, [
            rtrim(realpath($this->getParameters()[$pathName]), DIRECTORY_SEPARATOR),
            sprintf(
                '%s-%s-%s.%s',
                $this->getMinkParameter('browser_name'),
                date('YmdHis'),
                $label ?: 'none',
                $extension
            )
        ]);
    }

    /**
     * @return boolean
     */
    public function driverSupportsJavascript()
    {
        $driver = $this->getSession()->getDriver();
        return ($driver instanceof Selenium2Driver);
    }

    /**
     * @param Callable $callback
     * @param int $wait
     * @return boolean
     */
    public function wait(Callable $callback, $wait = 60)
    {
        for ($i = 0; $i < $wait; $i++) {

            try {
                if ($callback($this->getMainContext())) {
                    return true;
                }
            } catch (Exception $e) {
                // do nothing
            }
            sleep(1);
        }

        $backtrace = debug_backtrace();

        $msg = sprintf('Timeout thrown by %s::%s()', $backtrace[1]['class'], $backtrace[1]['function']);

        if (isset($backtrace[1]['file']) && isset($backtrace[1]['line'])) {

            $msg .= PHP_EOL . sprintf('%s, line %s', $backtrace[1]['file'], $backtrace[1]['line']);
        }

        throw new RuntimeException($msg);
    }

    /**
     * @todo change to be like getWindowNames (assuming iframes with no name have auto generated name?)
     * 
     * @param int $index
     * @return void
     */
    public function switchToIframeWithNoName($index = 0)
    {
        $this->getSession()->executeScript(
            "
                (function() {
                    var iframes = document.getElementsByTagName('iframe');
                    var iframe = iframes[{$index}];
                    iframe.id = 'no-name-iframe';
                })()
            "
        );
        $this->getSession()->switchToIFrame('no-name-iframe');
    }

    /**
     * @param int $index
     * @throws UnexpectedValueException
     * @return void
     */
    public function switchToWindowWithNoName($index = 0)
    {
        $windowNames = $this->getSession()->getWindowNames();

        if (!isset($windowNames[$index])) {
            throw new UnexpectedValueException("Window not found at index: {$index}");
        }

        $this->getSession()->switchToWindow($windowNames[$index]);
    }

    /**
     * @param string $selectorType
     * @param string $selector
     * @return NodeElement
     */
    public function getElement($selectorType, $selector)
    {
        return $this->assertSession()->elementExists($selectorType, $selector);
    }

    /**
     * @param string $field
     * @return NodeElement
     */
    public function getField($field)
    {
        return $this->assertSession()->fieldExists($field);
    }

    /**
     * @param string $selectorType
     * @param string $selector
     * @param string $attribute
     * @param string $text
     * @throws ElementHtmlException
     */
    public function assertElementAttributeContains($selectorType, $selector, $attribute, $text)
    {
        $element = $this->getElement($selectorType, $selector);

        $actual = $element->getAttribute($attribute);
        $regex = sprintf('/%s/ui', preg_quote($text, '/'));

        if (!preg_match($regex, $actual)) {
            $message = sprintf('The text "%s" was not found in the "%s" attribute of the element matching %s "%s".', $text, $attribute, $selectorType, $selector);
            throw new ElementHtmlException($message, $this->getMink()->getSession(), $element);
        }
    }

    /**
     * @param string $selectorType
     * @param string $selector
     * @param TableNode $expected
     * @throws ExpectationException
     */
    public function assertTableContains($selectorType, $selector, TableNode $expected)
    {
        $table = $this->getElement($selectorType, $selector);

        $matched = $this->findMatchingTableRows($table, $expected);

        $numMatched = count($matched);
        $numExpected = count($expected->getRows()) - 1;

        if ($numMatched !== $numExpected) {

            $message = sprintf('%d matching rows found in the table, but should be %d.', $numMatched, $numExpected);
            throw new ExpectationException($message, $this->getMink()->getSession());
        }
    }

    /**
     * @param ElementInterface $table
     * @param TableNode $search
     * @return NodeElement[]
     */
    protected function findMatchingTableRows(ElementInterface $table, TableNode $search)
    {
        $actualRows = $table->findAll('css', 'tr');
        $searchRows = $search->getHash();

        return array_filter($actualRows, function($actualRow) use ($searchRows) {

            $actualText = $actualRow->getText();

            foreach ($searchRows as $searchRow) {

                $numMatchedCols = array_reduce($searchRow, function($carry, $searchCol) use ($actualText) {

                    $regex = sprintf('/%s/ui', preg_quote($searchCol, '/'));

                    if (preg_match($regex, $actualText)) {
                        $carry++;
                    }

                    return $carry;
                });

                if ($numMatchedCols === count($searchRow)) {
                    return true;
                }
            }

            return false;
        });
    }

    /**
     * @param string $selectorType
     * @param string $selector
     * @param TableNode $notExpected
     * @throws ExpectationException
     */
    public function assertTableNotContains($selectorType, $selector, TableNode $notExpected)
    {
        $table = $this->getElement($selectorType, $selector);

        $numMatched = count($this->findMatchingTableRows($table, $notExpected));

        if ($numMatched) {

            $message = sprintf('%d matching rows found in the table, but should be none.', $numMatched);
            throw new ExpectationException($message, $this->getMink()->getSession());
        }
    }

    /**
     * @param string $text
     */
    public function assertUrlContains($text)
    {
        $url = $this->getSession()->getCurrentUrl();

        if (false === strpos($url, $text)) {

            $message = sprintf('Current URL "%s" does not contain the text "%s".', $url, $text);
            throw new ExpectationException($message, $this->getMink()->getSession());
        }
    }

    /**
     * @param string $page
     */
    public function visitPage($page)
    {
        try {
            $this->assertPageAddress($page);
        } catch (ExpectationException $e) {
            $this->visit($page);
        }
    }

    /**
     * @param string $collectionSelectorType
     * @param string $collectionSelector
     * @param string $target
     * @param string $position
     * @param string $compare
     * @param string $attribute
     */
    public function assertPositionInCollectionOfElements(
        $collectionSelectorType,
        $collectionSelector,
        $target,
        $position,
        $compare,
        $attribute = null
    )
    {
        $elements = $this->getSession()->getPage()->findAll($collectionSelectorType, $collectionSelector);

        $collection = array_map(function($element) use ($attribute) {
            if ($attribute) {
                return $element->getAttribute($attribute);
            } else {
                return $element->getText();
            }
        }, $elements);

        $targetResults = array_filter($collection, function($item) use ($target) {
            return (false !== strpos($item, $target));
        });

        if (empty($targetResults)) {

            throw new ExpectationException(sprintf(
                '"%s" was not found in collection',
                $target
            ), $this->getSession());
        }

        $compareResults = array_filter($collection, function($item) use ($compare) {
            return (false !== strpos($item, $compare));
        });

        if (empty($compareResults)) {

            throw new ExpectationException(sprintf(
                '"%s" was not found in collection',
                $compare
            ), $this->getSession());
        }

        $diff = (key($targetResults) - key($compareResults));

        if (
            ('before' === $position && $diff !== -1)
            || ('after' === $position && $diff !== 1)
        ) {

            throw new ExpectationException(sprintf(
                '"%s" was not found %s "%s" in collection: "%s" using attribute: %s position diff was: %d',
                $target,
                $position,
                $compare,
                $collectionSelector,
                $attribute,
                $diff
            ), $this->getSession());
        }
    }

    /**
     * @param string $selectorType
     * @param string $selector
     * @param int $count
     */
    public function assertElementCount($selectorType, $selector, $count)
    {
        $this->assertSession()->elementsCount($selectorType, $selector, intval($count));
    }

}
