# Civist - Petitions and Fundraising
Contributors: civist
Tags: petition, fundraising, donation, activism
Requires at least: 4.4
Tested up to: 6.6
Requires PHP: 5.6
Stable tag: trunk
License: MIT
License URI: https://opensource.org/licenses/MIT

With Civist you create petitions directly in WordPress, raise funds and build strong supporter networks.

## Description

Create and configure your petition directly in your own WordPress. Publish them in a page or post and you are ready to go! Your supporters sign the petition right there, embedded in your website.

Just add a donation form to your petitions to collect SEPA and credit card donations in compliance with EU legislation.

With Civist, you manage your supporters directly in WordPress. Contacts are automatically saved in your mailing list manager. Simple!

Export your contacts and signatures as CSV-files for further processing.

## Installation

The easiest way of installing the Civist WordPress plugin is to use the built in plugin search of WordPress. Just search for Civist and click the "Install Now" button.

Downloading the plugin and installing it from the ZIP-file is another option. You can always find the current version at [WordPress Plugin Directory](https://wordpress.org/plugins/civist/). To install it:

* Download the [ZIP file](https://downloads.wordpress.org/plugin/civist.zip)
* Go to the "Plugins" menu in the WordPress dashboard
* Upload the civist.zip file
* Activate the plugin

## Changelog

### Release 7.7.0 – 2024-10-22

#### Feature Additions

* Spanish translations got an update
* New translations for all available languages added
* The Stripe payment integration got an update

#### Bug Fixes

* Fixed: Payment methods P24, Multibanco, Klarna/Sofort

### Release 7.6.6 – 2024-07-15 - Update recommended

#### Feature Additions

* The plugin is ready for WordPress 6.6

#### Bug Fixes

* Fixed: The Civist Gutenberg blocks did not work in WordPress 6.6

### Release 7.6.5 - 2024-03-05

#### Feature Additions

* The plugin is ready for WordPress 6.4
* Petitions and donation forms are now available in Turkish / Türkçe (TR)
* Verification emails are now available in Turkish / Türkçe (TR)

### Release 7.6.1 – 2023-08-24

#### Feature Additions

* The plugin is ready for WordPress 6.3
* Petitions and donation forms are now available in Svenska (SV)
* Verification emails are now available in Svenska (SV)

### Release 7.6.0 – 2023-03-27

#### Feature Additions

* The plugin is ready for WordPress 6.2
* Four new currencies are now available in the currency selection of the donation form: Czech Koruna (CZK), Hungarian Forint (HUF), Romanian Leu (RON), South African Rand (ZAR)

#### Bug Fixes

* Fixed: The link to the help page on the Civist dashboard led to a 404 page when users used Civist with the German language set in WordPress.
* Fixed: Under certain circumstances, the support widget built into the Civist plugin would not load and display at the bottom right of the screen.


### Release 7.5.4 - 2022-09-06

#### Feature Additions

* Some translations got an update

#### Bug Fixes

* A bug was fixed that prevented the country field of the donation form from opening when using the Safari browser on macOS or iOS.

### Release 7.5.0 – 2022-05-24

#### Feature Additions

* The plugin is ready for WordPress 6.0
* Petitions and donation forms are now available in Lithuanian (LT)
* Verification emails are now available in Lithuanian (LT)

### Release 7.4.0

#### Feature Additions

* The plugin is ready for WordPress 5.9
* Updated Civist dashboard

#### Privacy

* Updated list of sub-processors in the Privacy Policy of the plugin

### Release 7.3.12

#### Feature Additions

* The plugin is ready for WordPress 5.8
* Technical improvements to increase the plugin's performance
* Verification emails are now available in French and Polish
* Some translations got an update and new ones were added
* UX improvements

#### Bug Fixes

* A bug was fixed that caused incorrect values of linked petitions to be displayed in the list of donation forms
* A link on the Civist dashboard was added ("Rate this plugin")
* A link on the Civist dashboard was fixed ("Learn more about online activism with Civist")

### Release 7.3.0 - 2020-11-03

#### Feature Additions

* Fundraising registration now has distinct choices for "Company" and "Nonprofit" instead of the previously used generic "Organisation", which reduces the likelihood of the identity verification performed by Stripe to fail. Nonprofit organisations who already registered for fundraising with the "Organisation" setting should reach out to our support.
* Confirmation emails for petitions are now available in french.

#### Bug Fixes

* A layout problem on the Civist dashboard in the WordPress admin on WordPress 5.5 was fixed.

### Release 7.2.0 - 2020-06-24

#### Feature Additions

* [Closed Beta] Petitions can now be configured to have an initials field.
* Some missing translations were added.

#### Bug Fixes

* A bug was fixed that caused an error while signing a petition due to a technical incompatibility with the plugin "Instant Articles for WP".

### Release 7.1.1 - 2020-04-07

#### Feature Additions

* Petitions and donation forms are now available in Croatian (HR).

### Release 7.1.0 – 2020-03-27

#### Feature Additions

* The fundraising setup and account settings are now using pages provided by Stripe for collecting personal and business information

#### Removed Features

* Fundraising is no longer available for users based in Malaysia (MY).

### Release 7.0.5 - 2020-03-17

#### Bug Fixes

* A bug was fixed that caused radio buttons' outline to appear cropped.

### Release 7.0.2 - 2020-01-10

#### Bug Fixes

* A bug was fixed that caused fundraising registration to fail for some countries.

### Release 7.0.0 - 2019-09-11 - Update highly recommended

#### Feature Additions

* The fundraising feature is now compliant with the new EU regulations for financial transactions (PSD2/SCA).
* The fundraising feature now supports additional countries:
  * Estonia (EE)
  * Greece (GR)
  * Latvia (LV)
  * Lithuania (LT)
  * Malaysia (MY)
  * Poland (PL)
  * Slovakia (SK)
  * Slovenia (SI)
* All translations got an update and some new were added.

#### Removed Features

* Customers in the Czech Republic cannot register for fundraising anymore.

#### Bug Fixes

* A bug was fixed that caused the cursor to jump to the first field of the next available payment method in a donation form while pressing the tabulator key in the last field of the actual used payment method.
* A bug was fixed that caused notifications in the petition and donation forms to not be fully visible.
* A bug was fixed that made it impossible to set the start or end time of a petition.

### Release 6.9.0 - 2019-07-25

#### Feature Additions

* Preparing the plugin for upcoming changes in EU regulations for financial transactions (PSD2/SCA).
* Petitions and donation forms are now available in Romanian (RO).
* Spanish translations got an update and some new were added.

#### Bug Fixes

* A bug was fixed that caused an error while signing a petition in very rare cases.
* Minor fixes for visual problems of the donation form.
* The donation form shows the amount correctly in a donation form.
* Using the arrow keys in edge to change the amount no longer causes the value changes to be lost when leaving the field.

### Release 6.8.0 - 2019-05-22

#### Feature Additions

* Mailchimp is now available as a mailing list provider
* The Civist Plugin is now available in Spanish (ES)

### Release 6.7.0 - 2019-04-25

#### Feature Additions

* EU and US customers can now collect donations from supporters in Austria, Belgium, Germany, Italy, Netherlands and Spain with the new payment service KLARNA Pay now (SOFORT).
* The donation table in the Civist supporter overview now shows the address of the donor for all payment methods that ask for it.
* A new Civist block for the Gutenberg editor was released. This block makes available four different styling options for your petition forms and new alignment possibilities for all forms.

#### Bug Fixes

* A bug was fixed that made it impossible to sign the petition form without reloading it in some rare cases.

### Release 6.6.0 - 2019-04-15

#### Feature Additions

* EU and US customers can now collect donations from their supporters with P24, a payment service from Poland.
* Petitions and donation forms are now available in English (GB)

#### Bug Fixes

* The shortcode creation after updating a donation form would show a blank screen in some cases
* Navigating away from the donation form edit page after updating it would show a blank screen in some cases

### Release 6.5.0 - 2019-04-10

#### Feature Additions

* You can now collect donations from your supporters with Multibanco, a payment service from Portugal.

### Release 6.4.2 - 2019-04-04

#### Feature Additions

* Improved synchronisation with Mailjet mailing lists
* [Closed Beta] Improved handling of contact fields for mailing list management with CleverReach

### Release 6.4.1 - 2019-03-20

#### Feature Additions

* Petitions and donation forms are now available in Norsk Bokmål (NB).

### Release 6.4.0 - 2019-03-11 - Update recommended

#### Feature Additions

* [Closed Beta] The Civist Widget got a new "Kiosk Mode" that makes it possible to use the petition form for collecting signatures in public places.

#### Bug Fixes

* The petition form shows the error message for invalid email addresses again.
* The value of the donation amount is set to zero if the field is cleared.
* The signature table in the supporter overview shows 25 signatures per page again, instead of one.

#### Bug Fixes

* The donation form shortcode creation after updating the donation form would show a blank screen in some cases
* Navigating away from the donation form edit page would show a blank screen in some cases

### v6.3.0

* The plugin is ready for WordPress 5.1
* Fundraising is now available in 22 countries, including the US, CA, AU, NZ, CH, HK, SG
* Donation forms got shorter and have a drastically improved user experience
* Better localisations and translations for all supported countries

### v6.2.3

* Petitions and donation forms are now available in Czech

### v6.2.0

* Supporters overview tables UI Improvements for a better experience on smaller screens
* Petitions can now be configured to hide the "Sign this petition. Become a supporter." text
* Petition's organisation field is now optional and placed after the name fields
* Translations and wording improvements
* Fixed an issue where changing payment methods for a donation form would make the preview stop working
* Fixed an issue where Chrome autocomplete would overlay the country and nationality fields

### v6.1.0

* Petitions can now be configured to have an organisation name field
* Petitions can now be configured to have a phone number field
* The petition's comment field is now available to everyone
* Some UI issues were fixed in the donations table
* In the supporter's detailed view, the petition's link was fixed
* The petition's progress bar now respects the configured language

### v6.0.0

* Petitions and donation forms can now be embedded using WordPress's new Block Editor and Gutenberg plugin

### v5.3.0

* CleverReach is now available as a mailing list provider in a closed beta
* Petitions can now be configured to have an optional comment field
* Improved status indication for identity verification with Stripe fundraising
* A bug causing the list of supporters to be empty when there are more than 25 supporters on Edge 17 was fixed
* A bug causing several tables to be empty after changing filters on Edge 17 was fixed

### v5.2.0

* Petitions and donation forms are now available in English (United States)
* Improved setup and configuration of Civist Fundraising

### v5.1.2

* A bug preventing changing pages in tables is now fixed

### v5.1.0

* Petitions and donation forms can now optionally be configured to use a specific supported language instead of automatically choosing the language based on the browser settings.

### v5.0.3

* Civist plugin now requires PHP version 5.6 or newer
* New and improved setup flow for Civist Fundraising
* Stripe is now available as a Payment Service for Civist Fundraising as a closed beta
* The list of all collected donations is now directly accessible from the Civist dashboard
* Improved shortcode creation for a Petition or Donation form
* Improved live updates of the petition progress bar
* Improved handling of the Escape key inside the country field in the petition and donation forms
* Improved tooltips on touch devices
* Improved accessibility for the petition and donation forms
* A bug preventing the petition to load after clicking the confirmation link in rare cases is now fixed
* A bug preventing Safari users from signing a petition while surfing in private mode is now fixed

### v4.5.17

* Petitions and donation forms are now available in Slovak
* Improved compatibility with uncommon encoding configurations

### v4.5.11

* Fixed an issue where users using Safari with the latest iOS version were unable to sign a petition

### v4.5.10

* You can now find GDPR related information in the plugin settings
* Prepare plugin for Civist v5.0.0

### v4.5.9

* Fixed a compatibility issue with some older PHP versions

### v4.5.8

* Prepare plugin for Civist v5.0.0

### v4.5.6

* Fixed an issue that prevented the plugin from working with some WordPress database configurations

### v4.5.3

* Fixed an issue where signing a petition would fail with some WordPress themes

### v4.5.1

* Petitions and donation forms now available in Korean
* Fixed an issue where the goal and progress bar of a petition would be visible when a goal for the petition wasn't defined
* Fixed an issue that would prevent removing a previously configured goal for a petition

### v4.5.0

* You can now insert petitions and donation forms into a post or page using a shortcode

### v4.4.3

* Fixed a bug where clicking the Civist button in the post/page editor would do nothing in Internet Explorer 11
* Fixed a bug where embedding a petition or donation form into a post or page would fail in Internet Explorer 11
* Fixed a compatibility issue with some plugins that use Webpack

### v4.4.2

* Fixed a bug that, in certain cases, had prevented supporters from signing a petition, making a donation, or subscribing

### v4.4.0

* Country fields now get pre-filled with the user's location on petitions and donation forms
* Fixed a bug while editing a petition or donation form that prevented the preview to update after a previously assigned cover image was removed
* Fixed a bug that stalled the UI when an error occurred while setting up the fundraising on the WordPress plugin

### v4.3.5

* Fix compatibility issue with WordPress 4.9

### v4.2.9

* Fix theme incompatibility issue that would prevent a supporter from signing or donating
* Fix issue where exported signees/subscribers would not be filtered by petition
* Add missing translation

### v4.2.8

* Fixed an issue where inserting petitions and donation forms was not possible if WordPress was not installed on the site root
* Fixed an UI issue in the insert petition dialog for some specific screen sizes
* UX improvements

### v4.2.0

* Searchable list of all donations now available in the plugin's supporter management section
* Donation forms can now be embedded directly into a page or post (independently from a petition)
* Donation edit screen now has a list of all petitions that the donation form is associated with
* Petition lists display the associated donation form of a petition
* Petition lists can be filtered by associated donation forms
* Petitions and donation forms translated to Dutch, French, Italian, Catalan, Basque and Polish
* Reconnect function for trouble shouting problems with plugin settings
* Minor UI improvements

### v4.1.3

* Mailjet Integration: bug fix for newsletter subscriptions

### v4.1.2

* Translations: German petition and fundraising forms use "Sie" instead of "Du"

### v4.1.1

* Update translations

### v4.1.0

* New feature: save newsletter subscribers directly to your Mailjet contact lists

### v4.0.3

* Petition and donation forms in Spanish and Portuguese

### v4.0.2

* Improve compatibility with other plugins
* UX improvements

### v4.0.1

* Fix "connection lost" issue when registering with Civist

### v4.0.0

* The initial release (petitions, SEPA / CC fundraising and supporter management)

## Upgrade Notice

We recommend that you always use the latest version of Civist to omit any problems for you and your supporters.
