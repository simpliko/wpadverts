=== WPAdverts - Classifieds Plugin ===
Plugin URI: https://wpadverts.com/
Contributors: gwin
Tags: classifieds, classified, classified ads, classified script, classifieds script, wp classified, wp classifieds
Requires PHP: 5.3
Requires at least: 4.0
Tested up to: 5.7
Stable tag: 1.5.3
License: GNU Version 2 or Any Later Version

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
 
There’s no need for expensive hosting with WPAdverts, because our crafty Wordpress-standards coding keeps your server-load low and your loading-times FAST!

= EASIER BY DESIGN =

Everything comes together like you’d expect.

WPAdverts blends seamlessly with WordPress, making it super-simple and familiar; you’ll feel like a pro’ in approx’ 3 minutes!

= SPEEDY, EXPERT SUPPORT =

If you ever do get stuck you can rest assured of fast support by someone who really knows Wordpress!

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

= 1.5.3 - 2021-03-23 =

* FEATURE: Data Types editor in wp-admin / Classifieds / Options / Core / Types panel, allows customizing post types and taxonomies.
* FEATURE: in wp-admin / Classifieds / Options / Core panel you can enable option to moderate ads updated in [adverts_manage].
* FIXED: Removed max_file_size option from the plUpload (it sometimes conflicted with the max file size settings in WPAdverts).

= 1.5.2 - 2021-02-09 =

* TRANSLATION: Arabic.
* TRANSLATION: Greek.
* FEATURE: New currencies: Egyptian Pound, Kuwaiti Dinar, Saudi Riyal, UAE Dirham, Qatari Riyal.
* FEATURE: {format_date} function in email templates now accepts a second argument, that is the date format to use.
* FEATURE: Users cannot open dropdown fields marked as readonly.
* FIXED: Bug in Files API that does not allow deleting files.
* FIXED: Sanitize file name before saving it on the server.

= 1.5.1 - 2020-12-14 =

* FIXED: File Upload API mixing additional file fields order.
* FIXED: File Upload API uses additional uploaded files as featured images.
* FIXED: Removed no longer used script from wpadverts-gallery.js
* FIXED: Emails Module generates a warning when using PHP7.4+
* FIXED: Some user entered texts not escaped properly (undisclosed details for security)
* FIXED: Fatal error when deleting Advert (if using WP older than 5.5)

= 1.5.0 - 2020-11-23 =

* FEATURE: File Uploads API
* FEATURE: Attaching files to messages in Emails Module
* FEATURE: {$advert_files} variable available in Emails which already support {$advert} variable
* FIXED: Error which duplicates "Post a Job" pages.
* FIXED: Renewal selection page shows only 5 options.

= 1.4.5 - 2020-08-13 =

 * FEATURE: Added Nigerian Naira currency.
 * FEATURE: Added signs for CZK, HUF, NOK, PHP and SEK currencies.
 * FEATURE: Pending Ads will now show in [adverts_manage] panel.
 * FIXED: Error when uploading more than one file to the gallery.
 * FIXED: Notice in the gallery in [adverts_add] shortcode.
 * FIXED: Autocomplete field will pre-render values to avoid having values unset when form is submitted before autocomplete field finishes loading.
 * FIXED: Line breaks not shown in text editor when a page is not using blocks (WP 5.5 compatibility issue)
 * FIXED: Removed duplicate bind code in [adverts_manage].
 * API: adverts_field_textarea_tinymce_params filter allows settings tinyMCE params for textarea field.

= 1.4.4 - 2020-06-16 =

* FEATURE: Allow using Autocomplete field with any taxonomy.
* FIXED: Preserve Ad author when approving ads using Quick Edit.
* FIXED: Hardcoded Autocomplete phrases.
* FIXED: Autocomplete field does not activate when selecting a top category.
* FIXED: Indian Rupee will now use ₹ sign instead of INR code.
* API: Form fields can now use a custom save method.
* API: adverts_field_autocomplete can be used in the forms.
* API: Saving Ad from wp-admin / Classifieds panel will now use Adverts_Post::save()
* TRANSLATION: Updated Italian translation

