=== WPAdverts - Classifieds Plugin ===
Plugin URI: https://wpadverts.com/
Contributors: gwin
Tags: classifieds, classified, classified ads, classifieds script, classifieds plugin
Requires PHP: 5.6
Requires at least: 5.7
Tested up to: 6.7
Stable tag: 2.1.8
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Build classifieds section in seconds. Allow your visitors to browse and post (paid or free) classified ads on your site.

== Description ==

This is the popular classifieds plugin that experienced users stay with or come back to!

More than 10 years of experience led us to build WPAdverts around 3 key points:

* FASTER
* MORE FLEXIBLE
* MORE FREEDOM!

WPAdverts is a fast, lite-weight plugin that’s flawless on ANY device and fits with ANY theme! 

It also plays nicely with other plugins, giving you the freedom to quickly create the classifieds site that YOU want, as a flexible foundation that can grow with you and your community.

= ANY HOSTING? YES! =
 
There’s no need for expensive hosting with WPAdverts, because our crafty WordPress-standards coding keeps your server-load low and your loading-times FAST!

= EASIER BY DESIGN =

Everything comes together like you’d expect.

WPAdverts blends seamlessly with WordPress, making it super-simple and familiar; you’ll feel like a pro’ in approx’ 3 minutes!

= SPEEDY, EXPERT SUPPORT =

If you ever do get stuck you can rest assured of fast support by someone who really knows WordPress!

We also have extensive documentation and helper videos too.

= EXTENSIONS? OF COURSE! =

We DO NOT confuse or “nickel and dime” you at every step; instead we simply offer 3 value-packed packages. See pricing below.

= TAKE ACTION; TAKE CONTROL =

Note this is more than a demo; it’s a fully-functioning classifieds software with all critical functions built-in; you can start TODAY. 

= INSTALL =

Click the Installation tab at the top of this page to see how you install WPAdverts – or just click the blue Install Now button at the bottom of this page!

Install WPAdverts now and you’ll understand our 5 star reviews and why people keep coming back, building their success upon success!


**Quick Links**

