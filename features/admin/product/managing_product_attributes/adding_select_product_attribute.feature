@managing_product_attributes
Feature: Adding a new select product attribute
    In order to show specific product's parameters to customer
    As an Administrator
    I want to add a new select product attribute

    Background:
        Given the store is available in "English (United States)"
        And I am logged in as an administrator

    @api @ui @mink:chromedriver
    Scenario: Adding a new select product attribute
        When I want to create a new select product attribute
        And I specify its code as "mug_material"
        And I name it "Mug material" in "English (United States)"
        And I add value "Banana Skin" in "English (United States)"
        And I add it
        Then I should be notified that it has been successfully created
        And the select attribute "Mug material" should appear in the store

    @api @ui @mink:chromedriver
    Scenario: Adding multiple select product attribute
        When I want to create a new select product attribute
        And I specify its code as "mug_material"
        And I name it "Mug material" in "English (United States)"
        And I add value "Banana Skin" in "English (United States)"
        And I also add value "Plastic" in "English (United States)"
        And I check multiple option
        And I add it
        Then I should be notified that it has been successfully created
        And the select attribute "Mug material" should appear in the store
        And the "Mug material" product attribute should have value "Banana Skin"
        And the "Mug material" product attribute should also have value "Plastic"

    @no-api @ui
    Scenario: Seeing disabled type field while adding a select product attribute
        When I want to create a new select product attribute
        Then the type field should be disabled
