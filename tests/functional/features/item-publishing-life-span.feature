Feature: Item publishing life span
In order to provide time relevant content
As a content publisher
I want to be able to control when items of content are available to site visitors

  Background:
    Given a fresh install of the CMS
      And there are users:
      | username  | roles     |
      | admin     | admin     |
      | author    | author    |
      | publisher | publisher |

  Scenario: Publish item with a live-from date in the future
     When I am logged in as "publisher"
      And I create and publish item:
      | name       | test-article-item |
      | type       | article           |
      | liveFrom   | tomorrow          |
      | expiresEnd | +2 years          |
     Then the item named "test-article-item" should be offline and due to come online "tomorrow"

  Scenario: Publish item with an expires-end date in the past
     When I am logged in as "publisher"
      And I create and publish item:
      | name       | test-article-item |
      | type       | article           |
      | liveFrom   | -2 years          |
      | expiresEnd | yesterday         |
     Then the item named "test-article-item" should have expired "yesterday"

  Scenario: Publish item that is live now and never expires
     When I am logged in as "publisher"
      And I create and publish item:
      | name       | test-article-item |
      | type       | article           |
      | liveFrom   | -2 years          |
      | expiresEnd | 2038-01-19        |
     Then the item named "test-article-item" should be online and never expiring

  Scenario: Publish item that is live now and expires on a specific date
     When I am logged in as "publisher"
      And I create and publish item:
      | name       | test-article-item |
      | type       | article           |
      | liveFrom   | -2 years          |
      | expiresEnd | tomorrow          |
     Then the item named "test-article-item" should be online and expiring "tomorrow"

  Scenario: Edit live-from date of item
    Given there are items:
      | name              | type    | version | stage     | liveFrom  | expiresEnd |
      | test-article-item | article | new     | published | yesterday | 2038-01-19 |
     When I am logged in as "publisher"
      And I edit publishing and publish the item named "test-article-item":
      | liveFrom   | tomorrow   |
      | expiresEnd | 2038-01-19 |
     Then the item named "test-article-item" should be offline and due to come online "tomorrow"

  Scenario: Edit expires-end date of item
    Given there are items:
      | name              | type    | version | stage     | liveFrom  | expiresEnd |
      | test-article-item | article | new     | published | yesterday | 2038-01-19 |
     When I am logged in as "publisher"
      And I edit publishing and publish the item named "test-article-item":
      | liveFrom   | yesterday |
      | expiresEnd | tomorrow  |
     Then the item named "test-article-item" should be online and expiring "tomorrow"

  Scenario: Allow CMS users to see item with live-from date in the future
    Given there are items:
      | name              | type    | version | stage     | liveFrom | expiresEnd |
      | test-article-item | article | new     | published | tomorrow | 2038-01-19 |
     When I am logged in as "author"
     Then the item named "test-article-item" should be published at version 1

  Scenario: Allow CMS users to see item with expires-end date in the past
    Given there are items:
      | name              | type    | version | stage     | liveFrom | expiresEnd |
      | test-article-item | article | new     | published | -2 years | yesterday  |
     When I am logged in as "author"
     Then the item named "test-article-item" should be published at version 1

  Scenario: Allow CMS users to see item that is live now and never expires
    Given there are items:
      | name              | type    | version | stage     | liveFrom | expiresEnd |
      | test-article-item | article | new     | published | -2 years | 2038-01-19 |
     When I am logged in as "author"
     Then the item named "test-article-item" should be published at version 1

  Scenario: Prevent site visitor from seeing item with live-from date in the future
    Given there are items:
      | name              | type    | version | stage     | liveFrom | expiresEnd |
      | test-article-item | article | new     | published | tomorrow | 2038-01-19 |
     Then the item named "test-article-item" should not be published

  Scenario: Prevent site visitor from seeing item with expires-end date in the past
    Given there are items:
      | name              | type    | version | stage     | liveFrom | expiresEnd |
      | test-article-item | article | new     | published | -2 years | yesterday  |
     Then the item named "test-article-item" should not be published

  Scenario: Allow site visitor to see item that is live now and never expires
    Given there are items:
      | name              | type    | version | stage     | liveFrom | expiresEnd |
      | test-article-item | article | new     | published | -2 years | 2038-01-19 |
     Then the item named "test-article-item" should be published at version 1
