Feature: Embed
  In order to access the site
  As a visitor
  I need to read embed Github data in post

  Background:
    Given I am on multisite admin home
    When I am logged in
    Then I should see "Dashboard"
    And I go to network admin
    And plugin must be activated
    And I go to test blog
    And test post must be created

  Scenario: Repositories
    Given I am on test post
    Then I should see the embedded repository

  Scenario: User profiles
    Given I am on test post
    Then I should see the embedded user profile

  Scenario: Milestone summaries
    Given I am on test post
    Then I should see the embedded milestone summaries

  Scenario: Repository contributors
    Given I am on test post
    Then I should see the embedded repository contributors
