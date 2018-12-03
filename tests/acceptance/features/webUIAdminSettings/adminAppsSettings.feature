@webUI
Feature: admin apps settings
	As an admin
	I want to be able to manage apps settings on the ownCloud server
	So that I can enable or disable apps on the ownCloud server

	Background:
		Given the administrator has browsed to the admin apps settings page

	@smokeTest
	Scenario: admin disables an app
		Given the app "comments" has been enabled
		When the administrator disables the app "comments" using the webUI
		Then app "comments" should be disabled

	@smokeTest
	Scenario: admin enables an app
		Given the app "comments" has been disabled
		And the administrator has browsed to the disabled apps page
		When the administrator enables the app "comments" using the webUI
		Then app "comments" should be enabled