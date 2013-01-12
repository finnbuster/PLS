=== simple-login ===
Tags: widget, login
Requires at least: 2.7
Tested up to: 3.1.2
Stable tag: 1.2.1

A simple widget that only contains the Login/Logout and the Register/Site Admin links, with customisable
redirect after login.

== Description ==

This widget was made to replace the built-in meta widget, if sidebar space is precious. Also, one can
select where to redirect the users after a successful login: the admin panel, the blog's main site,
the currently visited site where the login link was activated, or any custom field.

Might work with older versions that has widget support.

== Installation ==

1. Upload `simple_login.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Activate the Widget through the Appearance/Widgets menu

== FAQ ==

Q: What I’d like is to be able to change the “Login” and “Register” link it creates on de sidebar to my own language . Is there any way I can do this?

A: Since the widget uses the wp_register() and the wp_loginout() system calls, you need to change the language of your whole blog. Try this link: http://codex.wordpress.org/WordPress_in_Your_Language


Q: The widget does not say Register, only Login.

A: Go to the dashboard -> Settings -> General, and turn "Anyone can register" on.

== Changelog ==

= 1.2.1 =
* I'm still trying to figure out how not to mess up versioning.

= 1.2 =
* Added redirection options.

= 1.1 =
* Fixed the extra li tag caused by wp_register()

