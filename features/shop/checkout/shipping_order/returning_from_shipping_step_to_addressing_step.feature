@checkout
Feature: Returning from shipping step to addressing step
    In order to readdress already addressed order
    As a Customer
    I want to be able to go back to addressing step from shipping step

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Apollo 11 T-Shirt" priced at "$49.99"
        And the store ships everywhere for Free
        And I am a logged in customer

    @no-api @ui
    Scenario: Going back to addressing step with "Change Address" button
        Given I added product "Apollo 11 T-Shirt" to the cart
        And I addressed the cart
        When I go to the shipping step
        And I decide to change my address
        Then I should be redirected to the addressing step
        And I should be able to go to the shipping step again

    @no-api @ui
    Scenario: Going back to the addressing step with steps panel
        Given I added product "Apollo 11 T-Shirt" to the cart
        And I addressed the cart
        When I go to the shipping step
        And I navigate directly to the addressing step in the checkout steps panel
        Then I should be redirected to the addressing step
        And I should be able to go to the shipping step again
