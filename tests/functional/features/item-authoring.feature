Feature: Item authoring
In order to provide useful content to visitors
As a content author
I want to be able to create new and edit existing items of content

  Background:
    Given a fresh install of the CMS
      And there are users:
      | username  | roles     |
      | admin     | admin     |
      | author    | author    |
      | publisher | publisher |

  Scenario: Author a new item and save for later
     When I am logged in as "author"
      And I create and save item:
      | name | test-article-item |
      | type | article           |
     Then I should see in my authoring workflow:
      | name              | type    | version | stage     |
      | test-article-item | article | new     | authoring |

  Scenario: Author a new item and submit for publishing
     When I am logged in as "author"
      And I create and publish item:
      | name | test-article-item |
      | type | article           |
     Then I should see in my authoring workflow:
      | name              | type    | version | stage      |
      | test-article-item | article | new     | publishing |

  Scenario: Submit a previously saved new item for publishing
     When I am logged in as "author"
      And I create and save item:
      | name | test-article-item |
      | type | article           |
      And I edit and publish the item named "test-article-item" from my authoring workflow
     Then I should see in my authoring workflow:
      | name              | type    | version | stage      |
      | test-article-item | article | new     | publishing |

  Scenario: Author an item update and save for later
    Given there are items:
      | name              | type    | version | stage     |
      | test-article-item | article | new     | published |
     When I am logged in as "author"
      And I edit and save the item named "test-article-item"
     Then I should see in my authoring workflow:
      | name              | type    | version | stage     |
      | test-article-item | article | update  | authoring |

  Scenario: Author an item update and submit for publishing
    Given there are items:
      | name              | type    | version | stage     |
      | test-article-item | article | new     | published |
     When I am logged in as "author"
      And I edit and publish the item named "test-article-item"
     Then I should see in my authoring workflow:
      | name              | type    | version | stage      |
      | test-article-item | article | update  | publishing |

  Scenario: Submit a previously saved item update for publishing
    Given there are items:
      | name              | type    | version | stage     |
      | test-article-item | article | new     | published |
     When I am logged in as "author"
      And I edit and save the item named "test-article-item"
      And I edit and publish the item named "test-article-item" from my authoring workflow
     Then I should see in my authoring workflow:
      | name              | type    | version | stage      |
      | test-article-item | article | update  | publishing |

  Scenario: Prevent loss of new item when created and, for whatever reason, author navigates away from edit form
     When I am logged in as "author"
     When I create item:
      | name | test-article-item |
      | type | article           |
      And I navigate away
     Then I should see in my authoring workflow:
      | name              | type    | version | stage     |
      | test-article-item | article | new     | authoring |

  Scenario: Prevent the creation of a new item with the same name as an existing new item
    Given there are items:
      | name              | type    | version | stage      |
      | test-article-item | article | new     | publishing |
     When I am logged in as "author"
      And I create item:
      | name | test-article-item |
      | type | article           |
     Then I should see "A record with this value already exists"

  Scenario: Prevent the creation of a new item with the same name as an existing published item
    Given there are items:
      | name              | type    | version | stage     |
      | test-article-item | article | new     | published |
     When I am logged in as "author"
      And I create item:
      | name | test-article-item |
      | type | article           |
     Then I should see "A record with this value already exists"

  @javascript
  Scenario: Preview a new item
     When I am logged in as "author"
      And I create and preview item content:
      | name | test-article-item |
      | type | article           |
     Then the item named "test-article-item" should be previewing at version 1

  @javascript
  Scenario: Preview an item update
    Given there are items:
      | name              | type    | version | stage     |
      | test-article-item | article | new     | published |
     When I am logged in as "author"
      And I edit and preview the item named "test-article-item"
     Then the item named "test-article-item" should be previewing at version 2
