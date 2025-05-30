@checkout
Feature: Seeing a order item discount
    In order to be aware of discounts applied to an order
    As a Customer
    I want to see discounts of specific order item

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Lannister Coat" priced at "$100.00"
        And there is a promotion "Christmas sale"
        And this promotion gives "10%" off on every product with minimum price at "$50.00"
        And the store ships everywhere for Free
        And the store allows paying Offline
        And I am a logged in customer

    @api @ui
    Scenario: Seeing a discounted price on order item
        Given I added product "Lannister Coat" to the cart
        And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        When I proceed with "Free" shipping method and "Offline" payment
        Then I should be on the checkout summary step
        And the "Lannister Coat" product should have unit price discounted by "$10.00"
        And my order total should be "$90.00"
