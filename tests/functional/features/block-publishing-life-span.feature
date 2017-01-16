Feature: Block publishing life span
In order to provide time relevant content
As a content publisher
I want to be able to control when blocks of content are available to site visitors

  Background:
    Given a fresh install of the CMS
      And there are users:
      | username  | roles     |
      | admin     | admin     |
      | author    | author    |
      | publisher | publisher |

  Scenario: Publish block with a live-from date in the future
     When I am logged in as "publisher"
      And I create and publish block:
      | name       | test-html-block |
      | type       | html            |
      | liveFrom   | tomorrow        |
      | expiresEnd | +2 years        |
     Then the block named "test-html-block" should be offline and due to come online "tomorrow"

  Scenario: Publish block with an expires-end date in the past
     When I am logged in as "publisher"
      And I create and publish block:
      | name       | test-html-block |
      | type       | html            |
      | liveFrom   | -2 years        |
      | expiresEnd | yesterday       |
     Then the block named "test-html-block" should have expired "yesterday"

  Scenario: Publish block that is live now and never expires
     When I am logged in as "publisher"
      And I create and publish block:
      | name       | test-html-block |
      | type       | html            |
      | liveFrom   | -2 years        |
      | expiresEnd | 2038-01-19      |
     Then the block named "test-html-block" should be online and never expiring

  Scenario: Publish block that is live now and expires on a specific date
     When I am logged in as "publisher"
      And I create and publish block:
      | name       | test-html-block |
      | type       | html            |
      | liveFrom   | -2 years        |
      | expiresEnd | tomorrow        |
     Then the block named "test-html-block" should be online and expiring "tomorrow"

  Scenario: Edit live-from date of block
    Given there are blocks:
      | name            | type | version | stage     | liveFrom  | expiresEnd |
      | test-html-block | html | new     | published | yesterday | 2038-01-19 |
      And I am logged in as "publisher"
     When I edit publishing and publish the block named "test-html-block":
      | liveFrom   | tomorrow   |
      | expiresEnd | 2038-01-19 |
     Then the block named "test-html-block" should be offline and due to come online "tomorrow"

  Scenario: Edit expires-end date of block
    Given there are blocks:
      | name            | type | version | stage     | liveFrom  | expiresEnd |
      | test-html-block | html | new     | published | yesterday | 2038-01-19 |
      And I am logged in as "publisher"
     When I edit publishing and publish the block named "test-html-block":
      | liveFrom   | yesterday |
      | expiresEnd | tomorrow  |
     Then the block named "test-html-block" should be online and expiring "tomorrow"

  @javascript
  Scenario: Prevent site visitor from seeing block with live-from date in the future
    Given there are blocks:
      | name            | type | version | stage     | liveFrom | expiresEnd |
      | test-html-block | html | new     | published | tomorrow | 2038-01-19 |
     Then the block named "test-html-block" should not be published

  @javascript
  Scenario: Prevent site visitor from seeing block with expires-end date in the past
    Given there are blocks:
      | name            | type | version | stage     | liveFrom | expiresEnd |
      | test-html-block | html | new     | published | -2 years | yesterday  |
     Then the block named "test-html-block" should not be published

  @javascript
  Scenario: Allow site visitor to see block that is live now and never expires
    Given there are blocks:
      | name            | type | version | stage     | liveFrom | expiresEnd |
      | test-html-block | html | new     | published | -2 years | 2038-01-19 |
     Then the block named "test-html-block" should be published at version 1
