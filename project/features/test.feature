Feature: Test for new setup
  Testing new setup

  Scenario: Looking for Code is Poetry
    Given I go to "https://wordpress.org/"
    Then I should see "Code is Poetry."

  @javascript
  Scenario: Looking for Code is Poetry with JS on
    Given I go to "https://wordpress.org/"
    Then I should see "Code is Poetry."
