Feature: Block authoring workflow
In order to keep track of my work
As a content author
I want to be able to view and manage blocks of content at the authoring stage

  Background:
    Given a fresh install of the CMS
      And there are users:
      | username  | roles     |
      | admin     | admin     |
      | author    | author    |
      | publisher | publisher |

  Scenario: View multiple blocks at different stages in authoring workflow
    Given there are blocks:
      | name                 | type | version | stage      |
      | new-in-authoring     | html | new     | authoring  |
      | update-in-authoring  | html | update  | authoring  |
      | new-in-publishing    | html | new     | publishing |
      | update-in-publishing | html | update  | publishing |
     When I am logged in as "author"
     Then I should see in my authoring workflow:
      | name                 | type | version | stage      |
      | new-in-authoring     | html | new     | authoring  |
      | update-in-authoring  | html | update  | authoring  |
      | new-in-publishing    | html | new     | publishing |
      | update-in-publishing | html | update  | publishing |

  @javascript
  Scenario: Hide unpublished new block from CMS users
    Given there are blocks:
      | name            | type | version | stage     |
      | test-html-block | html | new     | authoring |
     When I am logged in as "admin"
     Then the block named "test-html-block" should not be available to assign

  @javascript
  Scenario: Hide unpublished block update from CMS users
    Given there are blocks:
      | name            | type | version | stage     |
      | test-html-block | html | update  | authoring |
     When I am logged in as "admin"
     Then the block named "test-html-block" should be published at version 1

  Scenario: Delete new block from authoring workflow
    Given there are blocks:
      | name            | type | version | stage     |
      | test-html-block | html | new     | authoring |
     When I am logged in as "author"
      And I delete the block named "test-html-block" from my authoring workflow
     Then I should not see the block named "test-html-block" anywhere in workflow

  Scenario: Delete block update from authoring workflow
    Given there are blocks:
      | name            | type | version | stage     |
      | test-html-block | html | update  | authoring |
     When I am logged in as "author"
      And I delete the block named "test-html-block" from my authoring workflow
     Then I should not see the block named "test-html-block" anywhere in workflow