= 1.4.3 - 2020-04-23 = 

 * FIXED: Contact information box does not load.
 * FIXED: Delete option does not fire in [adverts_manage].
 * API: Use wpadverts_post_type() with Featured module.

= 1.4.2 - 2020-04-22 =

 * FEATURE: First image on the list (when posting an Advert) is automatically selected as featured.
 * FEATURE: Autocomplete script rewritten (will be used in next MAL version and with Categories in [adverts_add] after that). 
 * FIXED: warnings in wp-admin / Appearance / Widgets
 * API: Groundwork for for using WPAdverts with multiple Custom Post Types (not complete nor documented yet)
 * API: adverts_plupload_default_settings filter applied on Gallery plupload settings
 * API: adverts_tax_term_description filter applied on term descriptions

= 1.4.1 - 2020-03-05 =

 * WARNING: If you are upgrading from version 1.3.7 or older please read the below article first.
 * https://wpadverts.com/blog/wpadverts-1-4-0-release/
 * FEATURE: Support for webp images (although requires additional plugin like wp-enable-webp).
 * FEATURE: On advert-category pages the [adverts_list] supports category="current" param (useful when desigining category pages using Elementor).
 * FIXED: Incompatibility with upcoming PHP7 version.
 * FIXED: Incorrect data in the advert-category feed.
 * FIXED: Pagination on advert-categort pages.
 * FIXED: Invalid variable name passed to wpadverts_user_saved action.
 * FIXED: Escaped quote characters in Polish translation.

= 1.4.0 - 2020-02-06 =

 * WARNING: This is major update with potential backward-incompatible changes!
 * DO NOT UPDATE WITHOUT READING THE RELEASE NOTES BELOW!
 * https://wpadverts.com/blog/wpadverts-1-4-0-release/

= 1.3.7 - 2019-11-06 =

 * FIXED: Internal bug in the Gallery when adding, editing or deleting images.

= 1.3.6 - 2019-09-19 =

 * FEATURE: Option to hide 'free' payments in the wp-admin / Classifieds / Payments History
 * FEATURE: Ability to hide Advert files in wp-admin / Media Library (and Media Library overlays).
 * FEATURE: Added a close icon in the Gallery on Ad details pages when gallery is in thumbnails mode.
 * FEATURE: Total sales added at the bottom of wp-admin / Classifieds / Payments History list.
 * FIXED: Sorting payments by selected month payments in wp-admin / Classifieds / Payments History does not work.
 * FIXED: Payment status does not change when updating a payment from wp-admin / Classifieds / Payments History panel.
 * FIXED: Browser AdBlockers are hiding some inputs in wp-admin / Classifieds / Options / Core panel.
 * API: adverts_form_field_option_id filter allows customizing the element option id.
 * API: Field options (radio, select and checkbox) support an "id" param now.
 * TRANSLATION: Hungarian translation added
 * TRANSLATION: Italian translation updated

= 1.3.5 - 2019-09-03 =

* FEATURE: Updated list of icons to version 4.7.0, that is about 200 icons added.
* FEATURE: Extended Payments Module API to allow registering different payment types.
* FIXED: Some assets (CSS and JS files) are being blocked by AdBlockers due to file names starting with "adverts-*".
* FIXED: Payments History edit page does not load correct form scheme if a payment gateway is using different form then the standard one.

= 1.3.4 - 2019-06-25 =

 * FEATURE: Recent Ads widget allows now showing: featured, normal or all ads.
 * FEATURE: Extended Payments API to allow custom pricings (will be used with Membership extension).
 * FIXED: adverts_payment_status_change filter uses incorrect arguments.
 * FIXED: Categories widget does not use filter which allows customizing icons for sub-categories.
 * FIXED: Email messages in Emails module are being sent even if disabled.
 * FIXED: When wp_insert_post() function fails in [adverts_add] an error message should be shown on screen (instead of preview)
 * FIXED: Some phrases could not be translated due to missing text domain.
 * FIXED: Fatal Error shown in wp-admin / Payments History when trying to access payment which does not exist.
 * TRANSLATION: Italian translation added

