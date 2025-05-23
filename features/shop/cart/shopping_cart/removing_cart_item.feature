@shopping_cart
Feature: Removing cart item from cart
    In order to delete some unnecessary cart items
    As a Visitor
    I want to be able to remove cart item

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "T-Shirt banana" priced at "$12.54"

    @api @ui
    Scenario: Removing cart item when the store has defined default shipping method
        Given the store has "UPS" shipping method with "$20.00" fee
        And I added product "T-Shirt banana" to the cart
        And I removed product "T-Shirt banana" from the cart
        When I see the summary of my cart
        Then my cart should be empty
        And my cart's total should be "$0.00"

    @api @ui
    Scenario: Checking cart's total after removing one item
        Given the store has a product "T-Shirt strawberry" priced at "$17.22"
        And I added product "T-Shirt banana" to the cart
        And I added product "T-Shirt strawberry" to the cart
        And I removed product "T-Shirt banana" from the cart
        When I see the summary of my cart
        Then my cart's total should be "$17.22"

    @api @ui
    Scenario: Removing cart item which causes order shipping method recalculation
        Given the store has "Paid" shipping category
        And the store has "Free" shipping category
        And product "T-Shirt banana" belongs to "Paid" shipping category
        And the store has a product "T-Shirt small" priced at "$15.00"
        And product "T-Shirt small" belongs to "Free" shipping category
        And the store has "FedEx" shipping method with "$30.00" fee
        And this shipping method requires at least one unit matches to "Paid" shipping category
        And the store has "UPS" shipping method with "$0.00" fee
        And this shipping method requires that all units match to "Free" shipping category
        And I added product "T-Shirt banana" to the cart
        And I added product "T-Shirt small" to the cart
        And I removed product "T-Shirt banana" from the cart
        When I see the summary of my cart
        Then my cart shipping total should be "$0.00"
