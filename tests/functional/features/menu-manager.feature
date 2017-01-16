@javascript
Feature: Menu manager
In order to help site visitors find content quickly
As a content publisher
I want to be able organize related content in hierarchical site navigation

  Background:
    Given a fresh install of the CMS
      And the menu is empty
      And there are users:
      | username  | roles     |
      | admin     | admin     |
      | publisher | publisher |
      | author    | author    |
      And there are items:
      | name                        | type    | version | stage     | publishTo | publishBeneathMenuItem     |
      | first-top-level-article     | article | new     | published | Menu      |                            |
      | first-second-level-article  | article | new     | published | Menu      | first-top-level-article    |
      | third-level-article         | article | new     | published | Menu      | first-second-level-article |
      | fourth-level-article        | article | new     | published | Menu      | third-level-article        |
      | fifth-level-article         | article | new     | published | Menu      | fourth-level-article       |
      | sixth-level-article         | article | new     | published | Menu      | fifth-level-article        |
      | seventh-level-article       | article | new     | published | Menu      | sixth-level-article        |
      | first-eighth-level-article  | article | new     | published | Menu      | seventh-level-article      |
      | second-eighth-level-article | article | new     | published | Menu      | seventh-level-article      |
      | third-eighth-level-article  | article | new     | published | Menu      | seventh-level-article      |
      | fourth-eighth-level-article | article | new     | published | Menu      | seventh-level-article      |
      | second-second-level-article | article | new     | published | Menu      | first-top-level-article    |
      | third-second-level-article  | article | new     | published | Menu      | first-top-level-article    |
      | fourth-second-level-article | article | new     | published | Menu      | first-top-level-article    |
      | second-top-level-article    | article | new     | published | Menu      |                            |
      | third-top-level-article     | article | new     | published | Menu      |                            |
      | fourth-top-level-article    | article | new     | published | Menu      |                            |

  Scenario: Shuffle order of top-level menu item up
     When I am logged in as "publisher"
      And I shuffle the content named "second-top-level-article" up from menu manager
     Then I should see the content named "second-top-level-article" before the content named "first-top-level-article" in the top-level menu

  Scenario: Shuffle order of top-level menu item down
     When I am logged in as "publisher"
      And I shuffle the content named "third-top-level-article" down from menu manager
     Then I should see the content named "fourth-top-level-article" before the content named "third-top-level-article" in the top-level menu

  Scenario: Shuffle order of second-level menu item up
     When I am logged in as "publisher"
      And I shuffle the content named "second-second-level-article" up from menu manager
     Then I should see the content named "second-second-level-article" before the content named "first-second-level-article" in the sub menu of the content named "first-top-level-article"

  Scenario: Shuffle order of second-level menu item down
     When I am logged in as "publisher"
      And I shuffle the content named "third-second-level-article" down from menu manager
     Then I should see the content named "fourth-second-level-article" before the content named "third-second-level-article" in the sub menu of the content named "first-top-level-article"

  Scenario: Shuffle order of last-level menu item up
     When I am logged in as "publisher"
      And I shuffle the content named "second-eighth-level-article" up from menu manager
     Then I should see the content named "second-eighth-level-article" before the content named "first-eighth-level-article" in the sub menu of the content named "seventh-level-article"

  Scenario: Shuffle order of last-level menu item down
     When I am logged in as "publisher"
      And I shuffle the content named "third-eighth-level-article" down from menu manager
     Then I should see the content named "fourth-eighth-level-article" before the content named "third-eighth-level-article" in the sub menu of the content named "seventh-level-article"

  Scenario: Prevent shuffling up of first top-level menu item
     When I am logged in as "publisher"
      And I attempt to shuffle the content named "first-top-level-article" up from menu manager
     Then I should see the content named "first-top-level-article" before the content named "second-top-level-article" in the top-level menu

  Scenario: Prevent shuffling down of last top-level menu item
     When I am logged in as "publisher"
      And I attempt to shuffle the content named "fourth-top-level-article" down from menu manager
     Then I should see the content named "third-top-level-article" before the content named "fourth-top-level-article" in the top-level menu

  Scenario: Prevent shuffling up of first second-level menu item
     When I am logged in as "publisher"
      And I attempt to shuffle the content named "first-second-level-article" up from menu manager
     Then I should see the content named "first-second-level-article" before the content named "second-second-level-article" in the sub menu of the content named "first-top-level-article"

  Scenario: Prevent shuffling down of last second-level menu item
     When I am logged in as "publisher"
      And I attempt to shuffle the content named "fourth-second-level-article" down from menu manager
     Then I should see the content named "third-second-level-article" before the content named "fourth-second-level-article" in the sub menu of the content named "first-top-level-article"

  Scenario: Prevent shuffling up of first last-level menu item
     When I am logged in as "publisher"
      And I attempt to shuffle the content named "first-eighth-level-article" up from menu manager
     Then I should see the content named "first-eighth-level-article" before the content named "second-eighth-level-article" in the sub menu of the content named "seventh-level-article"

  Scenario: Prevent shuffling down of last last-level menu item
     When I am logged in as "publisher"
      And I attempt to shuffle the content named "fourth-eighth-level-article" down from menu manager
     Then I should see the content named "third-eighth-level-article" before the content named "fourth-eighth-level-article" in the sub menu of the content named "seventh-level-article"
