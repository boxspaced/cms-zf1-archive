Feature: Item publishing settings
In order to provide useful content to visitors
As a content publisher
I want to be able to control the settings of an item during publishing

  Background:
    Given a fresh install of the CMS
      And there are users:
      | username  | roles     |
      | admin     | admin     |
      | author    | author    |
      | publisher | publisher |

  Scenario: Publish item with a specified name
     When I am logged in as "publisher"
      And I create and publish item:
      | name | test-article-item |
      | type | article           |
     Then the item named "test-article-item" should be published at version 1

  Scenario: Republish item with a different name
    Given there are items:
      | name              | type    | version | stage     |
      | test-article-item | article | new     | published |
     When I am logged in as "publisher"
      And I edit publishing and publish the item named "test-article-item":
      | name | test-article-item-changed |
     Then the item named "test-article-item" should not be published

  Scenario: Publish item with a specified colour scheme
     When I am logged in as "publisher"
      And I create and publish item:
      | name         | test-article-item |
      | type         | article           |
      | colourScheme | red               |
     Then the item named "test-article-item" should be published with the "red" colour scheme

  @javascript
  Scenario: Preview item colour scheme
     When I am logged in as "publisher"
      And I create and preview item publishing:
      | name         | test-article-item |
      | type         | article           |
      | colourScheme | red               |
     Then the item named "test-article-item" should be previewing with the "red" colour scheme

  @javascript
  Scenario: Preview item with a different colour scheme
    Given there are items:
      | name              | type    | version | stage     |
      | test-article-item | article | new     | published |
     When I am logged in as "publisher"
      And I edit publishing and preview the item named "test-article-item":
      | colourScheme | red |
     Then the item named "test-article-item" should be previewing with the "red" colour scheme

  Scenario: Republish item with a different colour scheme
    Given there are items:
      | name              | type    | version | stage     |
      | test-article-item | article | new     | published |
     When I am logged in as "publisher"
      And I edit publishing and publish the item named "test-article-item":
      | colourScheme | red |
     Then the item named "test-article-item" should be published with the "red" colour scheme

  Scenario: Publish item with a specified template
     When I am logged in as "publisher"
      And I create and publish item:
      | name       | test-article-item |
      | type       | article           |
      | templateId | testing-only      |
     Then the item named "test-article-item" should be published with the "testing-only" template

  Scenario: Republish item with a different template
    Given there are items:
      | name              | type    | version | stage     | template         |
      | test-article-item | article | new     | published | two-column-equal |
     When I am logged in as "publisher"
      And I edit publishing and publish the item named "test-article-item":
      | templateId | testing-only |
     Then the item named "test-article-item" should be published with the "testing-only" template

  @javascript
  Scenario: Preview item template
     When I am logged in as "publisher"
      And I create and preview item publishing:
      | name       | test-article-item |
      | type       | article           |
      | templateId | testing-only      |
     Then the item named "test-article-item" should be previewing with the "testing-only" template

  @javascript
  Scenario: Preview item with a different template
    Given there are items:
      | name              | type    | version | stage     | template         |
      | test-article-item | article | new     | published | two-column-equal |
     When I am logged in as "publisher"
      And I edit publishing and preview the item named "test-article-item":
      | templateId | testing-only |
     Then the item named "test-article-item" should be previewing with the "testing-only" template