* [View User Demo](https://demo.wpadverts.com/lite/) – see plugin frontend (plain)
* [View Admin Demo](https://demo.wpadverts.com/lite/wp-admin/) – see wp-admin panel
* [WordPress Classifieds Plugin](https://wpadverts.com/) – official site
* [WPAdverts Pricing](https://wpadverts.com/pricing/) – 3 affordable packs!

**Notable Features**

WPAdverts has ALL the classifieds features you’d expect, and some you might not:

* Modern, responsive design for any device or screen size
* Freedom to work with the very best of other plugins
* Choose Grid or List Display – and allows your visitors to switch too!
* You can allow both registered and/or anonymous users to post classifieds
* Allows users to browse, edit and delete their own ads
* Payments Module to track user payments and transactions logs
* Bank Transfer payment gateway is included, for free
* Charge users for renewing auto-expired ads
* 12 translations available, see list below
* Easy drag-and-drop image upload
* Detailed documentation for both developers AND normal users!

**Documentation**

* [Documentation](https://wpadverts.com/docs/) – mainly for users
* [Short-codes](https://wpadverts.com/docs/shortcodes/) – quick reference for users
* [Code Snippets](https://github.com/simpliko/wpadverts-snippets) – for programmers and developers

**Extensions**

WPAdverts plugin can be extended with premium add-ons to suit and your business, available in 3 convenient and affordable packs; Personal, Pro or Ultimate. 
See all the extensions available at https://wpadverts.com/extensions/

**12 Available Translations**

Brazilian, Croatian, French, Dutch, German, Italian, Polish, Portuguese, Russian, Slovak, Slovenian and Spanish.
We’re not English native speakers, so if you find any typo, grammar mistake or etc. please report it on the support forum, thanks!
Could you translate Adverts to your language? Please do help users by submitting your translation!
Get Involved!

* Rate Adverts – If you find this plugin useful please do leave a [positive review](https://wordpress.org/support/view/plugin-reviews/wpadverts)  : )
* Submit a Bug – If you find any issue please [submit a bug](https://github.com/simpliko/wpadverts/issues/new) on GitHub. 

Get started!

Click the blue button on the lower right-hand side of this page that says ‘Install Now’. It’s quick and easy!


== Installation ==

1. Activate the plugin
2. On activation plugin will create two Pages (in wp-admin / Pages panel)  with [adverts_list] and [adverts_add] shortcodes .
3. Go to Classifieds / Options panel and configure the options.
3. For more detailed instructions visit plugin [documentation](https://wpadverts.com/documentation/)

== Frequently Asked Questions ==

= I have a problem what now? =

Please describe your issue and submit ticket on plugin support forum, you should receive reply within 24 hours (except weekends).

= Ads pages are showing 404 error? =

Most likely rewrite rules were not registered properly for some reason. Go to wp-admin / Settings / Permalinks panel and click
"Save Changes" button without actually changing anything, this should reset router and fix URLs.

== Changelog ==

= 2.1.8 - 2024-11-19 =

* FIXED: XSS in the [adverts_manage] shortcode.
 
= 2.1.7 - 2024-10-29 =

* FIXED: XSS when loading contact details.
* FIXED: Nonce validation when revealing contact details on the Ad details pages.
* FIXED: Gallery icon showing on Ad details pages even when there are no images uploaded.
* FIXED: Fatal error when null is passed to adverts_filter_money() function.

= 2.1.6 - 2024-08-19 =

* API: The minified Tailwind CSS file was renamed and moved to /assets/css folder.
* FIXED: Improvments and fixes for the new slider to better integrate it with various themes.
* FIXED: wpadverts-blocks-editor-search stylesheet generates incorrect version number.
* FIXED: Use 'echo esc_attr()' instrad of 'esc_attr_e()' function where appropriate.

= 2.1.5 - 2024-08-08 =

* FEATURE: Redesigned Gallery Block - the block is now more intuitive to use, is responsive and supports touch navigation.
* FEATURE: In the Classifieds List Block you can enable image slider to allow users to see images without going into Ad details.
* FEATURE: Added Radio button preview to the wp-admin / Classifieds / Options / Default Styles preview.
* FIXED: Loading images for both grid and list view in the Classifieds List block.
* FIXED: Changing price color in the Classifieds List block does not work.
* API: To revert to the old Gallery add in your wp-config the define("WPADVERTS_BLOCK_GALLERY_USE_OLD", true); line.

= 2.1.4 - 2024-07-16 =

* FIXED: Blocks trying to load JavaScript file that does not exist.
* FIXED: Multiselect input not inheriting the styling correctly in the block mode.

= 2.1.3 - 2024-03-18 =

* FIXED: XSS vulnerability in administration panel.
* FIXED: Patched CSFR vulnerability. 
* FIXED: Replaced esc_attr_e() functions with esc_attr() where applicable.
* FEATURE: Compiled blocks using updated libraries (improved performance when using blocks in the wp-admin panel).

= 2.1.2 - 2024-02-19 =

* FIXED: List Data input not working correctly in the Classifieds Manage block
* FIXED: Contact Phone button in Contact Block should be hidden when the phone is not provided.
* FIXED: Changes to some phrases in the wp-admin / Classifieds / Options to make them more self explanatory.
* FIXED: $_checksum_args property missing (causing a notice/warning in the Classifieds Publish block). 
* FIXED: Selecting "None" in the taxonomy rendering option is not working correclty.
* FIXED: Data Table block not filtering fields correclty by the include_fields option.
* FIXED: Classifieds Search block not loading CSS code.
* FIXED: Not possible to move the Single Notification block.
* API: wpadverts/block/single-contact/contact-options filter added.
* API: wpadverts/block/field/types filter allows registering new field types.
* API: Introduced is_visible property to the contact options API 

= 2.1.1 - 2023-11-15 =

* FEATURE: More accurate field type classes in the HTML code generated for the block inputs.
* FIXED: Block Single Data Table not filtering by exclude_fields correctly.
* FIXED: Block Single Value generating a fatal error.
* FIXED: Custom Post Types and Taxonomies not restoring correctly in the wp-admin / Classifieds / Options / Core / Types panel.
* FIXED: Block "radio" inputs generating with incorrect name (with a [] at the end).
* FIXED: In the block rendering checking if {input}_block is a function name.
* API: wpadverts/block/tpl/wrap/value filter added to the wpadverts_block_tpl_wrap() function.
* API: adverts_manage_edit_back_button filter in [adverts_manage]
* API: adverts_manage_edit_public_link filter in [adverts_manage]
* LANGUAGE: Updated German translation.

= 2.1.0 - 2023-08-23 =

* FEATURE: 5 new Classifieds Single blocks allow visually building Ad details pages.
* FEATURE: Classifieds Categories block allows auto-detecting current category and hiding the 'No categories found' text.
* FEATURE: CSS classes added meta items generated in the Classifieds List block.
* FIXED: Warning in the Classifieds Manage block

= 2.0.5 - 2023-05-24 =

* FIXED: Blocks JavaScript compiled without references to js.map files.
* FIXED: Classifieds List block "posts per page" option not saving.
* API: wpadverts_disable_gallery_backcompat filter added to disable gallery backward compatibility.

= 2.0.5 - 2023-04-03 =

* FEATURE: Icon for the new Analytics extension.
* FIXED: Loading the ADVERTS_PLUPLOAD_DATA data in the wp_footer.
* FIXED: Loading options for blocks fail if the CF extension is not available.
* FIXED: Classifieds List block / List Data dropdown is not showing all possible options.
* FIXED: Using depracated block_categories filter instead of block_categories_all.
* FIXED: Check marks not showing for the checkbox and radio inputs in the contact form.
* FIXED: Warning thrown when object does not have any taxonomy terms assigned.
* FIXED: Remove all index.js.map files (these files are for development only).
* FIXED: Warning in the contact form.
* FIXED: Author Types in the wp-admin / Classifieds / Options / Core / Types panel not saving properly.
* API: adverts_sort_images filter added to the adverts_sort_images() function.

= 2.0.4 - 2022-11-29 =

* FEATURE: [adverts_block] shortcode allows rendering blocks using the shortcode syntax.
* FIXED: Reveal phone button not working when no image is uploaded to the gallery.
* FIXED: Some phrases in block templates not marked correctly for translation.
* FIXED: Internal server error when scheduling an advert from the wp-admin panel.
* FIXED: Files upload_limit validator not working correctly when the form has two or more file fields.
* FIXED: Classifieds / Publish block showing incorrect Form Schemes

= 2.0.3 - 2022-10-20 =

* FIXED: allow_sorting param not working correctly in the Classifieds Manage block.
* FIXED: Error when saving a custom taxonomy in the wp-admin / Classifieds / Options / Types panel without the "labels".
* FIXED: Error when saving a custom post type in the wp-admin / Classifieds / Options / Types panel without the "labels".

= 2.0.2 - 2022-10-04 =

* FEATURE: Redesigned wp-admin / Classifieds / Options panel.
* FEATURE: Added block pattern that allows displaying Classifieds / Search and Classifieds List blocks next to each other
* FEATURE: Switch beteween different options for matching blacklisted words in the wp-admin / Classifieds / Options / Core / SPAM panel.
* FEATURE: In the wp-admin / Classifieds / Options / Core you can set a custom date format for Classifieds / List and Classifieds / Details blocks.
* FEATURE: API changes in the wp-admin / Classifieds / Options / Types panel to prepare the plugin for use with the upcoming Multiverse extension.
* FIXED: allow_sorting notice showing in the Classifieds / Manage block in the frontend.
* FIXED: Pagination (in [adverts_list] and Classifieds / List block) not working correctly on custom taxonomy pages.
* FIXED: When selecting a taxonomy for display in the Classifieds / List block the taxonomy title did not show in the block.

= 2.0.1 - 2022-07-25 =

* IMPORTANT: This is a major release please do a full site backup before upgrading or test the update on your staging website first!!! 
* The new version allows building a classifieds website with new design and customizable blocks instead of shortcodes.
* If you would like to migrate from shortcodes to blocks please read https://wpadverts.com/doc/migrating-to-version-2-0/
* If you would like to keep using shortcoded version you do not need to do anything (except for the website backup).
* FIXED: Block related scripts and styles are now loaded only when needed.
* FIXED: The blocks CSS file no longer loads wp-edit-blocks stylesheet.
* FIXED: When ads list in Classifieds / Manage is empty invalid buttons are not shown.
* FIXED: Loading gallery icons on the Ad details pages in Block mode.
* API: You can force plugin to always load scripts and styles by adding below line in your theme functions.php file
* API: add_filter( "wpadverts/blocks/load-assets-globally", "__return_true" );

= 2.0.0 - 2022-07-21 =

* SEE 2.0.1 notes.

= 1.0 - 2015-08-11 =

* First Release *

== Screenshots ==

1. Ads Search and List blocks displayed in a grid mode.
2. Ads Search and List blocks displayed in a list mode.
3. Ads list on a mobile device.
4. Ads grid on a mobile device.
5. Quickly customize dozens of display options directly from the wp-admin panel.
6. Match your theme styling using the Styling Editor.
7. Ad details page.
8. Ad details page on a mobile device.
9. Publish Ad Form - allow anonymous and/or registered users to post ads.
10. Management Panel - allow users to manage their own ads.
11. List available categories () 
12. Ads list in wp-admin panel.
13. Ad edit page in wp-admin panel.
14. Category edition with icon select.
15. Options, modules and premium extensions.
16. Payment history - if you are planning to charge users for posting Ads.
17. Payment details.

== Upgrade Notice ==

= 1.0 - 2015-08-11 =

* Just try it, you will like it.
