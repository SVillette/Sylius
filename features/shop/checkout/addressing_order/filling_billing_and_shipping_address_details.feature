@checkout
Feature: Addressing an order
    In order to address an order
    As a Customer
    I want to be able to fill addressing details

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for Free
        And I am a logged in customer

    @api @ui
    Scenario: Address an order without different shipping address
        Given I added product "PHP T-Shirt" to the cart
        And I am at the checkout addressing step
        When I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        Then I should be on the checkout shipping step

    @api @ui @mink:chromedriver
    Scenario: Address an order with different shipping address
        Given I added product "PHP T-Shirt" to the cart
        And I am at the checkout addressing step
        When I specify the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Eddard Stark"
        And I specify the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I complete the addressing step
        Then I should be on the checkout shipping step