= 1.3.3 - 2019-05-08 =

 * FEATURE: "alt" and "title" tags for images in [adverts_list].
 * FEATURE: "Complete Payment" button will show in [adverts_manage] if user posted an Ad but not payed for it.
 * FEATURE: if adverts_email is not provided in [adverts_add] then contact form will be sent to user email address if Ad was posted by logged in user.
 * FIXED: Labels for advert_category taxonomy (fixes conflict with WP Post Modules plugin).
 * FIXED: Success message not visible after submitting contact form.
 * FIXED: HTML glitch in wp-admin / Classifieds / Options (visible on some installations on mobile devices).
 * FIXED: Notices showing when viewing wp-admin / Classifieds / Emails panel.
 * FIXED: Notices showing after submitting contact form with Emails Module enabled.
 * FIXED: Contact form is loaded only when "Send Message" button is visible on Ad details page.


= 1.3.2 - 2019-03-21 =

 * FIXED: Ability to manage other users images.
 * FIXED: Email templates other than Core do not show updated version when editing them.
 * FIXED: Notices in Emails module configuration.
 * FIXED: If "Visibility" in Settings is set to 0, the Ad posted from wp-admin instantly expires.
 * FIXED: (In some themes) Infiniete loop in [adverts_add] Preview due to the_content filter being used.
 * API: Additional CSS row widths added to adverts-frontend.css.
 * API: Contact information are now loaded differently. First the button and with a second apply_filters call the box which the button opens. 
 * NOTE: If you changed how how contact options work on Ad details page make sure they are working properly after the update.

= 1.3.1 - 2019-03-07 =

 * FIXED: Fatal Error on some older PHP version when enabling Emails Module.
 * FIXED: Setting default visibility to 0 should set the Expiration Date to Never Expires when posting from wp-admin.
 * FIXED: Contact Form message is not being sent after enabling Emails Module.

= 1.3.0 - 2019-02-27 =

 * FEATURE: Emails Module allows configuring and sending email messages when various actions occur (for example when user posts an Ad).
 * FEATURE: Secure "Complete Payment Link" if a user will not complete payment when posting an Ad he can do that using unique payment link.
 * FEATURE: Random Order option added to the Recent Ads widget.
 * FEATURE: The payment object is created on load in [adverts_add] third step (it makes it easy to complete payment later and prefill data).
 * FEATURE: Integration with Emails Module for Payments Module.
 * FEATURE: Integration with Emails Module for Contact Form Module (allows editing the contact form message template from wp-admin panel).
 * API: The video in gallery can use the default controls after adding add_filter( "adverts_gallery_enable_custom_controls", "__return_false" ) line in theme functions.php
 * API: The adverts_payment_$status filter should be run once only (on status change).
 * TRANSLATION: Updated French translation.

= 1.2.8 - 2018-11-29 =

* FEATURE: Allow non-expiring paid listings.
* FIXED: The free listings purchased using Payments module were not featured correctly.
* FIXED: When renewing listings the is_featured flag is never changed.
* FIXED: Pagination in [adverts_manage] displays next to the list (instead of below the list).
* API: wpadverts_filter_pricings_options filter - allows filtering available pricings.
* API: verify_choices form filter - allows checking if selected options (in select or checkboxes list) are valid values.
* API: "disabled" param was added to select, checkbox and radio input options.
* API: adverts_manage_moderate filter allows putting Advert into moderation when renewing.
* API: adext_insert_payment() function allows quickly creating payment programatically.

= 1.2.7 - 2018-10-09 =

* FEATURE: Added Stripe panel in wp-admin / Classifieds / Options
* FIXED: On mobile devices the page background is being scorlled instead of the gallery image editor.
* FIXED: Filling radio input options using callback function does not work.
* FIXED: [adverts_add] prefills values only for the advert_category taxonomy (added support for other taxonomies).
* API: Payments Module /payments.js file allows callback to customize checkout experience for different payment gateways.
* API: wpadverts_filter_pricings filter allows filtering pricings before they will be displayed in [adverts_add]. 
* TRANSLATION: Updated Spanish translation

= 1.2.6 - 2018-08-30 =

* FEATURE: In Settings it is not possible to set a text which will display instead of price if price was not provided.
* FIXED: Boxes on [adverts_list] not reszied correctly, which sometimes broke the grid layout.
* FIXED: max_choices validator shows a warning message when only one value is passed to validation.
* FIXED: Unicode characters in the URL break the file upload.
* TRANSLATION: Updated German translation

