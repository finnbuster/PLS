=== Pazzey's Store Locator ===
Contributors: crispuno
Donate link: http://www.crispuno.ca/?page_id=306
Tags: store locator, google maps, embed google maps, google maps store locator
Requires at least: 3.3.1
Tested up to: 3.4.1
Stable tag: 1.2
License: GPLv3 or later
License URI: http://www.gnu.org/copyleft/gpl.html

Store Locator Plugin that lets you embed a Google Maps powered store locator.

== Description ==

This is a Store Locator plugin for WordPress that incorporates Google Maps. 

What it can do:

*	Adds a custom post type called Stores for input of store locations
*	Automatically retrieves Google Maps coordinates of location
*	Lets you add a store locator in any page/post using a shortcode
*	Search by entering city and state/province, zip/postal code or part of an address
*	Shows results on right ordered by distance

More information and a demo can be found at [my site](http://www.crispuno.ca/?p=250).

== Installation ==

1. Download and unzip the file
2. Upload the pazzeys-store-locator folder in your WordPress plugins folder
3. Log in to WordPress and go to Plugins
4. Click on the Activate link for Pazzey's Store locator and you’re good to go!

== Frequently Asked Questions ==

= How do I embed the Store Locator? =

Use the new post type 'Stores' to enter the information about your store locations. Once all Store Locations are entered, type in [storelocator] in your Post or Page to embed it.

= What do I enter for the Google Coordinates? =

No need to worry about that! Just save the new Store location and it will automatically populate.

= What if it doesn't populate? =

Check the address you entered, it may have a misspelled street name or the State/Province, City and Country may not have been entered.
If your address has a Suite or Apt number at the beginning, try to type in after the street address instead.
For example 'Suite 123 45 Maple St.' can be typed as '45 Maple St. Suite 123' instead.

= Can I change the width and height of the Store Locator? =

Yes, you can do so within the shortcode - [storelocator width="650" height="700"]

= Can I style the Store Locator? =

Right now, you can do so by using the style.css file in the plugin's files folder. 

== Screenshots ==

1. Store Locator custom post type input page.
2. Shortcode usage.
3. Embedded Store Locator.

== Changelog ==

= 1.2 =
* Fixed issue with HTML tags showing in the link for Google Maps
* Changed bloginfo(url) to bloginfo(wpurl) for non-root WP installations

= 1.1 =
* Fixed shortcode URL bug

= 1.0 =
* Initial release

== Upgrade Notice ==

= 1.2 =
Fixed issue with HTML tags showing in the link for Google Maps
Fixed issue with plugin not appearing for non-root WP installations

= 1.1 =
Fixed issue with URL not found when embedded

= 1.0 =
First release
