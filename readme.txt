=== Plugin Name ===
Contributors: khaledsaikat
Donate link: http://khaledsaikat.com/donate-now/
Tags: plugin, admin, ajax, user, usermeta, users, import, csv, profile, user profile, frontend
Requires at least: 3.0.0
Tested up to: 3.3
Stable tag: 1.0.5

Frontend user profile with extra fields.

== Description ==

This plugin, import all data to user and usermeta table, instead of creating custom database table, so you can retrieve all user data by calling wordpress default function.

<p>
3 steps to getting started

Step 1. Create Field from User Meta >> Fields.

Step 2. Go to User Meta >> Forms. Drag and drop fields from right to left and save the form.

Step 3. write shortcode to your page or post. Shortcode: [user-meta type='profile' form='profile']

You may use type='none' for hide update button.

You may create more then one form. Use form name in shortcode. e.g. [user-meta type='profile' form='your_form_name']

Admin user can see all others frontend profile from User Administration screen. To enable this feature, go to User Meta >> User Meta, select profile page from Profile Page Selection and enable right sided checkbox.

In Case of extra field, you need to define unique meta_key. That meta_key will be use to save extra data in usermeta table. Without defining meta_key, extra data won't save.
</p>


== Installation ==

1. Upload and extract `user-meta.zip` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress


== Screenshots ==

1. Fields Editor
2. Forms Field selector
3. Frontend Profile

== Changelog ==

= 1.0.5 =
* Changing complete structure
* Make Seperation of fields and form, so one field can be use in many form
* Add verious type of fields
* Added dragable fields to form
* Improve frontend profile

= 1.0.3 =
* Extend Import Functionality
* Draggable Meta Field
* Add Donation Button

= 1.0.2 =
* Optimize code using php class.
* add [user-meta-profile] shortcode support.

= 1.0.1 =
* Some Bug Free.

= 1.0 =
* First version.
