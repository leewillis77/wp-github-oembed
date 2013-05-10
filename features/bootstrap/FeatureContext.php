<?php

define('WORDPRESS_ADMIN_USER', 'test');
define('WORDPRESS_ADMIN_PASSWORD', 'testtest');

define('WORDPRESS_SIMPLY_HOME', 'simply/');
define('WORDPRESS_SIMPLY_ADMIN', 'simply/wp-admin/');
define('WORDPRESS_MULTISITE_HOME', 'multisite/test1/');
define('WORDPRESS_MULTISITE_ADMIN', 'multisite/test1/wp-admin/');

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

    /**
     * @Given /^I am on simply admin login$/
     */
    public function iAmOnSimplyAdminLogin()
    {
        $this->getSession()->visit($this->locatePath(WORDPRESS_SIMPLY_ADMIN));
    }

    /**
     * @Given /^I am on multisite admin login$/
     */
    public function iAmOnMultisiteAdminLogin()
    {
        $this->getSession()->visit($this->locatePath(WORDPRESS_MULTISITE_ADMIN));
    }

    /**
     * @When /^I am logged in$/
     */
    public function iAmLoggedIn()
    {
        $this->getSession()->getPage()->find('css', 'input[name="log"]')->setValue(WORDPRESS_ADMIN_USER);
        $this->getSession()->getPage()->find('css', 'input[name="pwd"]')->setValue(WORDPRESS_ADMIN_PASSWORD);
        $this->getSession()->getPage()->find('css', 'input[name="wp-submit"]')->click();
    }

}