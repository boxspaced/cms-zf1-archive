@javascript
Feature: Item publishing blocks
In order to promote and inform vistors of additional site content
As a content publisher
I want to be able to assign and organize smaller reusable blocks of content within items

  Background:
    Given a fresh install of the CMS
      And there are users:
      | username  | roles     |
      | admin     | admin     |
      | author    | author    |
      | publisher | publisher |
      And there are blocks:
      | name                  | type | version | stage     |
      | test-html-block-one   | html | new     | published |
      | test-html-block-two   | html | new     | published |
      | test-html-block-three | html | new     | published |
      | test-html-block-four  | html | new     | published |

  Scenario: Assign block to a block sequence
    Given there are items:
      | name              | type    | version | stage     |
      | test-article-item | article | new     | published |
     When I am logged in as "publisher"
      And I edit publishing and publish the item named "test-article-item":
      | rightColumn1 | test-html-block-one |
     Then the block named "test-html-block-one" should be found 1 times within the item named "test-article-item"

  Scenario: Assign multiple blocks to a block sequence
    Given there are items:
      | name              | type    | version | stage     |
      | test-article-item | article | new     | published |
     When I am logged in as "publisher"
      And I edit publishing and publish the item named "test-article-item":
      | rightColumn1 | test-html-block-one   |
      | rightColumn2 | test-html-block-two   |
      | rightColumn3 | test-html-block-three |
     Then I should see the block named "test-html-block-one" before the block named "test-html-block-two" in the "rightColumn" block sequence within the item named "test-article-item"
      And I should see the block named "test-html-block-two" before the block named "test-html-block-three" in the "rightColumn" block sequence within the item named "test-article-item"

  Scenario: Change block assigned to a block sequence
    Given there are items:
      | name              | type    | version | stage     | rightColumn                                                   |
      | test-article-item | article | new     | published | test-html-block-one,test-html-block-two,test-html-block-three |
     When I am logged in as "publisher"
      And I change the block named "test-html-block-two" to the block named "test-html-block-four" in the "rightColumn" block sequence within the item named "test-article-item"
     Then I should see the block named "test-html-block-one" before the block named "test-html-block-four" in the "rightColumn" block sequence within the item named "test-article-item"
      And I should see the block named "test-html-block-four" before the block named "test-html-block-three" in the "rightColumn" block sequence within the item named "test-article-item"

  Scenario: Assign block to a free block
    Given there are items:
      | name              | type    | version | stage     |
      | test-article-item | article | new     | published |
     When I am logged in as "publisher"
      And I edit publishing and publish the item named "test-article-item":
      | lowerPromo | test-html-block-one |
     Then the block named "test-html-block-one" should be found 1 times within the item named "test-article-item"

  Scenario: Change block assigned to free block
    Given there are items:
      | name              | type    | version | stage     | lowerPromo          |
      | test-article-item | article | new     | published | test-html-block-one |
     When I am logged in as "publisher"
      And I edit publishing and publish the item named "test-article-item":
      | lowerPromo | test-html-block-two |
     Then the block named "test-html-block-two" should be found 1 times within the item named "test-article-item"

  Scenario: Assign the same block to multiple positions
    Given there are items:
      | name              | type    | version | stage     |
      | test-article-item | article | new     | published |
     When I am logged in as "publisher"
      And I edit publishing and publish the item named "test-article-item":
      | leftColumn1  | test-html-block-one |
      | mainImage    | test-html-block-one |
      | lowerPromo   | test-html-block-one |
      | rightColumn1 | test-html-block-one |
     Then the block named "test-html-block-one" should be found 4 times within the item named "test-article-item"

  Scenario: Shuffle block down in block sequence
    Given there are items:
      | name              | type    | version | stage     | rightColumn                                                   |
      | test-article-item | article | new     | published | test-html-block-one,test-html-block-two,test-html-block-three |
     When I am logged in as "publisher"
      And I shuffle the block named "test-html-block-one" down in the "rightColumn" block sequence within the item named "test-article-item"
     Then I should see the block named "test-html-block-one" after the block named "test-html-block-two" in the "rightColumn" block sequence within the item named "test-article-item"

  Scenario: Shuffle block up in block sequence
    Given there are items:
      | name              | type    | version | stage     | rightColumn                                                   |
      | test-article-item | article | new     | published | test-html-block-one,test-html-block-two,test-html-block-three |
     When I am logged in as "publisher"
      And I shuffle the block named "test-html-block-three" up in the "rightColumn" block sequence within the item named "test-article-item"
     Then I should see the block named "test-html-block-three" before the block named "test-html-block-two" in the "rightColumn" block sequence within the item named "test-article-item"

  Scenario: Shuffle first block up so becomes last in block sequence
    Given there are items:
      | name              | type    | version | stage     | rightColumn                                                   |
      | test-article-item | article | new     | published | test-html-block-one,test-html-block-two,test-html-block-three |
     When I am logged in as "publisher"
      And I shuffle the block named "test-html-block-one" up in the "rightColumn" block sequence within the item named "test-article-item"
     Then I should see the block named "test-html-block-one" after the block named "test-html-block-three" in the "rightColumn" block sequence within the item named "test-article-item"

  Scenario: Shuffle last block down so becomes first in block sequence
    Given there are items:
      | name              | type    | version | stage     | rightColumn                                                   |
      | test-article-item | article | new     | published | test-html-block-one,test-html-block-two,test-html-block-three |
     When I am logged in as "publisher"
      And I shuffle the block named "test-html-block-three" down in the "rightColumn" block sequence within the item named "test-article-item"
     Then I should see the block named "test-html-block-three" before the block named "test-html-block-one" in the "rightColumn" block sequence within the item named "test-article-item"

  Scenario: Remove first block from block sequence
    Given there are items:
      | name              | type    | version | stage     | rightColumn                                                   |
      | test-article-item | article | new     | published | test-html-block-one,test-html-block-two,test-html-block-three |
     When I am logged in as "publisher"
      And I remove the block named "test-html-block-one" from the "rightColumn" block sequence within the item named "test-article-item"
     Then the block named "test-html-block-one" should not be published within the item named "test-article-item"

  Scenario: Remove middle block from block sequence
    Given there are items:
      | name              | type    | version | stage     | rightColumn                                                   |
      | test-article-item | article | new     | published | test-html-block-one,test-html-block-two,test-html-block-three |
     When I am logged in as "publisher"
      And I remove the block named "test-html-block-two" from the "rightColumn" block sequence within the item named "test-article-item"
     Then the block named "test-html-block-two" should not be published within the item named "test-article-item"

  Scenario: Remove last block from block sequence
    Given there are items:
      | name              | type    | version | stage     | rightColumn                                                   |
      | test-article-item | article | new     | published | test-html-block-one,test-html-block-two,test-html-block-three |
     When I am logged in as "publisher"
      And I remove the block named "test-html-block-three" from the "rightColumn" block sequence within the item named "test-article-item"
     Then the block named "test-html-block-three" should not be published within the item named "test-article-item"

  Scenario: Delete first block from block sequence
    Given there are items:
      | name              | type    | version | stage     | rightColumn                                                   |
      | test-article-item | article | new     | published | test-html-block-one,test-html-block-two,test-html-block-three |
     When I am logged in as "publisher"
      And I delete the block named "test-html-block-one" from the "rightColumn" block sequence within the item named "test-article-item"
     Then the block named "test-html-block-one" should not be published within the item named "test-article-item"

  Scenario: Delete middle block from block sequence
    Given there are items:
      | name              | type    | version | stage     | rightColumn                                                   |
      | test-article-item | article | new     | published | test-html-block-one,test-html-block-two,test-html-block-three |
     When I am logged in as "publisher"
      And I delete the block named "test-html-block-two" from the "rightColumn" block sequence within the item named "test-article-item"
     Then the block named "test-html-block-two" should not be published within the item named "test-article-item"

  Scenario: Delete last block from block sequence
    Given there are items:
      | name              | type    | version | stage     | rightColumn                                                   |
      | test-article-item | article | new     | published | test-html-block-one,test-html-block-two,test-html-block-three |
     When I am logged in as "publisher"
      And I delete the block named "test-html-block-three" from the "rightColumn" block sequence within the item named "test-article-item"
     Then the block named "test-html-block-three" should not be published within the item named "test-article-item"

  Scenario: Remove free block
    Given there are items:
      | name              | type    | version | stage     | lowerPromo          |
      | test-article-item | article | new     | published | test-html-block-one |
     When I am logged in as "publisher"
      And I edit publishing and publish the item named "test-article-item":
      | lowerPromo |  |
     Then the block named "test-html-block-one" should not be published within the item named "test-article-item"

  Scenario: Preview item blocks
     When I am logged in as "publisher"
      And I create and preview item publishing:
      | name         | test-article-item   |
      | type         | article             |
      | leftColumn1  | test-html-block-one |
      | mainImage    | test-html-block-one |
      | lowerPromo   | test-html-block-one |
      | rightColumn1 | test-html-block-one |
     Then the block named "test-html-block-one" should be found 4 times within preview of the item named "test-article-item"

  Scenario: Preview item with different blocks
    Given there are items:
      | name              | type    | version | stage     |
      | test-article-item | article | new     | published |
     When I am logged in as "publisher"
      And I edit publishing and preview the item named "test-article-item":
      | leftColumn1  | test-html-block-one |
      | mainImage    | test-html-block-one |
      | lowerPromo   | test-html-block-one |
      | rightColumn1 | test-html-block-one |
     Then the block named "test-html-block-one" should be found 4 times within preview of the item named "test-article-item"
