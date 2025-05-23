@applying_taxes
Feature: Applying correct taxes for item units with a discount applied for all items in it when tax rates are included in price
    In order to pay proper amount when buying goods
    As a Visitor
    I want to have correct taxes applied when the order is discounted and tax rates are included in products prices

    Background:
        Given the store operates on a single channel in "United States"
        And default tax zone is "US"
        And the store uses the "Order item units based" tax calculation strategy
        And the store has included in price "US VAT" tax rate of 23% for "Clothes" within the "US" zone
        And the store has included in price "Low VAT" tax rate of 10% for "Mugs" within the "US" zone
        And the store has a product "PHP T-Shirt" priced at "$10.00"
        And it belongs to "Clothes" tax category
        And the store has a product "Symfony Mug" priced at "$56.95"
        And it belongs to "Mugs" tax category
        And the store has a product "PHP Mug" priced at "$56.90"
        And it belongs to "Mugs" tax category
        And there is a promotion "Holiday promotion"
        And the promotion gives "$10.00" off if order contains a "Symfony Mug" product
        And there is a promotion "PHP promotion"
        And the promotion gives "$10.00" off if order contains a "PHP Mug" product

    @api @ui
    Scenario: Applying correct taxes for a single item unit with order discount
        Given I added product "Symfony Mug" to the cart
        When I check the details of my cart
        Then my cart total should be "$46.95"
        And my included in price taxes should be "$4.27"

    @api @ui
    Scenario: Applying correct taxes for a single item unit with order discount
        Given I added product "PHP Mug" to the cart
        When I check the details of my cart
        Then my cart total should be "$46.90"
        And my included in price taxes should be "$4.26"

    @api @ui
    Scenario: Applying correct taxes for multiple units of different products with order discount and default calculator
        Given I added 2 products "PHP Mug" to the cart
        And I added product "Symfony Mug" to the cart
        When I check the details of my cart
        Then my cart total should be "$150.75"
        And my included in price taxes should be "$13.71"

    @api @ui
    Scenario: Applying correct taxes for multiple units of different products with order discount and decimal calculator
        Given the "US VAT" tax rate has decimal calculator configured
        And the "Low VAT" tax rate has decimal calculator configured
        And I added 2 products "PHP Mug" to the cart
        And I added product "Symfony Mug" to the cart
        When I check the details of my cart
        Then my cart total should be "$150.75"
        And my included in price taxes should be "$13.70"
