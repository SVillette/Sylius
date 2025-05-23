@applying_taxes
Feature: Apply correct taxes for an order with a discount for an item in it
    In order to pay proper amount when buying goods
    As a Customer
    I want to have correct taxes applied to my order when it has a discount

    Background:
        Given the store operates on a single channel in "United States"
        And default tax zone is "US"
        And the store has "US VAT" tax rate of 23% for "Clothes" within the "US" zone
        And the store has "Low VAT" tax rate of 10% for "Mugs" within the "US" zone
        And the store has a product "PHP T-Shirt" priced at "$10.00"
        And it belongs to "Clothes" tax category
        And the store has a product "Symfony Mug" priced at "$56.05"
        And it belongs to "Mugs" tax category
        And the store has a product "PHP Mug" priced at "$56.04"
        And it belongs to "Mugs" tax category
        And there is a promotion "Holiday promotion"
        And the promotion gives "$10.10" off on a "Symfony Mug" product
        And there is a promotion "PHP promotion"
        And the promotion gives "$10.10" off on a "PHP Mug" product
        And I am a logged in customer

    @api @ui
    Scenario: Properly rounded up tax for single product
        Given I added product "Symfony Mug" to the cart
        When I check the details of my cart
        Then my cart total should be "$50.55"
        And my cart taxes should be "$4.60"

    @api @ui
    Scenario: Properly rounded down tax for single product
        Given I added product "PHP Mug" to the cart
        When I check the details of my cart
        Then my cart total should be "$50.53"
        And my cart taxes should be "$4.59"

    @api @ui
    Scenario: Properly rounded taxes for order with multiple products without discount
        Given I added 2 products "PHP T-Shirt" to the cart
        And I added product "Symfony Mug" to the cart
        When I check the details of my cart
        Then my cart total should be "$75.15"
        And my cart taxes should be "$9.20"

    @api @ui
    Scenario: Properly rounded taxes for order with multiple products with discount
        Given I added 2 products "PHP T-Shirt" to the cart
        And I added 2 products "Symfony Mug" to the cart
        When I check the details of my cart
        Then my cart total should be "$125.69"
        And my cart taxes should be "$13.79"
