<?php

define('WORDPRESS_ADMIN_USER', 'test@test.test');
define('WORDPRESS_ADMIN_PASSWORD', 'testtest');
define('WORDPRESS_SIMPLY_HOME', 'simply/');
define('WORDPRESS_MULTISITE_HOME', 'multisite/test1/');

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Behat\MinkExtension\Context\MinkContext;

/**
 * Features context.
 */
class FeatureContext extends MinkContext
{
    /**
     * @Given /^I am on simply homepage$/
     */
    public function iAmOnSimplyHomepage()
    {
        $this->getSession()->visit($this->locatePath(WORDPRESS_SIMPLY_HOME));
		}

    /**
     * @Given /^I am on multisite homepage$/
     */
    public function iAmOnMultisiteHomepage()
    {
        $this->getSession()->visit($this->locatePath(WORDPRESS_MULTISITE_HOME));
    }
}