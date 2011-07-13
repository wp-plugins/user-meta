=== Plugin Name ===
Contributors: khaledsaikat
Tags: user, usermeta, users, import, csv
Requires at least: 3.0.0
Tested up to: 3.2
Stable tag: 1.0.0

Allow you to import user data from CSV file, including custom usermeta fields and you can add extra field to user profile page

== Description ==

This plugin allow two options:
1. Importing user from CSV file with castom meta data.
2. Add extra field to user profile page.

User field like username, emial etc can be defined
or any other custom field can also use, This plugin will check the user in wordpress system by username or email, if no match found then new user will be created.
If username or password already exists, then you can skip that user or also can overwrite existing user data.

This plugin, import all data to user and usermeta table, instead of creating custom database table, so you can retrieve all user data by calling wordpress default function.


== Installation ==

1. Upload and extract `user-meta.zip` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. You will see a new 'User Import' option under the existing 'Users' menu area.


== Changelog ==

= 1.0 =
* First version
