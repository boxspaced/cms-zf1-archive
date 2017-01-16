Feature: Course manager
In order to provide course information to visitors
As a course manager
I want to be able to create, edit and delete courses

  Background:
    Given a fresh install of the CMS
      And there are users:
      | username       | roles                        |
      | admin          | admin                        |
      | course-manager | course-manager,asset-manager |

  @javascript
  Scenario: Bulk import CSV list of courses
     When I am logged in as "course-manager"
      And I import "courses.csv" via the course manager
     Then I should see on page 1 of course manager:
      | category | title                                       | code  | day      | startDate  | time     | numWeeks | hoursPerWeek | venue           | fee   | concession | dayTime      | description                                                                                                                                                            |
      | Cookery  | Vegetarian Cookery                          | C1638 | Thursday | 2017-04-23 | 18:00 | 10       | 2.5         | Shadwell Centre | 81.99 | 56.00      |              | The course will give students a brief guide on vegetarian recipes using a wide selection of vegetables and meat free products covering flavours and cooking techniques |
      | Cookery  | Baking Bread Cakes and Pastries - Improvers | C4687 | Tuesday  | 2017-04-21 | 10:00 | 10       | 3         | Shadwell Centre | 81.00 | 56.90      |              |                                                                                                                                                                        |
      | Cookery  | Thai Cookery - All Levels                   | C4668 | Tuesday  | 2017-04-21 | 18:00 | 10       | 2.5         | Shadwell Centre | 81.00 | 56.00      | Evening only |                                                                                                                                                                        |
