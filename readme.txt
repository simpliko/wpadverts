=== WPAdverts - Classifieds Plugin ===
Plugin URI: http://wpadverts.com/
Contributors: gwin
Tags: classifieds, classified, classified ads, classified script, classifieds script, wp classified, wp classifieds
Requires at least: 4.0
Tested up to: 4.6.0
Stable tag: 1.0.9
License: GNU Version 2 or Any Later Version

Build classifieds section in seconds. Allow your visitors to browse and post (paid or free) classified ads on your site.

== Description ==

WPAdverts is a lite weight plugin which allows to build beatufiul classifieds site in minutes. Plugin will work with ANY properly
coded WordPress theme and you can use it on new or existing site.

When building Adverts we are focusing on ease of use, WordPress compatibility and extensibility, the plugin core is small but this
is NOT a demo, this is a fully functional classifieds software with most crucial functionalities builtin.

**Links**

* [WordPress Classifieds Plugin](https://wpadverts.com/) - official site.
* [View User Demo](https://demo.wpadverts.com/) - see plugin frontend.
* [View Admin Demo](https://demo.wpadverts.com/wp-admin/) - see wp-admin panel.
* [Documentation](https://wpadverts.com/documentation/) - mainly for users.
* [Code Snippets](https://github.com/simpliko/wpadverts-snippets) - for programmers and developers.

**Noatble Features**

* Modern, responsive design.
* Intuitive ads browsing and searching.
* Display categories grid.
* Allow (registered and/or anonymous) users to post Ads.
* Payments Module to track user payments and transactions logs.
* Bank Transfer payment gateway included.
* Easy to use drag and drop image upload.
* Ads will automatically expire after set number of days.
* Detailed user and developer documenation.

See [Screenshots](https://wordpress.org/plugins/wpadverts/screenshots/) tab for full, visual features list.

**Extensions**

WPAdverts plugin can be extended with premium add-ons. Currently we have a couple of modules available:

* [WooCommerce Integration](https://wpadverts.com/extensions/woocommerce-integration/)
* [BuddyPress Integration](https://wpadverts.com/extensions/buddypress-integration/)
* [Maps and Locations](https://wpadverts.com/extensions/maps-and-locations/)
* [PayPal Payments Standard](https://wpadverts.com/extensions/paypal-payments-standard/)
* [Restricted Categories](https://wpadverts.com/extensions/restricted-categories/)

See all https://wpadverts.com/extensions/

**Available Translations**

* French
* German
* Russian
* Slovenian

**Get Involved**

* Wording - I am not English native speaker, if you find any typo, grammer mistake or etc. please report it on support forum.
* Translation - If you translated Adverts to your language feel free to submit translation.
* Rate Adverts - If you find this plugin useful please leave [positive review](https://wordpress.org/support/view/plugin-reviews/wpadverts).
* Submit a Bug - If you find some issue please [submit a bug](https://github.com/gwin/wpadverts/issues/new) on GitHub.

== Installation ==

1. Activate the plugin
2. On activation plugin will create two Pages (in wp-admin / Pages panel)  with [adverts_list] and [adverts_add] shortcodes .
3. Go to Classifieds / Options panel and configure the options.
3. For more detailed instructions visit plugin [documentation](https://wpadverts.com/documentation/)

== Frequently Asked Questions ==

= I have a problem what now? =

Please describe your issue and submit ticket on plugin support forum, you should receive reply within 24 hours (except Sunday).

= Ads pages are showing 404 error? =

Most likely rewrite rules were not registered properly for some reason. Go to wp-admin / Settings / Permalinks panel and click
"Save Changes" button without actually changing anything, this should reset router and fix URLs.

== Changelog ==

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
