@javascript
Feature: Digital gallery
In order to find digital images I may be interested in
As a site visitor
I want to be able to search for and view digital images

  Background:
    Given a fresh install of the CMS
      And there are users:
      | username | roles |
      | admin    | admin |
      And there are digital gallery filters:
      | id | type     | text                                |
      | 46 | decade   | 1950s                               |
      | 76 | location | Island History Trust (Isle of Dogs) |
      | 10 | subject  | Pubs                                |

  Scenario: Search digital images by keyword
    Given there is an existing "digital_gallery.csv" and "digital_gallery.zip" import for digital gallery module
     When I search for "Grosvenor" in digital gallery
     Then I should see "Grosvenor Buildings, 1951" on page 1 of digital gallery results

  Scenario: Browse digital images by decade
    Given there is an existing "digital_gallery.csv" and "digital_gallery.zip" import for digital gallery module
     When I browse by "1950s" in digital gallery
     Then I should see "Westferry Road 1950s" on page 1 of digital gallery results

  Scenario: Browse digital images by location
    Given there is an existing "digital_gallery.csv" and "digital_gallery.zip" import for digital gallery module
     When I browse by "Island History Trust (Isle of Dogs)" in digital gallery
     Then I should see "Manchester Road 1955" on page 1 of digital gallery results

  Scenario: Browse digital images by subject
    Given there is an existing "digital_gallery.csv" and "digital_gallery.zip" import for digital gallery module
     When I browse by "Pubs" in digital gallery
     Then I should see "The Grapes 1985" on page 1 of digital gallery results

  Scenario: Page through results
    Given there is an existing "digital_gallery.csv" and "digital_gallery.zip" import for digital gallery module
     When I search for "poplar or street" in digital gallery
     Then I should see "Mecca Cafe Bloomfield St London" on page 2 of digital gallery results

  Scenario: Handle no results
    Given there is an existing "digital_gallery.csv" and "digital_gallery.zip" import for digital gallery module
     When I search for "behat" in digital gallery
     Then I should see "No results found" on page 1 of digital gallery results

  Scenario: View digital image details
    Given there is an existing "digital_gallery.csv" and "digital_gallery.zip" import for digital gallery module
     Then I should see digital image "P02666" with detail:
      | title              | keywords                               | description                  | imageNo | copyright                                        | image |
      | Douro Street, 1981 | 1980s, Street Scenes, Bow and Old Ford | Douro Street May - June 1981 | P02666  | Local History Library and Archives | .jpg  |
