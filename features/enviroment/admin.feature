Feature: Admin
  In order to manage plugin
  As a Shop administrator
  I need to be able to manage plugin

  Scenario: Browsing to the simply admin dashboard
    Given I am on simply admin login
    When I am logged in
    Then I should see "Dashboard"

  Scenario: Browsing to the multisite admin dashboard
    Given I am on multisite admin login
    When I am logged in
    Then I should see "Dashboard"
    And I should see "Network Admin"
