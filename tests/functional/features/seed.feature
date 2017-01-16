Feature: Seed
In order to seed
As a seeder
I want to be able to seed

  Scenario: Seed
    Given a fresh install of the CMS
      And there are users:
      | username  | roles     |
      | admin     | admin     |
      | author    | author    |
      | publisher | publisher |
      | digital-gallery-manager | digital-gallery-manager,asset-manager |
      | whats-on-manager | whats-on-manager,asset-manager |
      | course-manager | course-manager,asset-manager |
     And there are items:
      | name | type    | version | stage     |
      | home | article | new     | published |
      | test-article-item | article | new     | published |
      | new-in-authoring     | article | new     | authoring  |
      | update-in-authoring  | article | update  | authoring  |
      | new-in-publishing    | article | new     | publishing |
      | update-in-publishing | article | update  | publishing |
      And there are blocks:
      | name                  | type | version | stage     |
      | test-html-block-one   | html | new     | published |
      | test-html-block-two   | html | new     | published |
      | test-html-block-three | html | new     | published |
      | test-html-block-four  | html | new     | published |
     And there is an existing "whats_on.csv" import for whats on module
     And there is an existing "courses.csv" import for course module
     And there is an existing "digital_gallery.csv" and "digital_gallery.zip" import for digital gallery module
