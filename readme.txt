=== TomS Social Login ===
Contributors: Tom Sneddon
Tags: Social login, login, account, profile, google, facebook, paypal, github, wechat, qq, weibo, dingtalk, register, user, woocommerce, ultimate member, user registration, auto register
Requires at least: 5.8
Requires PHP: 7.4
Tested up to: 6.2
Stable tag: 1.1.0
Author URI: https://toms-caprice.org
Donate link: https://toms-caprice.org/donate
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Support users use their Facebook,Google,Paypal,Github,Wechat,QQ,Weibo,Dingtalk accounts to login your wordpress site. Compatibility Woocommerce, Ultimate Member, User Registration and more popular registration plugins.

== Description ==

TomS Social Login is a simple and Beautiful social login plugin that support users use their Facebook,Google,Paypal,Github,Wechat,QQ,Weibo,Dingtalk accounts to login your wordpress site. Compatibility Woocommerce, Ultimate Member, User Registration and more popular registration plugins.
Never stop the user to login your wordpress site via their social account, even their wordpress account deleted manually by adminstrator, this will re-register the wordpress account automatically when user via their social account login again. Auto register wordpress account via their social account when first login. user no need to manually create the wordpress user account and password.
Easy to use and setup simply.

= Features =

= Currently Supported Social Login: =
* Facebook
* Google
* Paypal
* Github
* Wechat
* QQ
* Weibo
* Dingtalk

If you need other Social login for your wordpress site let me know.

= Supported Third party user registration Plugins =

* <a href="https://wordpress.org/plugins/woocommerce/" target="_blank" >**Woocommerce**</a>
* <a href="https://wordpress.org/plugins/ultimate-member/" target="_blank" >**Ultimate Member**</a>
* <a href="https://wordpress.org/plugins/user-registration/" target="_blank" >**User Registration**</a>

If you need to support other plugins let me know.


= Order settings =
* You can drag and drop the icon to change the order.

= Button Style settings =
* Default we provided 5 button styles:
* **Square**
* **Circle**
* **Rectangle**
* **Rounded Rectangle**
* **Qt Style Button**

= Configure settings =
You need the below options:
* **App ID (Some app calls it: Client ID or App key)**
* **App Secret (Some app calls it: Client secret or Secret)**
You can customize the call back url.(Default is your home url if you leave it blank.)
* **Valid OAuth Redirect URI (Some app calls it: Authorized Redirect UR or Return URL or Authorization Callback URL or Authorization Callback Domain etc)**

We will make the tutorial that help you to get the above values step by step. More details see: https://toms-caprice.org/docs/toms-social-login

= Translation ready =

== Frequently Asked Questions ==

= Where to redirect after user login via their social account ? =
Default is redirect to the active page. But if the page is wordpress default login page(http://example.com/wp-login.php), will redirect to the home page.

= Why the social button not show ? =
Go to **Admin Panel** -> **TomS WP** -> **TomS Social Login** -> **Configuration** -> check the checkbox of the **Social** you want to show and click **Save Change**.

= How to add the login button to custom page or field ? =
You can put this shortcode **[TSSL_Login_Button]** to your custom page or field, this shortcode only show for no login user.

= How to add the Binding button to custom page or field ? =
You can put this shortcode **[TSSL_Binding_Button]** to your custom page or field, this shortcode only show for logged in user.

= How to use social account profile picture ? =
Go to **Admin Panel** -> **Settings** -> **Discussion** -> **Default Avatar** -> select the **TomS Social Login Avatar** and click **Save Change**.

== Changelog ==

The <a href="https://toms-caprice.org/changelog">Changelog</a> is the best place to learn in more detail about any important changes.

= 1.1.0 - 01/April/2023 =

* NEW: Added new items in setting page to control binding/unbind button show on user profile page.
* Tweak: WordPress 6.2 compatibility
* FIX: setting page need to refresh the page to show the new result when click the "Save changes" button.

= 1.0.0 - 18/October/2022 =

* Initial release

Older changes are found in the <a href="https://plugins.svn.wordpress.org/toms-social-login/trunk/changelog.txt">changelog.txt</a> file in the plugin directory.

== Screenshots ==

1. TomS Social Login settings page

2. TomS Social login Binding button in Wordpress user profile page

3. Square style in Wordpress default login form

4. Circle style in Wordpress default login form

5. Rectangle style in Wordpress default login form

6. Rounded Rectangle style in Wordpress default login form

7. Qt style in Wordpress default login form

8. TomS Social login Binding button in Woocommerce my account page

9. Square style in Woocommerce login form

10. Circle style in Woocommerce login form

11. Rectangle style Woocommerce login form

12. Rounded Rectangle style in Woocommerce login form

13. Qt style in Woocommerce login form

14. TomS Social login Binding button in Ultimate Member account tab

15. Square style in Ultimate Member login form

16. Circle style in Ultimate Member login form

17. Rectangle style Ultimate Member login form

18. Rounded Rectangle style in Ultimate Member login form

19. Qt style in Ultimate Member login form

20. TomS Social login Binding button in User Registration dashboard of my account page 

21. Square style in User Registration login form

22. Circle style in User Registration login form

23. Rectangle style User Registration login form

24. Rounded Rectangle style in User Registration login form

25. Qt style in User Registration login form

26. TomS Social Login avatar setting

== Translations ==

Reliance upon any non-English translation is at your own risk; We can give no guarantees that translations from the original English are accurate.

We recognise and thank those mentioned at https://toms-caprice.org/translations for code and/or libraries used and/or modified under the terms of their open source licences.

== Upgrade Notice ==
* a recommended update for all.
