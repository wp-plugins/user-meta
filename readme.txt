=== Plugin Name ===
Contributors: khaledsaikat
Tags: user, usermeta, users, import, csv, profile, user profile
Requires at least: 3.0.0
Tested up to: 3.2.1
Stable tag: 1.0.3

Allow import user data from CSV file, including custom usermeta fields. Add extra field to user profile page. Pprovide shortcode for frontend profile management

== Description ==
<p>
This plugin allow three options:
1. Importing user from CSV file with custom meta data.
2. Add extra field to user profile page.
3. Provide [user-meta-profile] shortcode for frontend profile update
</p>

User field like username, email etc can be defined
or any other custom field can also use, This plugin will check the user in wordpress system by username or email, if no match found then new user will be created.
If username or password already exists, then you can skip that user or also can overwrite existing user data.

This plugin, import all data to user and usermeta table, instead of creating custom database table, so you can retrieve all user data by calling wordpress default function.

<p>
add extra field to backend and frontend profile
After activation you will see two new item under Users menu: User Import, Meta Editor.
Use User Import for importing new or existing users
Use Meta Editor to add extra field in backend or frontend profile, optionally this plugin allow grouping.
Use [user-meta-profile] shortcode in any page or post to show frontend profile.
</p>

== Installation ==

1. Upload and extract `user-meta.zip` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. You will see two new item under Users menu: User Import, Meta Editor.
4. Use User Import for importing new or existing users
5. Use Meta Editor to add extra field in backend or frontend profile, optionally this plugin allow grouping.
6. Use [user-meta-profile] shortcode in any page or post to show frontend profile.


== Changelog ==

= 1.0.2 =
* Optimize code using php class.
* add [user-meta-profile] shortcode support.

= 1.0.1 =
* Some Bug Free.

= 1.0 =
* First version.
