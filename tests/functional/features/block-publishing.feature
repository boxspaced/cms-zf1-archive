Feature: Block publishing
In order to provide useful content to visitors
As a content publisher
I want to be able to publish new and updated blocks of content

  Background:
    Given a fresh install of the CMS
      And there are users:
      | username  | roles     |
      | admin     | admin     |
      | author    | author    |
      | publisher | publisher |

  @javascript
  Scenario: Publish a new block directly from authoring workflow
    Given I am logged in as "publisher"
     When I create and publish block:
      | name | test-html-block |
      | type | html            |
     Then the block named "test-html-block" should be published at version 1

  @javascript
  Scenario: Publish a previously saved new block from authoring workflow
     When I am logged in as "publisher"
      And I create and save block:
      | name | test-html-block |
      | type | html            |
      And I edit and publish the block named "test-html-block" from my authoring workflow
     Then the block named "test-html-block" should be published at version 2

  @javascript
  Scenario: Publish a previously saved block update from authoring workflow
    Given there are blocks:
      | name            | type | version | stage     |
      | test-html-block | html | new     | published |
      And I am logged in as "publisher"
     When I edit and save the block named "test-html-block"
      And I edit and publish the block named "test-html-block" from my authoring workflow
     Then the block named "test-html-block" should be published at version 3

  @javascript
  Scenario: Publish a new block that has been submitted for publishing by another user
    Given there are blocks:
      | name            | type | version | stage      |
      | test-html-block | html | new     | publishing |
      And I am logged in as "publisher"
     When I publish the block named "test-html-block" from my publishing workflow
     Then the block named "test-html-block" should be published at version 1

  @javascript
  Scenario: Publish a block update that has been submitted for publishing by another user
    Given there are blocks:
      | name            | type | version | stage      |
      | test-html-block | html | update  | publishing |
      And I am logged in as "publisher"
     When I publish the block named "test-html-block" from my publishing workflow
     Then the block named "test-html-block" should be published at version 2

  @javascript
  Scenario: Publish a block update from block
    Given there are blocks:
      | name            | type | version | stage     |
      | test-html-block | html | new     | published |
      And I am logged in as "publisher"
     When I edit and publish the block named "test-html-block"
     Then the block named "test-html-block" should be published at version 2

  Scenario: Delete published block
    Given there are blocks:
      | name            | type | version | stage      |
      | test-html-block | html | update  | authoring  |
      | test-html-block | html | update  | publishing |
      And I am logged in as "publisher"
     When I delete the block named "test-html-block"
     Then I should not see "test-html-block" anywhere in system
