<?php

use Behat\Behat\Context\BehatContext;
use Behat\Behat\Event\StepEvent;
use Behat\Mink\Session;

class FeatureContext extends BehatContext
{

    /**
     * @var string
     */
    private $loggedInAs;

    /**
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        $this->useContext('mink', new MinkContext($parameters));
        $this->useContext('app', new AppContext($parameters));
        $this->useContext('content', new ContentContext($parameters));
        $this->useContext('module', new ModuleContext($parameters));
        $this->useContext('digital-gallery', new DigitalGalleryContext($parameters));
    }

    /**
     * Close browser after every scenario to stop window related errors
     *
     * @AfterScenario
     */
    public function tearDown()
    {
        $this->getSession()->stop();
    }

    /**
     * @return string
     */
    public function getLoggedInAs()
    {
        return $this->loggedInAs;
    }

    /**
     * @param string $loggedInAs
     * @return FeatureContext
     */
    protected function setLoggedInAs($loggedInAs)
    {
        $this->loggedInAs = $loggedInAs;
        return $this;
    }

    /**
     * @return MinkContext
     */
    public function getMinkContext()
    {
        return $this->getSubcontext('mink');
    }

    /**
     * @return Session
     */
    public function getSession()
    {
        return $this->getMinkContext()->getSession();
    }

    /**
     * @AfterStep
     * @param StepEvent $event
     * @return void
     */
    public function takeScreenshotAfterFailedStep(StepEvent $event)
    {
        if (4 === $event->getResult()) {
            $screenshot = $this->getMinkContext()->takeScreenshot('failed-step');
            $this->printDebug($screenshot);
        }
    }

    /**
     * @AfterStep
     * @param StepEvent $event
     * @return void
     */
    public function dumpResponseAfterFailedStep(StepEvent $event)
    {
        if (4 === $event->getResult()) {
            $response = $this->getMinkContext()->dumpResponse('failed-step');
            $this->printDebug($response);
        }
    }

    /**
     * @Given /^I am logged in as "([^"]*)"$/
     */
    public function login($username)
    {
        if ($username === $this->getLoggedInAs()) {
            return;
        }

        $this->getMinkContext()->visitPage('/account/logout');
        $this->getMinkContext()->visitPage('/account/login');
        $this->getMinkContext()->fillField('username', $username);
        $this->getMinkContext()->fillField('password', 'password');
        $this->getMinkContext()->pressButton('Login');
        $this->getMinkContext()->assertPageAddress('/account');
        $this->getMinkContext()->assertPageContainsText('Account home');

        $this->setLoggedInAs($username);
    }

    /**
     * @Given /^I am logged out$/
     */
    public function logout()
    {
        if (null === $this->getLoggedInAs()) {
            return;
        }

        $this->getMinkContext()->visitPage('/account/logout');
        $this->setLoggedInAs(null);
    }

    /**
     * @When /^I navigate away$/
     */
    public function navigateAway()
    {
        $this->getMinkContext()->visitPage('/account');
    }

    /**
     * @When /^I upload "([^"]*)" to (images|documents|media) via asset manager$/
     */
    public function uploadFileViaAssetManager($file, $folder)
    {
        $this->getMinkContext()->visitPage('/asset');

        $this->getMinkContext()->wait(function($context) {
            $context->getMinkContext()->switchToIFrameWithNoName(1);
            return true;
        });

        $this->uploadFileInCkFinder($file, $folder);
    }

    /**
     * @param string $file
     * @param string $folder
     * @return void
     */
    public function uploadFileInCkFinder($file, $folder)
    {
        $this->getMinkContext()->wait(function($context) use ($folder) {
            $context->getMinkContext()->clickLink(ucfirst($folder));
            return true;
        });

        $this->getMinkContext()->wait(function($context) {
            $context->getMinkContext()->clickLink('Upload');
            return true;
        });

        $this->getSession()->executeScript(
            "
                (function() {
                    var fileInput = document.getElementById('ckf_fileInput');
                    fileInput.style.visibility = 'visible';
                    fileInput.style.height = 'auto';
                    fileInput.style.width = 'auto';
                })()
            "
        );

        $this->getMinkContext()->attachFileToField('ckf_fileInput', $file);
    }

    /**
     * @Then /^I should see "([^"]*)" in the asset manager (images|documents|media)$/
     */
    public function assertFileInAssetManager($file, $folder)
    {
        $this->getMainContext()->login('asset-manager');
        $this->getMinkContext()->visit('/asset'); // need to force page to reload after upload
        $this->getMinkContext()->assertPageContainsText('Asset manager');

        $this->getMinkContext()->wait(function($context) {
            $context->getMinkContext()->switchToIFrameWithNoName(1);
            return true;
        });

        $this->getMinkContext()->wait(function($context) use ($folder) {
            $context->getMinkContext()->clickLink(ucfirst($folder));
            return true;
        });

        $this->getMinkContext()->wait(function($context) use ($file) {
            $context->getMinkContext()->assertPageContainsText($file);
            return true;
        });
    }

    /**
     * @return void
     */
    public function removeWysiwygEditors()
    {
        if (!$this->getMinkContext()->driverSupportsJavascript()) {
            return;
        }

        $this->getSession()->executeScript(
            "
                for(name in CKEDITOR.instances)
                {
                    CKEDITOR.instances[name].destroy(true);
                }
            "
        );
    }

    /**
     * @return void
     */
    public function reindex()
    {
        exec(sprintf('%s/cli/digital-gallery --index', APPLICATION_PATH));
        exec(sprintf('%s/cli/search --index', APPLICATION_PATH));
    }

}
