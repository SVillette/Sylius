@shopping_cart
Feature: Viewing a cart summary with correct default shipping method based on variant applied
    In order to see details about my order
    As a Visitor
    I want to be able to see my cart summary with the correct shipping method based on variants

    Background:
        Given the store operates on a single channel in "United States"
        And the store allows shipping with "Method 1" at position 2 with "$5.00" fee
        And the store also allows shipping with "Method 2" at position 0 with "$6.00" fee
        And the store has a product "T-Shirt banana" priced at "$10"

    @ui
    Scenario:
        Given I added product "T-Shirt banana" to the cart
        When I see the summary of my cart
        Then my cart shipping total should be "$6.00"
