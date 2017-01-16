Feature: Whats on manager
In order to provide whats on information to visitors
As a whats on manager
I want to be able to create, edit and delete whats on events

  Background:
    Given a fresh install of the CMS
      And there are users:
      | username         | roles                          |
      | admin            | admin                          |
      | whats-on-manager | whats-on-manager,asset-manager |

  @javascript
  Scenario: Bulk import CSV list of whats on events
     When I am logged in as "whats-on-manager"
      And I import "whats_on.csv" via the whats on manager
     Then I should see on page 1 of whats on manager:
      | category   | activity    | dayTime      | venue                    | age      | description                                                                                                                                                                             | specificDate |
      | Job Advice | Skillsmatch | Monday - Day | Chrisp Street | 16yrs+   | Free advice on training, upgrading skills and job applications. Mondays 9.30am - 4pm                                                                                                    |              |
      | Over 50s   | Prime Time  | Monday - Day | Whitechapel   | Over 50s | Have an enjoyable and relaxing time, meet new and familiar friends, enjoy a cup of coffee and chat with staff about new books, CDs, DVDs. Fun for the over 50s. Mondays 10.15 - 11.45am |              |
      | Under 5s   | Story Time  | Monday - Day | All                      | 0 - 5yrs | Rhymes, stories and fun for Under 5s, plus time to choose your favourite books! Monday to Friday 10.30 - 11.15am                                                                        |              |
      And I should see on page 4 of whats on manager:
      | category              | activity             | dayTime       | venue          | age    | description                                                                                                 | specificDate |
      | Medicine for the Soul | Health Advice Stalls | Tuesday - Day | Bow | 16yrs+ | Advice on different topics from Health Trainers, Compass Wellbeing and Age UK. Tuesday 7 April, 11am - 12pm | 2017-04-07   |
      | Medicine for the Soul | Health Advice Stalls | Tuesday - Day | Bow | 16yrs+ | Advice on different topics from Health Trainers, Compass Wellbeing and Age UK. Tuesday 14 April, 11am - 2pm | 2017-04-14   |
