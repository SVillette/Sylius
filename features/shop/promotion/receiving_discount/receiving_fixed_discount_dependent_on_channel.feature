@receiving_discount
Feature: Receiving fixed discount dependent on channel on cart
    In order to pay proper amount while buying promoted goods in different channels
    As a Customer
    I want to have promotions applied to my cart

    Background:
        Given the store operates on a channel named "Web-US" in "USD" currency and with hostname "united.states"
        And the store operates on another channel named "Web-GB" in "GBP" currency and with hostname "great.britain"
        And the store has a product "PHP T-Shirt" priced at "$100.00" in "Web-US" channel
        And this product is also priced at "£80.00" in "Web-GB" channel
        And there is a promotion "Holiday promotion"
        And this promotion gives "$10.00" discount to every order in the "Web-US" channel and "£12.00" discount to every order in the "Web-GB" channel
        And I am a logged in customer

    @api @ui
    Scenario: Receiving fixed discount in proper currency for channel
        Given I changed my current channel to "Web-US"
        And I added product "PHP T-Shirt" to the cart
        When I check the details of my cart
        Then my cart total should be "$90.00"
        And my discount should be "-$10.00"

    @api @ui
    Scenario: Receiving fixed discount in proper currency after channel change
        Given I changed my current channel to "Web-GB"
        And I added product "PHP T-Shirt" to the cart
        When I check the details of my cart
        Then my cart total should be "£68.00"
        And my discount should be "-£12.00"
