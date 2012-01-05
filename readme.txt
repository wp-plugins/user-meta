=== Plugin Name ===
Contributors: khaledsaikat
Donate link: http://khaledsaikat.com/donate-now/
Tags: plugin, admin, ajax, user, usermeta, users, import, csv, profile, user profile, frontend
Requires at least: 3.0.0
Tested up to: 3.3
Stable tag: 1.0.5

Frontend user profile with extra fields.

== Description ==

Frontend user profile with extra fields. Show all WordPress user field in frontend. Beside defaults, various types of fields are available. Use single fields with several form.

This plugin, import all data to user and usermeta table, instead of creating custom database table, so you can retrieve all user data by calling wordpress default function.


<div class="inside">
            <h4>3 steps to getting started</h4><p><b>Step 1. </b>Create Field from User Meta >> Fields.</p><p><b>Step 2. </b>Go to User Meta >> Forms. Drag and drop fields from right to left and save the form.</p><p><b>Step 3. </b>write shortcode to your page or post. Shortcode: [user-meta type='profile' form='profile']</p><p></p><li>You may use type='none' for hide update button.</li><li>You may create more then one form. Use form name in shortcode. e.g. [user-meta type='profile' form='your_form_name']</li><li>Admin user can see all others frontend profile from User Administration screen. To enable this feature, go to User Meta >> User Meta, select profile page from Profile Page Selection and enable right sided checkbox.</li><li>In Case of extra field, you need to define unique meta_key. That meta_key will be use to save extra data in usermeta table. Without defining meta_key, extra data won't save.</li>
            <p></p>
        </div>




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

== Upgrade Notice ==

= 1.0.5 =
* Added new fields with great look and feel and functionality.