= 1.2.5 - 2018-07-04 =

* FIXED: Thumbnails slider not showing all slides.
* FIXED: Not all pricings are shown when there is long list of pricings (over 20).
* FIXED: Allow using .adverts-button CSS class for input[type=submit].
* FIXED: The is_featured param not passed to Advert when renewing.
* FIXED: Incorrect parameter passed to adverts_payment_* filter.

= 1.2.4 - 2018-05-10 =

* FIXED: Fatal Error After 1.2.3 update.

= 1.2.3 - 2018-05-10 =

* TRANSLATION: Improved Polish Translation.
* FEATURE: Ability to handle Expired Adverts pages.
* FEATURE: Added link to Authors extension in wp-admin / Classifieds / Options panel.
* API: adverts_attachment_uploaded action allows modifying attachment after upload.
* FIXED: Updated CSS for textarea, checkbox and radio inputs marings and paddings.
* FIXED: Inline styling in add.php template replaced with actions_class.
* FIXED: Inline styling in form.php template replaced with actions_class.
* FIXED: Updated headers and removed inline styling in manage-edit.php template.

Read release notes here https://wpadverts.com/blog/handling-expired-adverts-pages/

= 1.2.2 - 2018-03-13 =

* TRANSLATION: Improved Brazilian Translation.
* FEATURE: Ability to show an image on the left side in Recent Adverts widget.
* FEATURE: On Advert details page user ID will be used to get an Avatar, the adverts_email will be used as a fallback.
* API: adverts_field_password added to the Forms API
* API: filter wpadverts_module_groups added to allow dynamic group adding in wp-admin / Classifieds / Options
* FIXED: The padlock icon in wp-admin / Plugins list shows too big.
* FIXED: Gallery shows black when jQuery 2.0 or newer is being used.
* FIXED: Warning in Featured Ads module.
* FIXED: Image gallery not showing (when Ad has some images uploaded).

Read release notes here https://wpadverts.com/blog/images-in-recent-ads-widget-wpadverts-1-2-2/

= 1.2.1 - 2018-01-29 =

* TRANSLATION: Slovak
* FEATURE: The first image in the Gallery will be used in [adverts_list] (unless featured image is selected).
* FEATURE: [adverts_list] now accepts list_type param which allows to show only Featured or Normal ads.
* FEATURE: The email and name are prefilled in the Contact Form if logged in user is viewing the page.
* FEATURE: Sri Lankan Rupees added to the currencies list.
* FEATURE: Ability to display forms as either: stacked (input below label) or aligned (input next to label).
* FIXED: Loading icon not spinning when clicking on "Show contact Information".
* FIXED: Uploaded files validation not working properly.
* FIXED: File upload not working with modified jQuery.
* FIXED: Image CW and CCW rotations mixed.

Read release notes here https://wpadverts.com/blog/mark-as-sold-custom-fields/

= 1.2.0 - 2017-12-05 =

* NEW: Image edition in WPAdverts Gallery (available for logged in users by default).
* NEW: Video upload, note only web video (mp4, webm and ogg) is accepted.
* NEW: Redesigned image gallery in the frontend with better optimization for mobile devices.
* NEW: Additional Gallery options in wp-admin / Classifieds / Options / Core / Gallery.
* NEW: Ability to display image gallery with thumbnails slider below image.
* NEW: Lightbox allows displaying images and videos in fullscreen mode on click.
* API: Added file upload validators
* FIXED: "Send Message" phrase marked for translation incorrectly.
* FIXED: It is now possible to disable Money filter in adverts_price field.
* REMOVED: Responsive Slides jQuery plugin

Read release notes here https://wpadverts.com/blog/video-uploads-and-image-editing-v1-2-0/

= 1.1.7 - 2017-11-08 =

* NOTE: The update 1.2 is coming soon, this one is a quick patch to fix incompatibility with WP 4.9
* FIXED: Gallery Upload incompatibility with WordPress 4.9

= 1.1.6 - 2017-09-04 =

