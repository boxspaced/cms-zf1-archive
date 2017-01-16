Feature: Home page
In order to get an initial impression of the website
As a website visitor
I want to be able to see a home page

  Background:
    Given a fresh install of the CMS
      And there are users:
      | username  | roles     |
      | admin     | admin     |
      | author    | author    |
      | publisher | publisher |

  Scenario: Item as home page
    Given there are items:
      | name | type    | version | stage     |
      | home | article | new     | published |
     When I go to the homepage
     Then I should see "v1"
