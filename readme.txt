=== Plugin Name ===
Contributors: khaledsaikat
Donate link: http://khaledsaikat.com/donate-now/
Tags: user, profile, frontend, user-profile, users, usermeta, import, csv, ajax, admin, plugin
Requires at least: 3.0.0
Tested up to: 3.3.1
Stable tag: 1.0.5

Frontend user profile with extra fields.

== Description ==

<p>Frontend user profile with extra fields. Show all WordPress user fields in frontend. Beside default fields, various types of fields are available. </p>

<p>Supported default fields: Username, Email, Password, Website, Display Name, Nick Name, First Name, Last Name, Description, Registration Date, Role, Jabber, Aim, Yim, Avatar.</p>

<p>Supported extra fields type: TextBox, Paragraph, Rich Text, Hidden Field, Checkbox, Dropdown, Radio. </p>

<p>You can create unlimited number of fields. All newly created field's data will save to WordPress default usermeta table. so you can retrieve all user data by calling wordpress default functions(e.g. get_userdata(), get_user_meta() ). User Meta plugin separates fields and forms. So, a single field can be used among several forms. </p>

<div class="inside">
            <h4>3 steps to getting started</h4><p><b>Step 1. </b>Create Field from User Meta >> Fields.</p><p><b>Step 2. </b>Go to User Meta >> Forms. Drag and drop fields from right to left and save the form.</p><p><b>Step 3. </b>write shortcode to your page or post. Shortcode: [user-meta type='profile' form='profile']</p><p></p><li>You can use type='none' for hide update button.</li><li>You can create more than one form. Use form name in shortcode. e.g. [user-meta type='profile' form='your_form_name']</li><li>Admin user can see all others frontend profile from User Administration screen. To enable this feature, go to User Meta >> User Meta, select profile page from Profile Page Selection and enable right sided checkbox.</li><li>In Case of extra field, you need to define unique meta_key. That meta_key will be use to save extra data in usermeta table. Without defining meta_key, extra data won't save.</li>
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
* Added new fields with new look and feel also functionality.