* TRANSLATION: British
* FEATURE: Updated [adverts_list] search form CSS
* FEATURE: Added Required PHP version (for infomational purposes only)
* FEATURE: Ability to display Adverts Categories Widget as a categories tree
* FIXED: Display properly [adverts_list] with 4 columns
* FIXED: Use esc_attr() and esc_html() function when needed (for better WP compatibility)
* FIXED: Some flash messages displayed in an old mode
* FIXED: Incorrect tag closing in pt_BR translation
* API: Actions and filters for replacing default icons with custom images
* API: adverts_category_post_count filter allows to recalculate number of Ads in a category

= 1.1.5 - 2017-07-10 =

* FEATURE: Updated design for flash (error and info) messages.
* FEATURE: In wp-admin left menu the Classifieds menu has a red icon with number of pending Ads
* FEATURE: [adverts_add] has now a "requires" param which allows to show the shortcode only to users with specific capability
* FEATURE: When on Ad detail page, the Ad category has an additional CSS class in the Adverts Categories widget
* FIXED: Access to Adverts options requires now manage_options capability instead of install_plugins
* FIXED: Fatal Error in [adverts_manage] (occurs on some installations with BuddyPress enabled)
* FIXED: Impossible to uncheck the "Show Phone" option in Contact Form configuration
* FIXED: Changing number of dcimal places in configuration applies this change to Price field in [adverts_add]

= 1.1.4 - 2017-06-09 =

* TRANSLATION: Croatian
* FEATURE: Beautiful Advert edit forms in wp-admin / Classifieds panel.
* FEATURE: Do not show Contact Form on Ad details page if user did not enter contact email in [adverts_add].
* FEATURE: Display full category tree in Adverts Categories widget.
* FEATURE: CSS class for current category in Adverts Categories widget.
* FEATURE: Small update to Gallery upload layout.
* FIXED: Warning message when displaying drop down field with no options.
* FIXED: Disallow shortcodes and some HTML tags in the Advert content
* API: 2nd param in adverts_list_query filter

= 1.1.3 - 2017-04-26 =

* FEATURE: Google Analytics integration box in wp-admin / Classifieds / Options.
* FEATURE: Display [adverts_list] in list mode on mobile (if selected).
* FEATURE: Removed images from [adverts_manage] on mobile devices.
* FEATURE: Free Ads renewals cannot be used more than once a week.
* FEATURE: Display category description above [adverts_list] when browsing Ads by category.
* FEATURE: Allow sorting Ads in the [adverts_list] (by default disabled).
* API: allow_sorting and order_by params in [adverts_list].
* API: adverts_renewal_time_min filter allows to set how often free renewals can be used.
* API: adverts_sh_manage_actions_after filter.
* FIXED: Renewing updates post publication date (basically renewing bumps Ad to the top).
* FIXED: Missing adverts_filter_number function.
* FIXED: Do not display location icon on [adverts_list] if location was not provided.
* FIXED: Advert renewal page in [adverts_manage] does not load required assets.

= 1.1.2 - 2017-03-30 =

* TRANSLATION: Spanish
* TRANSLATION: Dutch
* API: adverts_data_box_content_exclude filter added
* API: max_choices validator for dropdown and checkbox fields.
* API: show_pagination param added to [adverts_list].
* FIXED: Payments Module not showing all available pricings.
* FIXED: Replaced annonymous sorting function with a static function (to make WPAdverts compatible with PHP 5.2 again).
* FIXED: Adverts are being assigned to user who is editing the Ad from wp-admin.
* FIXED: Incorrect textarea width in the frontend forms.
* FIXED: adverts_price() throwing a warning when price is incorrectly formatted.

= 1.1.1 - 2017-01-06 =

* FIXED: Not displaying warning in [adverts_manage] for unregistered users.
* FIXED: Clearing list saved licenses when saving Core settings.
* FIXED: Typo in plugins list HTML (wp-admin / Plugins)
* FIXED: (API) Binding data to multiselect fields.

= 1.1.0 - 2016-12-30 =

* NEW: Redesigned [adverts_manage] layout
* NEW: "Are you sure?" question before deleting an Ad in [adverts_manage]
* NEW: Drag and drop image sorting in the gallery
* NEW: Renew ads from [adverts_manage]
* API: Ability to extend [adverts_manage] with additional buttons
* FIXED: Do not display Listing Type field in [adverts_manage]
* FIXED: Displaying meta data in [adverts_add]
* FIXED: Allow editing pending ads from [adverts_manage] panel.

