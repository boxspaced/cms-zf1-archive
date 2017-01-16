Feature: Item authoring workflow
In order to keep track of my work
As a content author
I want to be able to view and manage items of content at the authoring stage

  Background:
    Given a fresh install of the CMS
      And there are users:
      | username  | roles     |
      | admin     | admin     |
      | author    | author    |
      | publisher | publisher |

  Scenario: View multiple items at different stages in authoring workflow
    Given there are items:
      | name                 | type    | version | stage      |
      | new-in-authoring     | article | new     | authoring  |
      | update-in-authoring  | article | update  | authoring  |
      | new-in-publishing    | article | new     | publishing |
      | update-in-publishing | article | update  | publishing |
     When I am logged in as "author"
     Then I should see in my authoring workflow:
      | name                 | type    | version | stage      |
      | new-in-authoring     | article | new     | authoring  |
      | update-in-authoring  | article | update  | authoring  |
      | new-in-publishing    | article | new     | publishing |
      | update-in-publishing | article | update  | publishing |

  Scenario: Hide unpublished new item from site visitors
    Given there are items:
      | name              | type    | version | stage     |
      | test-article-item | article | new     | authoring |
     When I am logged out
     Then the item named "test-article-item" should not be published

  Scenario: Hide unpublished new item from CMS users
    Given there are items:
      | name              | type    | version | stage     |
      | test-article-item | article | new     | authoring |
     When I am logged in as "admin"
     Then the item named "test-article-item" should not be published

  Scenario: Hide unpublished item update from site visitors
    Given there are items:
      | name              | type    | version | stage     |
      | test-article-item | article | update  | authoring |
     When I am logged out
     Then the item named "test-article-item" should be published at version 1

  Scenario: Hide unpublished item update from CMS users
    Given there are items:
      | name              | type    | version | stage     |
      | test-article-item | article | update  | authoring |
     When I am logged in as "admin"
     Then the item named "test-article-item" should be published at version 1

  Scenario: Delete new item from authoring workflow
    Given there are items:
      | name              | type    | version | stage     |
      | test-article-item | article | new     | authoring |
     When I am logged in as "author"
      And I delete the item named "test-article-item" from my authoring workflow
     Then I should not see the item named "test-article-item" anywhere in workflow

  Scenario: Delete item update from authoring workflow
    Given there are items:
      | name              | type    | version | stage     |
      | test-article-item | article | update  | authoring |
     When I am logged in as "author"
      And I delete the item named "test-article-item" from my authoring workflow
     Then I should not see the item named "test-article-item" anywhere in workflow
