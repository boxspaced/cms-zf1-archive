@javascript
Feature: Course search
In order to find courses available to enroll in
As a site visitor
I want to be able to search for and view course information

  Background:
    Given a fresh install of the CMS
      And there are users:
      | username | roles |
      | admin    | admin |

  Scenario: Search courses by keyword
    Given there is an existing "courses.csv" import for course module
     When I search for "vegetarian" in course search
     Then I should see "Vegetarian Cookery - C1638" on page 1 of course search results

  Scenario: Browse courses by category
    Given there is an existing "courses.csv" import for course module
     When I browse by "Cookery" in course search
     Then I should see "RSPH Level 2 Award in Food Safety in Catering - C4701" on page 1 of course search results

  Scenario: Browse courses by day
    Given there is an existing "courses.csv" import for course module
     When I browse by "Monday" in course search
     Then I should see "Contemporary Dance - Improvers - C3686" on page 1 of course search results

  Scenario: Browse courses by time
    Given there is an existing "courses.csv" import for course module
     When I browse by "Day time" in course search
     Then I should see "Get Started In Sewing - C3715" on page 1 of course search results

  Scenario: Browse courses by venue
    Given there is an existing "courses.csv" import for course module
     When I browse by "Bow Road Methodist Church" in course search
     Then I should see "Tango Level 1 - Beginners - C3733" on page 1 of course search results

  Scenario: Page through results
    Given there is an existing "courses.csv" import for course module
     When I browse by "Photography" in course search
     Then I should see "Develop a Photography Project - All Levels - C4080" on page 2 of course search results

  Scenario: Handle no results
    Given there is an existing "courses.csv" import for course module
     When I search for "behat" in course search
     Then I should see "No results found" on page 1 of course search results

  Scenario: View course details
    Given there is an existing "courses.csv" import for course module
     Then I should see course "C4872" with detail:
      | code  | day | startDate | time | numWeeks | hoursPerWeek | venue | fee | concession |
      | C4872 | Saturday | 06 Jun 2017 | 10:00 | 5 | 2 | Shadwell Centre | £32.00 | £22.00 |