= 1.0.12 - 2016-12-01 =

* FIXED: Displaying multiselect values in forms
* FIXED: Using `esc_html_e()` instead of `echo esc_html()`
* FIXED: Aligning fields in [adverts_list] search form
* FIXED: Unique order number for each field in [adverts_add] form
* NEW: Support for "description" in adverts_field_header
* API: Added "adverts_form_scheme" filter
* API: New field filters: Integer, Number, URL
* API: New field validators: Is URL, Is Number

= 1.0.11 - 2016-10-13 =

* NEW: Defaut values for [adverts_list]
* NEW: Custom Fields module link in wp-admin / Options
* NEW: 3 new code snippets in Snippets Library https://github.com/simpliko/wpadverts-snippets
* FIXED: Using `esc_html_e()` instead of `echo esc_html()`
* FIXED: Handle multiple taxonomies (if needed) in [adverts_manage].
* API: Alternative category browsing URLs (easy fix for categories if they are not compatible with your theme).
* API: Ability to Trash posts instead of deleting them in the frontend (to enable this feature add this line to your theme functions.php `add_filter("adverts_skip_trash", "__return_false");`)
* API: Saving Custom Fields as Taxonomies instead of Meta (go to Snippets library to see an example code)
* API: Allow to custom format the Advert price displayed in [adverts_list] and in Ad details.

= 1.0.10 - 2016-09-12 =

* TRANSLATION: Portugese
* NEW: Contact Form Module (you can enable it from wp-admin / Classifieds / Options)
* FIXED: adverts_config() causing a fatal error in WP-CLI
* FIXED: JavaScript error on Pricings list
* FIXED: 'advert' class replaced with 'classified' on Advert details pages (to avoid AdBlockers)

= 1.0.9 - 2016-08-14 = 

* NEW: Customizable "Recents Ads" widget.
* FEATURE: Administrator can save Advert from wp-admin panel even if required fields are not filled (he will saw notification though).
* FEATURE: Featured image will always be displayed as first one in the gallery.
* FEATURE: Communication with WPAdverts updates server (for premium addons) will be handled via secure SSL connection only.
* FEATURE: JavaScript and CSS files are now versioned, so always newest version will be loaded.
* FIXED: Images in Gallery are resized to completely fit inside the gallery box (previously if image was vertical a part of it was not displayed).
* FIXED: Clicking on multiselect dropdown will hide options (if the options are currently displayed).

= 1.0.8 - 2016-07-04 =

