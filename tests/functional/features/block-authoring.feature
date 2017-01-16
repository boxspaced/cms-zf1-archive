Feature: Block authoring
In order to provide useful content to visitors
As a content author
I want to be able to create new and edit existing blocks of content

  Background:
    Given a fresh install of the CMS
      And there are users:
      | username  | roles     |
      | admin     | admin     |
      | author    | author    |
      | publisher | publisher |

  Scenario: Author a new block and save for later
     When I am logged in as "author"
      And I create and save block:
      | name | test-html-block |
      | type | html            |
     Then I should see in my authoring workflow:
      | name            | type | version | stage     |
      | test-html-block | html | new     | authoring |

  Scenario: Author a new block and submit for publishing
     When I am logged in as "author"
      And I create and publish block:
      | name | test-html-block |
      | type | html            |
     Then I should see in my authoring workflow:
      | name            | type | version | stage      |
      | test-html-block | html | new     | publishing |

  Scenario: Submit a previously saved new block for publishing
     When I am logged in as "author"
      And I create and save block:
      | name | test-html-block |
      | type | html            |
      And I edit and publish the block named "test-html-block" from my authoring workflow
     Then I should see in my authoring workflow:
      | name            | type | version | stage      |
      | test-html-block | html | new     | publishing |

  Scenario: Author an block update and save for later
    Given there are blocks:
      | name            | type | version | stage     |
      | test-html-block | html | new     | published |
      And I am logged in as "author"
     When I edit and save the block named "test-html-block"
     Then I should see in my authoring workflow:
      | name            | type | version | stage     |
      | test-html-block | html | update  | authoring |

  Scenario: Author an block update and submit for publishing
    Given there are blocks:
      | name            | type | version | stage     |
      | test-html-block | html | new     | published |
      And I am logged in as "author"
     When I edit and publish the block named "test-html-block"
     Then I should see in my authoring workflow:
      | name            | type | version | stage      |
      | test-html-block | html | update  | publishing |

  Scenario: Submit a previously saved block update for publishing
    Given there are blocks:
      | name            | type | version | stage     |
      | test-html-block | html | new     | published |
      And I am logged in as "author"
     When I edit and save the block named "test-html-block"
      And I edit and publish the block named "test-html-block" from my authoring workflow
     Then I should see in my authoring workflow:
      | name            | type | version | stage      |
      | test-html-block | html | update  | publishing |

  Scenario: Prevent loss of new block when created and, for whatever reason, author navigates away from edit form
     When I am logged in as "author"
      And I create block:
      | name | test-html-block |
      | type | html            |
      And I navigate away
     Then I should see in my authoring workflow:
      | name            | type | version | stage     |
      | test-html-block | html | new     | authoring |

  Scenario: Prevent the creation of a new block with the same name as an existing new block
    Given there are blocks:
      | name            | type | version | stage      |
      | test-html-block | html | new     | publishing |
      And I am logged in as "author"
     When I create block:
      | name | test-html-block |
      | type | html            |
     Then I should see "A record with this value already exists"

  Scenario: Prevent the creation of a new block with the same name as an existing published block
    Given there are blocks:
      | name            | type | version | stage     |
      | test-html-block | html | new     | published |
      And I am logged in as "author"
     When I create block:
      | name | test-html-block |
      | type | html            |
     Then I should see "A record with this value already exists"
