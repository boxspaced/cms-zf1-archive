Feature: Block publishing workflow
In order to keep track of my work
As a content publisher
I want to be able to view and manage blocks of content at the publishing stage

  Background:
    Given a fresh install of the CMS
      And there are users:
      | username  | roles     |
      | admin     | admin     |
      | author    | author    |
      | publisher | publisher |

  Scenario: View multiple blocks at different stages in publishing workflow
    Given there are blocks:
      | name                 | type | version | stage      |
      | new-in-authoring     | html | new     | authoring  |
      | update-in-authoring  | html | update  | authoring  |
      | new-in-publishing    | html | new     | publishing |
      | update-in-publishing | html | update  | publishing |
     When I am logged in as "publisher"
     Then I should see in my publishing workflow:
      | name                 | type | version | stage      |
      | new-in-publishing    | html | new     | publishing |
      | update-in-publishing | html | update  | publishing |
      But I should not see in my publishing workflow:
      | name                |
      | new-in-authoring    |
      | update-in-authoring |

  @javascript
  Scenario: Hide unpublished new block from CMS users
    Given there are blocks:
      | name            | type | version | stage      |
      | test-html-block | html | new     | publishing |
     When I am logged in as "admin"
     Then the block named "test-html-block" should not be available to assign

  @javascript
  Scenario: Hide unpublished block update from site visitors
    Given there are blocks:
      | name            | type | version | stage      |
      | test-html-block | html | update  | publishing |
     When I am logged out
     Then the block named "test-html-block" should be published at version 1

  @javascript
  Scenario: Hide unpublished block update from CMS users
    Given there are blocks:
      | name            | type | version | stage      |
      | test-html-block | html | update  | publishing |
     When I am logged in as "admin"
     Then the block named "test-html-block" should be published at version 1

  Scenario: Send a new block back to author when not acceptable
    Given there are blocks:
      | name            | type | version | stage      |
      | test-html-block | html | new     | publishing |
      And I am logged in as "publisher"
     When I send the block named "test-html-block" back to author from my publishing workflow
     Then the block named "test-html-block" should be sent back to the author

  Scenario: Send an block update back to author when not acceptable
    Given there are blocks:
      | name            | type | version | stage      |
      | test-html-block | html | update  | publishing |
      And I am logged in as "publisher"
     When I send the block named "test-html-block" back to author from my publishing workflow
     Then the block named "test-html-block" should be sent back to the author

  Scenario: Delete new block from publishing workflow
    Given there are blocks:
      | name            | type | version | stage      |
      | test-html-block | html | new     | publishing |
     When I am logged in as "publisher"
      And I delete the block named "test-html-block" from my publishing workflow
     Then I should not see the block named "test-html-block" anywhere in workflow

  Scenario: Delete block update from publishing workflow
    Given there are blocks:
      | name            | type | version | stage      |
      | test-html-block | html | update  | publishing |
     When I am logged in as "publisher"
      And I delete the block named "test-html-block" from my publishing workflow
     Then I should not see the block named "test-html-block" anywhere in workflow