* FEATURE: [adverts_list] 'display' param allows to display Ads in a list instead of grid
* FEATURE: [adverts_list] 'switch_views' param allow users to switch between list and grid views
* FEATURE: [adverts_add] custom display for "Listing Type" field, so users will know they are selecting payment type, not filling yet another field
* FIXED: Updated HTML for showing hidden items in [adverts_list]
* FIXED: Removed "Account" field from [adverts_manage] (it's not needed there).
* FIXED: Deleting incorrect media library items when deleting Pricings or Payments History
* FIXED: [adverts_manage] displays "Listing Type" field if post_status = pending.

= 1.0.7 - 2016-06-13 =

* API: adverts_tpl_single_top and adverts_tpl_single_bottom filters on Ad details pages (allow to replace "show contact information" and default gallery)
* FEATURE: Advanced Search button on [adverts_list] (will show when some hidden fields are added to search form, see http://wpadverts.com/documentation/custom-fields-search-form/)
* FEATURE: [adverts_list] has two new params redirect_to and show_results (see https://wpadverts.com/documentation/creating-ads-list-adverts_list/)
* FIXED: Allow to disable money filter for Price field
* FIXED: Allow to set custom empty text for each multiselect field.
* FIXED: "Sign In" redirects user back to [adverts_add] instead of wp-admin
* FIXED: Posts with status 'pending' no longer show on site.

= 1.0.6 - 2016-04-06 =

* FEATURE: CSS classes in form rows (each form row has class name adverts-field-name-{$field_name}).
* FEATURE: Show Temporary Ads in wp-admin / Classifieds panel (this is a list of posts abandoned by users).
* FIXED: Listing duration for free ads not applied properly.
* FIXED: Set default [adverts_list] page on WPAdverts activation.
* FIXED: Using proper ajax URL variable in [adverts_add] when editing an image.
* FIXED: File upload on Edge
* FIXED: File upload on mobile devices
* FIXED: Disallow user registration with email address already existing in wp_users table [adverts_add].
* FIXED: Riderct loop when Ad content is empty.

= 1.0.5 - 2016-01-21 =

* TRANSLATION: Russian (thanks to @Mick Levin)
* FEATURE: [adverts_list] search form API,
* FEATURE: search_bar param in [adverts_list] shortcode.
* FEATURE: adverts_tpl_single_posted_by filter on Ad details page.
* FEATURE: adverts_tpl_single_location on Ad details page.
* FEATURE: autocomplete field API
* FEATURE: in wp-admin when creating Ad required fields are not makred properly.
* FIXED: Multiselect field displayed in wrong place in HTML code.
* FIXED: adverts_template_load for single.php file.
* FIXED: pagination on category pages.
* FIXED: jQuery dependecies.

= 1.0.4 - 2015-12-23 =

* TRANSLATION: French (thanks to @Ceyar)
* FEATURE: 'adverts_list_query'
* FIXED: Some 'Featured Ads' module files not commited.
* FIXED: Default visibility not working in wp-admin

= 1.0.3 - 2015-12-22 =

* TRANSLATION: German Updated
* TRANSLATION: Slovenian (thanks to @gdolenc)
* FEATURE: Category Widget, 'show top categories only' option
* FEATURE: Featured Ads
* FEATURE: Overriding templates using 'adverts_template_load' filter
* FEATURE: String Length Validator
* FEATURE: 'adverts_new_user_notification' filter
* FIXED: [adverts_manage] incorrect edit link
* FIXED: Incorrect Expiration Date on ads list in wp-admin
* FIXED: Is_Integer validator accepts texts
* FIXED: Advert Price reset, when marking payment as complete
* FIXED: Removing not needed 'slashes' in user submitted data
* FIXED: Expiration Date not changing when switching Ad from 'Never Expires'

= 1.0.2 - 2015-09-26 =

* TRANSLATION: German (thanks to @Juliandaddy)
* FEATURE: 'adverts-option-depth-x' class in multiselect dropdowns
* FEATURE: Improved design for search bar in [adverts_list]
* FIXED: Categories display on Android and iPhone/iPad
* FIXED: Sending registration emails in WP 4.3.0 and newer
* FIXED: Saving Ads categories in [adverts_manage]
* FIXED: Loading Adverts_HTML class in Categories widget
* FIXED: Logout link in [adverts_add]
* FIXED: Not translated phrase

= 1.0.1 - 2015-08-25 =

* FEATURE: Improved UX in [adverts_manage]. When unregistered user tries to access this page he will see Login and Register links in addition to error message.
* FIXED: Removed the_editor_content filter.
* FIXED: Set default user role for registered user to Subscriber.
* FIXED: HTML updates for WP 4.3
* FIXED: Default listing type selection in [adverts_add]
* FIXED: In [adverts_add] correct post names are now generated instead of 'Adverts Auto Draft'
* FIXED: Improved Gallery upload/edit/delete security.
* FIXED: Delete link not working on some installations with mod_security enabled.

= 1.0 - 2015-08-11 =

* First Release *

== Screenshots ==

1. Ads list [adverts_list] displayed as a grid (you can select how many columns to display).
2. Ads list [adverts_list] displayed as a list.
3. Ad details page (compatible with all popular SEO plugins to boost your rankings).
4. Top Categories [adverts_categories show="top"] icons are configurable from wp-admin / Classifieds / Categories panel.
5. All Categories [adverts_categories].
6. Post an Ad Form [adverts_add] (allow anonymous and/or registered users to post ads).
7. Ads list in wp-admin panel.
8. Ad edit page in wp-admin panel.
9. Category edition with icon select.
10. Options, modules and premium extensions.
11. Payment history (if you are planning to charge users for posting Ads)
12. Payment details

== Upgrade Notice ==

= 1.0 - 2015-08-11 =

* Just try it, you will like it.
