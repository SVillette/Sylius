@receiving_discount
Feature: Receiving percentage discount on shipping
    In order to pay decreased amount for shipping
    As a Customer
    I want to have shipping promotion applied to my cart

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "DHL" shipping method with "$10.00" fee
        And the store has a product "PHP Mug" priced at "$20.00"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And there is a promotion "Holiday promotion"
        And I am a logged in customer

    @api @ui
    Scenario: Receiving percentage discount on shipping
        Given the promotion gives "20%" discount on shipping to every order
        And I added product "PHP T-Shirt" to the cart
        And I proceed with selecting "DHL" shipping method
        When I check the details of my cart
        Then my cart total should be "$108.00"
        And my cart shipping total should be "$8.00"

    @api @ui
    Scenario: Receiving free shipping
        Given the promotion gives free shipping to every order
        And I added product "PHP T-Shirt" to the cart
        And I proceed with selecting "DHL" shipping method
        When I check the details of my cart
        Then my cart total should be "$100.00"
        And my cart shipping total should be "$0.00"

    @api @ui @javascript
    Scenario: Receiving free shipping after changing the quantity of a product in the cart
        Given the promotion gives free shipping to every order over "$70.00"
        And I added product "PHP Mug" to the cart
        When I change product "PHP Mug" quantity to 4 in my cart
        Then my cart total should be "$80.00"
        And my cart shipping total should be "$0.00"

    @api @ui @mink:chromedriver
    Scenario: Not receiving free shipping after changing the quantity of a product in the cart
        Given the promotion gives free shipping to every order over "$70.00"
        And I added product "PHP Mug" to the cart
        When I change product "PHP Mug" quantity to 4 in my cart
        And I change product "PHP Mug" quantity to 2 in my cart
        Then my cart total should be "$50.00"
        And my cart shipping total should be "$10.00"

    @api @ui
    Scenario: Not receiving negative discount on shipping
        Given the promotion gives free shipping to every order over "$70.00"
        And there is a promotion "Shipping promotion"
        And this promotion gives free shipping to every order over "$50.00"
        And I added 4 products "PHP Mug" to the cart
        When I check the details of my cart
        Then my cart total should be "$80.00"
        And my cart shipping total should be "$0.00"

    @api @ui @javascript
    Scenario: Still receiving free shipping after removing the product from the cart
        Given the promotion gives free shipping to every order over "$70.00"
        And I added product "PHP Mug" to the cart
        And I added product "PHP T-Shirt" to the cart
        When I remove product "PHP Mug" from the cart
        Then my cart total should be "$100.00"
        And my cart shipping total should be "$0.00"

    @api @ui @javascript
    Scenario: Not receiving free shipping after removing the product from the cart
        Given the promotion gives free shipping to every order over "$70.00"
        And I added product "PHP Mug" to the cart
        And I added product "PHP T-Shirt" to the cart
        When I remove product "PHP T-Shirt" from the cart
        Then my cart total should be "$30.00"
        And my cart shipping total should be "$10.00"
