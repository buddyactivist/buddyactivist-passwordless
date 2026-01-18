=== BuddyActivist Passwordless Registration & Login ===
Contributors: BuddyActivist
Tags: passwordless, registration, login, magic link, buddypress, buddyboss, payment, shortcode, cron
Requires at least: 5.8
Tested up to: 6.7
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A modern, secure, passwordless registration and login system with optional payment step, automatic page creation, BuddyPress/BuddyBoss integration, avatar validation, and automatic cleanup of unpaid users.

== Description ==

BuddyActivist Passwordless is a complete passwordless onboarding system designed for community platforms, civic networks, and membership-based ecosystems.

It replaces the traditional username/password flow with a **magic link** sent via email, ensuring a frictionless and secure user experience.

The plugin automatically creates all required pages:

- **Registration** (email → magic link)
- **Registration Completion** (xProfile fields + avatar)
- **Login** (email → magic link)
- **Registration Completed** (optional, created only if a payment shortcode is configured)

If a payment shortcode is added in the plugin settings, the plugin automatically creates the **Registration Completed** page and adds:

- A 60-minute countdown timer
- Automatic redirect after successful payment
- Automatic cleanup of unpaid users via cron

If no payment shortcode is configured, the registration flow ends directly on the **Registration Completion** page and the user is redirected to their BuddyPress/BuddyBoss profile.

### Key Features

- **Passwordless registration and login**
- **Magic link authentication**
- **Automatic page creation**
- **Optional payment step**
- **Dynamic flow based on admin settings**
- **Avatar validation (size, format, dimensions)**
- **BuddyPress/BuddyBoss profile integration**
- **Automatic cleanup of unpaid users (cron)**
- **Fallback message if payment fails**
- **Fully translatable (.pot included)**

### Perfect for:

- Membership communities  
- Civic platforms  
- Associations and cooperatives  
- Paid onboarding flows  
- BuddyPress/BuddyBoss networks  
- Platforms requiring frictionless onboarding  

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/`
2. Activate the plugin through the “Plugins” menu in WordPress
3. Go to **Settings → BuddyActivist Passwordless**
4. (Optional) Insert a payment shortcode  
   - If provided, the plugin will automatically create the **Registration Completed** page
5. Add the registration and login pages to your menus if needed

The plugin is ready to use.

== Frequently Asked Questions ==

= Does the plugin require BuddyPress or BuddyBoss? =
No, but it integrates perfectly with both.  
If BuddyPress/BuddyBoss is active, users are redirected to their profile after registration.

= How does the payment step work? =
If you insert a payment shortcode in the settings, the plugin automatically creates the **Registration Completed** page and adds:
- a 60-minute timer  
- automatic redirect after payment  
- automatic cleanup of unpaid users  

If no shortcode is provided, the payment step is skipped entirely.

= What happens if the payment fails? =
The user is shown a clear fallback message:
“The payment was not successful. Please try again.”

= What happens if the user does not complete the payment? =
A cron job runs every 15 minutes and deletes users who:
- started the payment process  
- did not complete it within 60 minutes  

= Can I customize the pages? =
Yes. All pages are standard WordPress pages and can be edited normally.

= Is the plugin translatable? =
Yes. A `.pot` file is included and the plugin is fully internationalized.

== Screenshots ==

1. Registration form (email → magic link)
2. Registration completion (profile fields + avatar)
3. Payment page with countdown timer
4. Admin settings page (payment shortcode)

== Changelog ==

= 1.0.0 =
* Initial release
* Passwordless registration and login
* Magic link system
* Automatic page creation
* Optional payment step
* Countdown timer
* Automatic cleanup of unpaid users (cron)
* Fallback message for failed payments
* BuddyPress/BuddyBoss integration
* Avatar validation
* Full internationalization

== Upgrade Notice ==

= 1.0.0 =
First stable release.
