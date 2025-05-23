@checkout
Feature: Placing an order after moving back in checkout process
    In order to see all remaining checkout steps before confirmation
    As a Customer
    I want to be able to see all remaining steps

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for Free
        And the store allows paying Offline
        And I am a logged in customer

    @no-api @ui
    Scenario: Placing an order after moving back from the checkout summary to the addressing step but without any address modification
        Given I added product "PHP T-Shirt" to the cart
        And I was at the checkout summary step
        When I go back to addressing step of the checkout
        And I return to the checkout summary step
        And I confirm my order
        Then I should see the thank you page

    @no-api @ui
    Scenario: Placing an order after moving back from the checkout summary to the shipping method step but without any shipping method modification
        Given I added product "PHP T-Shirt" to the cart
        And I was at the checkout summary step
        When I go back to the shipping step
        And I return to the checkout summary step
        And I confirm my order
        Then I should see the thank you page

    @no-api @ui
    Scenario: Placing an order after moving back from the checkout summary to the payment method step but without any payment method modification
        Given I added product "PHP T-Shirt" to the cart
        And I was at the checkout summary step
        When I go back to payment step of the checkout
        And I return to the checkout summary step
        And I confirm my order
        Then I should see the thank you page
