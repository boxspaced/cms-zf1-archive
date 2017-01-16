Feature: Asset manager
In order to provide useful content to visitors
As a asset manger
I want to be able to upload, rename and delete images, documents and media

  Background:
    Given a fresh install of the CMS
      And there are users:
      | username      | roles         |
      | admin         | admin         |
      | asset-manager | asset-manager |

  @javascript
  Scenario: Upload new image
     When I am logged in as "asset-manager"
      And I upload "small-image.jpg" to images via asset manager
     Then I should see "small-image.jpg" in the asset manager images

  @javascript
  Scenario: Upload new document
     When I am logged in as "asset-manager"
      And I upload "courses.csv" to documents via asset manager
     Then I should see "courses.csv" in the asset manager documents

  @javascript
  Scenario: Upload new media
     When I am logged in as "asset-manager"
      And I upload "test.mp3" to media via asset manager
     Then I should see "test.mp3" in the asset manager media
  