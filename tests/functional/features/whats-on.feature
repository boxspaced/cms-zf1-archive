@javascript
Feature: Whats on search
In order to find whats on in the area
As a site visitor
I want to be able to search for and view whats on information

  Background:
    Given a fresh install of the CMS
      And there are users:
      | username | roles |
      | admin    | admin |

  Scenario: Search whats on by keyword
    Given there is an existing "whats_on.csv" import for whats on module
     When I search for "book" in whats on search
     Then I should see "Book Group" on page 1 of whats on search results

  Scenario: Browse whats on by day/time
    Given there is an existing "whats_on.csv" import for whats on module
     When I browse by "Monday - Day" in whats on search
     Then I should see "Boys Book Group: What in the World Is This Part 1" on page 1 of whats on search results

  Scenario: Browse whats on by venue
    Given there is an existing "whats_on.csv" import for whats on module
     When I browse by "Cubitt Town Library" in whats on search
     Then I should see "Mental Health and Wellbeing Advice" on page 1 of whats on search results

  Scenario: Browse whats on by activities
    Given there is an existing "whats_on.csv" import for whats on module
     When I browse by "Arts & Crafts" in whats on search
     Then I should see "Dan Jones Family Workshop" on page 1 of whats on search results

  Scenario: Browse whats on by age
    Given there is an existing "whats_on.csv" import for whats on module
     When I browse by "0 - 5yrs" in whats on search
     Then I should see "Julia Donaldson Week: The Flying Bath Storytelling and Face Painting" on page 1 of whats on search results

  Scenario: Page through results
    Given there is an existing "whats_on.csv" import for whats on module
     When I browse by "Monday - Day" in whats on search
     Then I should see "Boys Book Club How to Train Your Dragon" on page 2 of whats on search results

  Scenario: Handle no results
    Given there is an existing "whats_on.csv" import for whats on module
     When I search for "behat" in whats on search
     Then I should see "No results found" on page 1 of whats on search results

  Scenario: Search by specific date
    Given there is an existing "whats_on.csv" import for whats on module
     When I search by specific date "tomorrow" in whats on search
     Then I should see "Prime Time Compass Wellbeing Special" on page 1 of whats on search results
