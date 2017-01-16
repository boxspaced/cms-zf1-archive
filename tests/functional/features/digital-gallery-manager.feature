Feature: Digital gallery manager
In order to provide digital images to visitors
As a digital gallery manager
I want to be able to create, edit and delete digital images

  Background:
    Given a fresh install of the CMS
      And there are users:
      | username                | roles                                 |
      | admin                   | admin                                 |
      | digital-gallery-manager | digital-gallery-manager,asset-manager |
      And there are digital gallery filters:
      | id | type     | text                                |
      | 46 | decade   | 1950s                               |
      | 49 | decade   | 1980s                               |
      | 65 | location | Poplar                              |
      | 76 | location | Island History Trust (Isle of Dogs) |
      | 10 | subject  | Pubs                                |
      | 12 | subject  | Street scenes                       |

  Scenario: Prevent invalid filter input
     When I am logged in as "digital-gallery-manager"
      And I create a new filter via the digital gallery manager:
      | type | text |
      |      |      |
     Then I should see "Validation failed"

  Scenario: Create new filter
     When I am logged in as "digital-gallery-manager"
      And I create a new filter via the digital gallery manager:
      | type   | text  |
      | decade | 1900s |
     Then I should see in digital gallery filters:
      | type   | text  |
      | decade | 1900s |

  Scenario: Edit a filter
     When I am logged in as "digital-gallery-manager"
      And I edit the filter with text "1950s" via the digital gallery manager:
      | type   | text  |
      | decade | 1900s |
     Then I should see in digital gallery filters:
      | type   | text  |
      | decade | 1900s |

  Scenario: Delete a filter
     When I am logged in as "digital-gallery-manager"
      And I delete the filter with text "1950s" via the digital gallery manager
     Then I should not see in digital gallery filters:
      | type   | text  |
      | decade | 1950s |

  @javascript
  Scenario: Upload an image
     When I am logged in as "digital-gallery-manager"
      And I upload a new image via the digital gallery manager:
      | title      | keywords              | description                    | imageNo | copyright | price | image           | decades | locations                                  | subjects |
      | The Cedars | architecture,building | Picture of The Cedars building | 01289   | ACME Inc  | 9.99  | small-image.jpg | 1950s   | Poplar,Island History Trust (Isle of Dogs) | Pubs     |
     Then I should see in digital gallery manager:
      | title      |
      | The Cedars |
      And I should see digital image "01289" with detail:
      | title      | keywords              | description                    | imageNo | copyright | image |
      | The Cedars | architecture,building | Picture of The Cedars building | 01289   | ACME Inc  | .jpg  |

  @javascript
  Scenario: Upload a duplicate image
     When I am logged in as "digital-gallery-manager"
      And I upload a new image via the digital gallery manager:
      | title      | keywords              | description                    | imageNo | copyright | price | image           | decades | locations                                  | subjects |
      | The Cedars | architecture,building | Picture of The Cedars building | 01289   | ACME Inc  | 9.99  | small-image.jpg | 1950s   | Poplar,Island History Trust (Isle of Dogs) | Pubs     |
      And I upload a new image via the digital gallery manager:
      | title    | keywords              | description                  | imageNo | copyright | price | image           | decades | locations                                  | subjects |
      | The Oaks | architecture,building | Picture of The Oaks building | 01290   | ACME Inc  | 9.99  | small-image.jpg | 1950s   | Poplar,Island History Trust (Isle of Dogs) | Pubs     |
     Then I should see in digital gallery manager:
      | title      |
      | The Cedars |
      | The Oaks   |
      And I should see digital image "01289" with detail:
      | title      | keywords              | description                    | imageNo | copyright | image |
      | The Cedars | architecture,building | Picture of The Cedars building | 01289   | ACME Inc  | .jpg  |
      And I should see digital image "01290" with detail:
      | title    | keywords              | description                  | imageNo | copyright | image |
      | The Oaks | architecture,building | Picture of The Oaks building | 01290   | ACME Inc  | .jpg  |

  @javascript
  Scenario: Prevent upload of excessively large image
     When I am logged in as "digital-gallery-manager"
      And I upload a new image via the digital gallery manager:
      | title      | keywords              | description                    | imageNo | copyright | price | image                | decades | locations                                  | subjects |
      | The Cedars | architecture,building | Picture of The Cedars building | 01289   | ACME Inc  | 9.99  | very-large-image.jpg | 1950s   | Poplar,Island History Trust (Isle of Dogs) | Pubs     |
     Then I should see "Validation failed"
      And I should see "Maximum allowed size for file 'very-large-image.jpg' is '6MB'"

  Scenario: Edit an image
    Given there are digital gallery images:
      | title      | keywords              | description                  | imageNo | copyright | price | image           | decades | locations                                  | subjects |
      | The Cedars | architecture,building | Picture of The Oaks building | 01289   | ACME Inc  | 9.99  | small-image.jpg | 1950s   | Poplar,Island History Trust (Isle of Dogs) | Pubs     |
     When I am logged in as "digital-gallery-manager"
      And I edit the image titled "The Cedars" via the digital gallery manager:
      | title    | keywords              | description                  | imageNo | copyright | price | decades | locations                                  | subjects |
      | The Oaks | architecture,building | Picture of The Oaks building | 01289   | ACME Inc  | 9.99  | 1950s   | Poplar,Island History Trust (Isle of Dogs) | Pubs     |
     Then I should see in digital gallery manager:
      | title    |
      | The Oaks |
      And I should see digital image "01289" with detail:
      | title    | keywords              | description                  | imageNo | copyright | image |
      | The Oaks | architecture,building | Picture of The Oaks building | 01289   | ACME Inc  | .jpg  |

  Scenario: Delete an image
    Given there are digital gallery images:
      | title      | keywords              | description                    | imageNo | copyright | price | image           | decades | locations                                  | subjects |
      | The Cedars | architecture,building | Picture of The Cedars building | 01289   | ACME Inc  | 9.99  | small-image.jpg | 1950s   | Poplar,Island History Trust (Isle of Dogs) | Pubs     |
     When I am logged in as "digital-gallery-manager"
      And I delete the image titled "The Cedars" via the digital gallery manager
     Then I should not see in digital gallery manager:
      | title      |
      | The Cedars |

  @javascript
  Scenario: Bulk import large amount of images
     When I am logged in as "digital-gallery-manager"
      And I import "digital_gallery_large.csv" and "digital_gallery_large.zip" via the digital gallery manager
     Then I should see "Import completed, number of images processed: 837"
      And I should see "WARNING: Image was skipped because it errored: P01278 Bromley High Street looking East, 1991.jpg"
      But I should not see "WARNING: Image was skipped because it was not found in the zip file"
      And I should not see "WARNING: Image was skipped because it was too large"

  @javascript
  Scenario: Prevent bulk import of image that is too large
     When I am logged in as "digital-gallery-manager"
      And I import "digital_gallery.csv" and "digital_gallery.zip" via the digital gallery manager
     Then I should see "Import completed, number of images processed: 25"
      And I should see "WARNING: Image was skipped because it was too large: very-large-image.jpg"

  @javascript
  Scenario: Warn when image in bulk import CSV is not found in zip file
     When I am logged in as "digital-gallery-manager"
      And I import "digital_gallery.csv" and "digital_gallery.zip" via the digital gallery manager
     Then I should see "Import completed, number of images processed: 25"
      And I should see "WARNING: Image was skipped because it was not found in the zip file: missing-image.jpg"

  @javascript
  Scenario: Bulk import empty CSV
     When I am logged in as "digital-gallery-manager"
      And I import "digital_gallery_empty.csv" and "digital_gallery.zip" via the digital gallery manager
     Then I should see "Import completed, number of images processed: 0"
      And I should not see "WARNING:"

  @javascript
  Scenario: Bulk import empty zip
     When I am logged in as "digital-gallery-manager"
      And I import "digital_gallery.csv" and "digital_gallery_empty.zip" via the digital gallery manager
     Then I should see "Import completed, number of images processed: 0"
      And I should see "WARNING: Image was skipped because it was not found in the zip file"
