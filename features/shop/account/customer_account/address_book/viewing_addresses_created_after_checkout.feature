@address_book
Feature: Viewing addresses created after checkout
    In order to see all my addresses
    As a Customer
    I want to be able to browse my address book

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for Free
        And the store allows paying Offline
        And I am a logged in customer
        And I have an address "Lucifer Morningstar", "Seaside Fwy", "90802", "Los Angeles", "United States", "Arkansas" in my address book
        And my default address is of "Lucifer Morningstar"

    @api @ui
    Scenario: Viewing address created after placing an order
        Given I added product "PHP T-Shirt" to the cart
        And I addressed the cart with "John Doe" as the billing address
        And I chose "Free" shipping method and "Offline" payment method
        When I confirm my order
        And I browse my address book
        Then I should have 2 addresses in my address book
