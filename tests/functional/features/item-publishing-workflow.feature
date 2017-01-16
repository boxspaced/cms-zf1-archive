Feature: Item publishing workflow
In order to keep track of my work
As a content publisher
I want to be able to view and manage items of content at the publishing stage

  Background:
    Given a fresh install of the CMS
      And there are users:
      | username  | roles     |
      | admin     | admin     |
      | author    | author    |
      | publisher | publisher |

  Scenario: View multiple items at different stages in publishing workflow
    Given there are items:
      | name                 | type    | version | stage      |
      | new-in-authoring     | article | new     | authoring  |
      | update-in-authoring  | article | update  | authoring  |
      | new-in-publishing    | article | new     | publishing |
      | update-in-publishing | article | update  | publishing |
     When I am logged in as "publisher"
     Then I should see in my publishing workflow:
      | name                 | type    | version | stage      |
      | new-in-publishing    | article | new     | publishing |
      | update-in-publishing | article | update  | publishing |
      But I should not see in my publishing workflow:
      | name                |
      | new-in-authoring    |
      | update-in-authoring |

  Scenario: Hide unpublished new item from site visitors
    Given there are items:
      | name              | type    | version | stage      |
      | test-article-item | article | new     | publishing |
     When I am logged out
     Then the item named "test-article-item" should not be published

  Scenario: Hide unpublished new item from CMS users
    Given there are items:
      | name              | type    | version | stage      |
      | test-article-item | article | new     | publishing |
     When I am logged in as "admin"
     Then the item named "test-article-item" should not be published

  Scenario: Hide unpublished item update from site visitors
    Given there are items:
      | name              | type    | version | stage      |
      | test-article-item | article | update  | publishing |
     When I am logged out
     Then the item named "test-article-item" should be published at version 1

  Scenario: Hide unpublished item update from CMS users
    Given there are items:
      | name              | type    | version | stage      |
      | test-article-item | article | update  | publishing |
     When I am logged in as "admin"
     Then the item named "test-article-item" should be published at version 1

  Scenario: Send a new item back to author when not acceptable
    Given there are items:
      | name              | type    | version | stage      |
      | test-article-item | article | new     | publishing |
     When I am logged in as "publisher"
      And I send the item named "test-article-item" back to author from my publishing workflow
     Then the item named "test-article-item" should be sent back to the author

  Scenario: Send an item update back to author when not acceptable
    Given there are items:
      | name              | type    | version | stage      |
      | test-article-item | article | update  | publishing |
     When I am logged in as "publisher"
      And I send the item named "test-article-item" back to author from my publishing workflow
     Then the item named "test-article-item" should be sent back to the author

  Scenario: Delete new item from publishing workflow
    Given there are items:
      | name              | type    | version | stage      |
      | test-article-item | article | new     | publishing |
     When I am logged in as "publisher"
      And I delete the item named "test-article-item" from my publishing workflow
     Then I should not see the item named "test-article-item" anywhere in workflow

  Scenario: Delete item update from publishing workflow
    Given there are items:
      | name              | type    | version | stage      |
      | test-article-item | article | update  | publishing |
     When I am logged in as "publisher"
      And I delete the item named "test-article-item" from my publishing workflow
     Then I should not see the item named "test-article-item" anywhere in workflow

  @javascript
  Scenario: Preview new item from publishing workflow
    Given there are items:
      | name              | type    | version | stage      |
      | test-article-item | article | new     | publishing |
     When I am logged in as "publisher"
      And I preview the item named "test-article-item" from my publishing workflow
     Then the item named "test-article-item" should be previewing at version 1

  @javascript
  Scenario: Preview item update from publishing workflow
    Given there are items:
      | name              | type    | version | stage      |
      | test-article-item | article | update  | publishing |
     When I am logged in as "publisher"
      And I preview the item named "test-article-item" from my publishing workflow
     Then the item named "test-article-item" should be previewing at version 2
