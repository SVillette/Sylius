@applying_promotion_rules
Feature: Receiving discount based on nth order
    In order to pay less while placing an order
    As a Customer
    I want to receive a discount for my purchase

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And the store ships everywhere for Free
        And the store allows paying "Cash on Delivery"

    @api @ui
    Scenario: Receiving a discount on an first order
        Given there is a promotion "1st order promotion"
        And it gives "$20.00" off customer's 1st order
        And I am a logged in customer
        And I added product "PHP T-Shirt" to the cart
        When I check the details of my cart
        Then my cart total should be "$80.00"
        And my discount should be "-$20.00"

    @api @ui
    Scenario: Receiving no discount on an order if it is not first order placed
        Given there is a promotion "1st order promotion"
        And it gives "$20.00" off customer's 1st order
        And the customer "john.doe@example.com" has already placed an order "#001"
        And the customer bought a single "PHP T-Shirt"
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        And I added product "PHP T-Shirt" to the cart
        And I addressed the cart
        When I check the details of my cart
        Then my cart total should be "$100.00"
        And there should be no discount applied

    @api @ui
    Scenario: Receiving no discount on an order if I placed more than one order
        Given there is a promotion "2nd order promotion"
        And it gives "$10.00" off customer's 2nd order
        And I am a logged in customer
        And I added product "PHP T-Shirt" to the cart
        When I check the details of my cart
        Then my cart total should be "$100.00"
        And there should be no discount applied
