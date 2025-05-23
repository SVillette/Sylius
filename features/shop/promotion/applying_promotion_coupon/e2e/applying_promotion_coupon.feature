@applying_promotion_coupon
Feature: Applying promotion coupon
    In order to pay proper amount after using the promotion coupon
    As a Customer
    I want to have promotion coupon's discounts applied to my cart

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And the store has promotion "Christmas sale" with coupon "SANTA2016"
        And this promotion gives "$10.00" discount to every order
        And I am a logged in customer

    @api @ui @javascript
    Scenario: Receiving fixed discount for my cart
        Given I added product "PHP T-Shirt" to the cart
        When I check the details of my cart
        And I use coupon with code "SANTA2016"
        Then my cart total should be "$90.00"
        And my discount should be "-$10.00"

    @api @ui @javascript
    Scenario: Receiving no discount if promotion for the applied coupon is invalid
        Given I added product "PHP T-Shirt" to the cart
        When I check the details of my cart
        And I use coupon with code "INVALID"
        Then I should be notified that the coupon is invalid
        And my cart total should be "$100.00"
        And there should be no discount applied
