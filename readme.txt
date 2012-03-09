=== Plugin Name ===
Contributors: khaledsaikat
Donate link: http://user-meta.com/donation
Tags: user, profile, registration, login, frontend, users, usermeta, import, csv, upload, AJAX, admin, plugin, page, image, images, photo, picture, file, email, shortcode, captcha, avatar, redirect, register, password, custom, csv, import, user import
Requires at least: 3.0.0
Tested up to: 3.3.1
Stable tag: 1.1.1

WordPress user management plugin. Custom user profile, custom registration with extra fields. User login by username or email. Import user from csv.

== Description ==

= WordPress user management plugin =

Support custom user profile, custom registration with extra fields. User login by username or email. Import user from csv with meta data.
Themes the WordPress profile, register and login pages according to your theme. Add extra fields(meta data) to user profile or registration page, User Meta plugin support variety of fields to create profile or registration form.

= Custom user profile and registration page =

User Meta Pro allow to fully customize user profile or registration page by providing form editor tools. any page or post can be use as profile/registration page by using shortcode. support ajax user avatar, ajax file upload, and ajax input validation, captcha validation, pagination for break long page to paginated page. Let user login with or without username or email.

= Login with username or email =

Let user to login by email instead of username. Or both email or username. Redirected them to certan page after login.

= Import user from csv =

Import user from csv with extra meta data. Assign role to newly imported user. Update current user data by csv file.

= Supported field for form builder =

* User Avatar
* TextBox
* Paragraph
* Rich Text
* Hidden Field
* DropDown
* CheckBox
* Select One (radio)
* Date /Time
* Password
* Email
* File Upload
* Image Url
* Phone Number
* Number
* Website
* Country

* Page Heading
* Section Heading
* HTML
* Captcha

<p>You can create unlimited number of fields. All newly created field's data will save to WordPress default usermeta table. so you can retrieve all user data by calling wordpress default functions(e.g. get_userdata(), get_user_meta() ). User Meta plugin separates fields and forms. So, a single field can be used among several forms. </p>

= Documentation =

**3 steps to getting started**
1. Create Field from User Meta >> Fields Editor.
1. Go to User Meta >> Forms Editor, Give a name to your form. Drag and drop fields from right to left and save the form.
1. Write shortcode to your page or post. e.g.: Shortcode: [user-meta type='profile' form='your_form_name']

[View Documentation](http://user-meta.com/documentation/ "View Documentation")

**NB** Registration, login and some extra fields are only supported in pro version.
Get [User Meta Pro](http://user-meta.com/ "User Meta Pro").


== Installation ==

1. Upload and extract `user-meta.zip` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Why error message, "User registration is currently not allowed." is showing in registration page? =

WordPress doesn't allow to register new user by default settings. To allow user to register, go to Settings >> General page in admin section. Checked the checkbox which is saying "Anyone can register" and Save Changes.


== Screenshots ==

1. Fields Editor
2. Forms Field selector
3. Frontend Profile
4. Frontend Login

== Changelog ==

= 1.1.1 =
* Added Support while fail AJAX call.

= 1.1.0 =
* Include first version of User Meta Pro
* Pro: added more fields type
* Pro: Frontend Registration
* Pro: Frontend Login

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

= 1.1.1 =
* Added Support while fail AJAX call.

= 1.1.0 =
* Introduce with User Meta Pro.

= 1.0.5 =
* Added new fields with new look and feel also functionality.