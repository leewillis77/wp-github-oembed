Feature: homepage
  In order to access the site
  As a website user
  I need to be able to reach the homepage

  Scenario: Browsing to the simply homepage
    Given I am on simply homepage
    Then the response status code should be 200

  Scenario: Browsing to the multisite homepage
    Given I am on multisite homepage
    Then the response status code should be 200
