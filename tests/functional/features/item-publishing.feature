Feature: Item publishing
In order to provide useful content to visitors
As a content publisher
I want to be able to publish new and updated items of content

  Background:
    Given a fresh install of the CMS
      And there are users:
      | username  | roles     |
      | admin     | admin     |
      | author    | author    |
      | publisher | publisher |

  Scenario: Publish a new item directly from authoring workflow
     When I am logged in as "publisher"
      And I create and publish item:
      | name | test-article-item |
      | type | article           |
     Then the item named "test-article-item" should be published at version 1

  Scenario: Publish a previously saved new item from authoring workflow
     When I am logged in as "publisher"
      And I create and save item:
      | name | test-article-item |
      | type | article           |
      And I edit and publish the item named "test-article-item" from my authoring workflow
     Then the item named "test-article-item" should be published at version 2

  Scenario: Publish a previously saved item update from authoring workflow
    Given there are items:
      | name              | type    | version | stage     |
      | test-article-item | article | new     | published |
     When I am logged in as "publisher"
      And I edit and save the item named "test-article-item"
      And I edit and publish the item named "test-article-item" from my authoring workflow
     Then the item named "test-article-item" should be published at version 3

  Scenario: Publish a new item that has been submitted for publishing by another user
    Given there are items:
      | name              | type    | version | stage      |
      | test-article-item | article | new     | publishing |
     When I am logged in as "publisher"
      And I publish the item named "test-article-item" from my publishing workflow
     Then the item named "test-article-item" should be published at version 1

  Scenario: Publish an item update that has been submitted for publishing by another user
    Given there are items:
      | name              | type    | version | stage      |
      | test-article-item | article | update  | publishing |
     When I am logged in as "publisher"
      And I publish the item named "test-article-item" from my publishing workflow
     Then the item named "test-article-item" should be published at version 2

  Scenario: Publish an item update from item
    Given there are items:
      | name              | type    | version | stage     |
      | test-article-item | article | new     | published |
     When I am logged in as "publisher"
      And I edit and publish the item named "test-article-item"
     Then the item named "test-article-item" should be published at version 2

  Scenario: Delete published item
    Given there are items:
      | name              | type    | version | stage      |
      | test-article-item | article | update  | authoring  |
      | test-article-item | article | update  | publishing |
     When I am logged in as "publisher"
      And I delete the item named "test-article-item"
     Then I should not see "test-article-item" anywhere in system

  @javascript
  Scenario: Preview a new item
     When I am logged in as "publisher"
      And I create and preview item publishing:
      | name | test-article-item |
      | type | article           |
     Then the item named "test-article-item" should be previewing at version 1

  @javascript
  Scenario: Preview an item update
    Given there are items:
      | name              | type    | version | stage     |
      | test-article-item | article | new     | published |
     When I am logged in as "publisher"
      And I edit and preview the item named "test-article-item"
     Then the item named "test-article-item" should be previewing at version 2
