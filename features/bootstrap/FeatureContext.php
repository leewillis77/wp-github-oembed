<?php

define('WORDPRESS_ADMIN_USER', 'test');
define('WORDPRESS_ADMIN_PASSWORD', 'testtest');

define('WORDPRESS_SIMPLY_HOME', 'simply/');
define('WORDPRESS_SIMPLY_ADMIN', 'simply/wp-admin/');
define('WORDPRESS_MULTISITE_HOME', 'multisite/test1/');
define('WORDPRESS_MULTISITE_ADMIN', 'multisite/test1/wp-admin/');
define('WORDPRESS_POST_TITLE', 'test wp-github-oembed');

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
		private static $testPostURLSimply = false;
		private static $testPostURLMultisite = false;

		private function isMultisite()
		{
				if (preg_match('|'.WORDPRESS_SIMPLY_HOME.'|', $this->getSession()->getCurrentURL())) {
						return false;
				} else {
						return true;
				}
		}

		private function setTestPostURL($value)
		{
				if ($this->isMultisite()) {
					self::$testPostURLSimply = $value;
				} else {
					self::$testPostURLMultisite = $value;
				}
		}

		private function getTestPostURL()
		{
				if ($this->isMultisite()) {
					return self::$testPostURLSimply;
				} else {
					return self::$testPostURLMultisite;
				}
		}

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
     *  @Given /^I am on simply admin home$/
     */
    public function iAmOnSimplyAdminHome()
    {
        $this->getSession()->visit($this->locatePath(WORDPRESS_SIMPLY_ADMIN));
    }

    /**
     * @Given /^I am on multisite admin home$/
     */
    public function iAmOnMultisiteAdminHome()
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

    /**
     * @When /^I go to admin plugins page$/
     */
    public function iGoToAdminPluginsPage()
    {
        $this->getSession()->getPage()->find('xpath', '//a[text()="Installed Plugins"]')->click();
    }

    /**
     * @Given /^plugin must be activated$/
     */
    public function pluginMustBeActivated()
    {
        $this->iGoToAdminPluginsPage();
        $flag = $this->getSession()->getPage()->find('css', '#github-embed .activate');
        if (!is_null($flag)) {
            $this->getSession()->getPage()->find('css', '#github-embed .activate a')->click();
        }
    }

    /**
     * @Given /^plugin must be deactivated$/
     */
    public function pluginMustBeDeactivated()
    {
        $this->iGoToAdminPluginsPage();
        $flag = $this->getSession()->getPage()->find('css', '#github-embed .deactivate');
        if (!is_null($flag)) {
            $this->getSession()->getPage()->find('css', '#github-embed .deactivate a')->click();
        }
    }

    /**
     * @Given /^test post must be created$/
     */
    public function testPostMustBeCreated()
    {
    		if (!$this->getTestPostURL()) {
    			$this->createPost();
    		}
    }

    /**
     * Fill post form
     */
		private function setPostContent() {
				$this->getSession()->getPage()->find('css', 'input[name="post_title"]')->setValue(WORDPRESS_POST_TITLE);
				$this->getSession()->getPage()->find('css', 'textarea[name="content"]')->setValue("
					<div id='test-oembed-repositories'>
					  <h3>Repositories</h3>
						https://github.com/leewillis77/wp-github-oembed
					</div>
					<div id='test-oembed-user-profiles'>
					  <h3>User profiles</h3>
						https://github.com/leewillis77/
					</div>
					<div id='test-oembed-milestone-summaries'>
						<h3>Milestone summaries</h3>
						https://github.com/leewillis77/wp-github-oembed/issues?milestone=1&state=open
					</div>
					<div id='test-oembed-repository-contributors'>
						<h3>Repository contributors</h3>
						https://github.com/leewillis77/wp-github-oembed/graphs/contributors
					</div>
				");
		}

    /**
     * Create post for tests
     */
    private function createPost()
    {
        $this->getSession()->getPage()->find('xpath', '//a[text()="All Posts"]')->click();
        $this->getSession()->getPage()->find('css', 'input[name="s"]')->setValue(WORDPRESS_POST_TITLE);
        $this->getSession()->getPage()->find('xpath', '//input[@value="Search Posts"]')->click();
        $flag = $this->getSession()->getPage()->find('xpath', '//text()[contains(.,"No posts found")]');
        if (!is_null($flag)) {
						$this->getSession()->getPage()->find('xpath', '//li[@id="menu-posts"]//a[text()="Add New"]')->click();
						$this->setPostContent();
						$this->getSession()->getPage()->find('xpath', '//input[@id="save-post"]')->click();
        } else {
        		$this->getSession()->getPage()->find('xpath', '//a[text()="Edit"]')->click();
        		$this->setPostContent();
        		$this->getSession()->getPage()->find('xpath', '//input[@id="save-post"]')->click();
        }
        $this->getSession()->getPage()->find('xpath', '//a[@id="post-preview"]')->click();
        $this->setTestPostURL($this->getSession()->getCurrentURL());
    }

    /**
     * @Given /^I am on test post$/
     */
    public function iAmOnTestPost()
    {
    		if ($this->isMultisite()) {
    				$this->iAmOnMultisiteHomepage();
    		}
        $this->getSession()->visit($this->getTestPostURL());
    }

    /**
     * @Then /^I should see the embedded repository$/
     */
    public function iShouldSeeTheEmbeddedRepository()
    {
				$flagTitle		= $this->getSession()->getPage()->find('xpath', '//div[@id="test-oembed-repositories"]//text()[contains(.,"WordPress Github ")]');
				$flagForks 		= $this->getSession()->getPage()->find('xpath', '//div[@id="test-oembed-repositories"]//text()[contains(.,"forks")]');
				$flagIssues 	= $this->getSession()->getPage()->find('xpath', '//div[@id="test-oembed-repositories"]//text()[contains(.,"open issues")]');
				$flagCommits 	= $this->getSession()->getPage()->find('xpath', '//div[@id="test-oembed-repositories"]//text()[contains(.,"Recent commits")]');
        if (is_null($flagTitle) || is_null($flagForks) || is_null($flagIssues) || is_null($flagCommits)) {
        	throw new Exception("Embedded repository not found in {$this->getSession()->getCurrentUrl()} page.");
        }
    }

    /**
     * @Then /^I should see the embedded user profile$/
     */
    public function iShouldSeeTheEmbeddedUserProfile()
    {
				$flagRepositories	= $this->getSession()->getPage()->find('xpath', '//div[@id="test-oembed-user-profiles"]//text()[contains(.,"repositories")]');
				$flagFollowers 		= $this->getSession()->getPage()->find('xpath', '//div[@id="test-oembed-user-profiles"]//text()[contains(.,"followers")]');
        if (is_null($flagRepositories) || is_null($flagFollowers)) {
        	throw new Exception("Embedded user profile not found in {$this->getSession()->getCurrentUrl()} page.");
        }
    }

    /**
     * @Then /^I should see the embedded milestone summaries$/
     */
    public function iShouldSeeTheEmbeddedMilestoneSummaries()
    {
				$flag	= $this->getSession()->getPage()->find('xpath', '//div[@id="test-oembed-milestone-summaries"]//text()[contains(.,"Test Milestone")]');
        if (is_null($flag)) {
        	throw new Exception("Embedded milestone summaries not found in {$this->getSession()->getCurrentUrl()} page.");
        }
    }

    /**
     * @Then /^I should see the embedded repository contributors$/
     */
    public function iShouldSeeTheEmbeddedRepositoryContributors()
    {
				$flag	= $this->getSession()->getPage()->find('xpath', '//div[@id="test-oembed-repository-contributors"]//text()[contains(.,"Contributors:")]');
        if (is_null($flag)) {
        	throw new Exception("Embedded repository contributors not found in {$this->getSession()->getCurrentUrl()} page.");
        }
    }

    /**
     * @Given /^I go to network admin$/
     */
    public function iGoToNetworkAdmin()
    {
        $this->getSession()->getPage()->find('xpath', '//a[text()="Network Admin"]')->click();
    }

    /**
     * @Given /^I go to test blog$/
     */
    public function iGoToTestBlog()
    {
        $this->getSession()->getPage()->find('xpath', '//a[text()="test1"]')->click();
    }
}