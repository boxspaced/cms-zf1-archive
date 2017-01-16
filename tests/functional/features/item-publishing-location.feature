@javascript
Feature: Item publishing location
In order to make it easy for visitors to find content
As a content publisher
I want to be able to set the location of an item during publishing

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

  Scenario: Publish item to standalone from workflow
     When I am logged in as "publisher"
      And I create and publish item:
      | name | standalone-article |
      | type | article            |
      | to   | Standalone         |
     Then I should see the content named "standalone-article" published to standalone

  Scenario: Prevent item being republished to standalone when it has child menu items
     When I am logged in as "publisher"
     Then I should not be able to republish the item named "first-top-level-article" to standalone

  Scenario: Publish item at the top-level of menu from workflow
     When I am logged in as "publisher"
      And I create and publish item:
      | name              | fifth-top-level-article |
      | type              | article                 |
      | to                | Menu                    |
      | beneathMenuItemId | Top level               |
     Then I should see the content named "fifth-top-level-article" in the top-level menu

  Scenario: Publish item at second-level of menu from workflow
     When I am logged in as "publisher"
      And I create and publish item:
      | name              | fifth-second-level-article |
      | type              | article                    |
      | to                | Menu                       |
      | beneathMenuItemId | first-top-level-article    |
     Then I should see the content named "fifth-second-level-article" in the sub menu of the content named "first-top-level-article"

  Scenario: Publish item at the last-level of menu from workflow
     When I am logged in as "publisher"
      And I create and publish item:
      | name              | fifth-eighth-level-article |
      | type              | article                    |
      | to                | Menu                       |
      | beneathMenuItemId | seventh-level-article      |
     Then I should see the content named "fifth-eighth-level-article" in the sub menu of the content named "seventh-level-article"

  Scenario: Publish item at the top-level of menu from menu manager
     When I am logged in as "publisher"
      And I create and publish item at top-level from menu manager:
      | name | fifth-top-level-article |
      | type | article                 |
     Then I should see the content named "fifth-top-level-article" in the top-level menu

  Scenario: Publish item at second-level of menu from menu manager
     When I am logged in as "publisher"
      And I create and publish item beneath "first-top-level-article" from menu manager:
      | name | fifth-second-level-article |
      | type | article                    |
     Then I should see the content named "fifth-second-level-article" in the sub menu of the content named "first-top-level-article"

  Scenario: Publish item at the last-level of menu from menu manager
     When I am logged in as "publisher"
      And I create and publish item beneath "seventh-level-article" from menu manager:
      | name | fifth-eighth-level-article |
      | type | article                    |
     Then I should see the content named "fifth-eighth-level-article" in the sub menu of the content named "seventh-level-article"

  Scenario: Prevent item being published beyond maximum menu level defined in config
     When I am logged in as "publisher"
     Then I should not be able to publish a new "article" item to menu beneath content named "first-eighth-level-article" from menu manager
      And I should not be able to publish a new "article" item to menu beneath content named "first-eighth-level-article" from workflow

  Scenario: Prevent item being republished to a position where its child menu items would go beyond the maximum level
    Given there are items:
      | name                         | type    | version | stage     | publishTo | publishBeneathMenuItem  |
      | fifth-top-level-article      | article | new     | published | Menu      |                         |
      | another-second-level-article | article | new     | published | Menu      | fifth-top-level-article |
     When I am logged in as "publisher"
     Then I should not be able to republish the item named "fifth-top-level-article" to menu beneath the content named "seventh-level-article"
      And I should not be able to republish the item named "fifth-top-level-article" to menu beneath the content named "first-eighth-level-article"
      And I should not be able to republish the item named "fifth-top-level-article" to menu beneath the content named "second-eighth-level-article"
      And I should not be able to republish the item named "fifth-top-level-article" to menu beneath the content named "third-eighth-level-article"
      And I should not be able to republish the item named "fifth-top-level-article" to menu beneath the content named "fourth-eighth-level-article"

  Scenario: Allow item with child items to be republished when the child menu items do not go beyond the maximum level
    Given there are items:
      | name                         | type    | version | stage     | publishTo | publishBeneathMenuItem  |
      | fifth-top-level-article      | article | new     | published | Menu      |                         |
      | another-second-level-article | article | new     | published | Menu      | fifth-top-level-article |
     When I am logged in as "publisher"
      And I edit publishing and publish the item named "fifth-top-level-article":
      | to                | Menu                |
      | beneathMenuItemId | sixth-level-article |
     Then I should see the content named "fifth-top-level-article" in the sub menu of the content named "sixth-level-article"
      And I should see the content named "another-second-level-article" in the sub menu of the content named "fifth-top-level-article"

  Scenario: Prevent item being republished beneath itself
     When I am logged in as "publisher"
     Then I should not be able to republish the item named "fifth-level-article" to menu beneath the content named "fifth-level-article"

  Scenario: Prevent item being republished beneath one of its own child menu items
     When I am logged in as "publisher"
     Then I should not be able to republish the item named "fifth-level-article" to menu beneath the content named "sixth-level-article"
      And I should not be able to republish the item named "fifth-level-article" to menu beneath the content named "seventh-level-article"
      And I should not be able to republish the item named "fifth-level-article" to menu beneath the content named "first-eighth-level-article"
      And I should not be able to republish the item named "fifth-level-article" to menu beneath the content named "second-eighth-level-article"
      And I should not be able to republish the item named "fifth-level-article" to menu beneath the content named "third-eighth-level-article"
      And I should not be able to republish the item named "fifth-level-article" to menu beneath the content named "fourth-eighth-level-article"

  Scenario: Prevent deletion of item when it has child menu items
     When I am logged in as "publisher"
      And I attempt to delete the item named "first-top-level-article"
     Then I should see the content named "first-top-level-article" in the top-level menu

  Scenario: Prevent deletion of item when it has child menu items from menu manager
     When I am logged in as "publisher"
      And I attempt to delete the item named "first-top-level-article" from menu manager
     Then I should see the content named "first-top-level-article" in the top-level menu

  Scenario: Republish standalone item to the top-level menu
    Given there are items:
      | name               | type    | version | stage     |
      | standalone-article | article | new     | published |
     When I am logged in as "publisher"
      And I edit publishing and publish the item named "standalone-article":
      | to                | Menu |
      | beneathMenuItemId |      |
     Then I should see the content named "standalone-article" in the top-level menu

  Scenario: Republish standalone item to menu
    Given there are items:
      | name               | type    | version | stage     |
      | standalone-article | article | new     | published |
     When I am logged in as "publisher"
      And I edit publishing and publish the item named "standalone-article":
      | to                | Menu                |
      | beneathMenuItemId | sixth-level-article |
     Then I should see the content named "standalone-article" in the sub menu of the content named "sixth-level-article"

  Scenario: Delete item published to top-level menu
     When I am logged in as "publisher"
      And I delete the item named "fourth-top-level-article"
     Then I should not see "fourth-top-level-article" anywhere in system

  Scenario: Delete item published to menu
     When I am logged in as "publisher"
      And I delete the item named "fourth-second-level-article"
     Then I should not see "fourth-second-level-article" anywhere in system
