=== Plugin Test Drive ===
Contributors: Omer Greenwald
Tags: Plugin Testing, Plugin Tester, Plugin Test Drive, test, management, plugin, plugin management, plugins, admin, conditional
Requires at least: 3.0
Tested up to: 4.5.3
Stable tag: 1.3

Test any wordpress plugin before activating it.

== Description ==
Plugin Test Drive lets you take any plugin for a test drive, see if suits your needs before letting your site visitors experience it.

Set options for any plugin to see how it works for you before activating it.

* Test plugin functionality in both admin dashboard and the front end site.

* Change options and style of plugins until they blend perfectly in your site/blog.

* Compare different plugins to find out which one best suits your needs.

* Once you are done testing and the plugin is ready to be up and running, you can then activate it.

= How Does it work? = 

* Plugin Test Drive lets you load wordpress plugins conditionally by user name or IP address. While the tested plugins are visible and fully functional for you, they are invisible to others.

* Testing plugins is safe. When you select a plugin for testing, PTD validates it the same way WordPress validates a plugin when you activate it. If the plugin fails the validation test, PTD will let you know about it and will not test that plugin.

= Languages =

* English

* Italian by [Guter](http://www.inspiratio.it/)

= New Feature =
Since version 1.1 of Plugin Test Drive, you can test a plugin, right after installing it. Refer to the Screenshots.

== Frequently Asked Questions ==
= What does this plugin do? =
This plugin lets you load other plugins conditionally by user name or IP address.
= Which version is required to run this plugin? =
This plugin will work on wp 3.0 and above only.
= Which tester key should I choose, by IP or by user name? =
You may pick whichever you like. The difference is that some plugins are intended to work for unregistered visitors only.
In these cases, "by IP" is more suitable. in other cases, "by user name" may be more convenient.
= What happens to the tested plugins once I deactivate PTD? =
They return to being inactive as before.
= Can I test a plugin while it's active =
No. first deactivate it.
= Why can't I activate/edit/update/delete plugins that are in testing mode =
To ensure safe transition from and to testing mode, these actions are disabled for tested plugins.
To enable these actions again, simply stop testing the plugin.
= What else should I know before using this plugin? =
For plugins that enable typing a function call directly in your theme files, make sure to include "if_function_exists()":

* WRONG - some_plugin_function();
* RIGHT - if (function_exists(' some_plugin_function ')) { some_plugin_function(); }

== Installation ==
1. Either copy the plugin folder to your wp-content/plugins/ folder or install it via "Plugins"->"Add New" menu.
2. Activate Plugin Test Drive from the plugins screen.
3. Go to Setting -> Plugin Test Drive to select plugins for testing.

== Screenshots ==

1. Screenshot-1: PTD options page.
2. Screenshot-2: Plugins in testing mode in plugins screen.
2. Screenshot-3: Testing a plugin directly after installing it.


== Changelog ==

= 1.0.1 =
some changes in readme.txt

= 1.0.2 =
Fix to permission restriction problem

= 1.0.3 =
* Fix to disable submit button bug.
* clear tested plugins list only if ptd is de/activated by the user and not by auto upgrade process

= 1.0.4 =
* Added "untestable" flag.
* Remove plugin action links in plugin page for wp ver < 3.0
* Italian translation added.

= 1.0.5 =
* upload to server of 1.0.4 was halted

= 1.1 =
* Added ability to test a plugin right after installing it

= 1.1.1 =
* make sure a plugin was installed properly before displaying "Test Plugin" link after installing that plugin.

= 1.1.2 =
* compatibility for the new wp version - 3.0.2 is verified.
* added predefined untestable plugins list (2 plugins were added).
* added wp_localize in order to pass parameters to javascript.
* disabled option to select tested plugins for bulk actions.

= 1.1.3 =
* compatibility for the new wp version - 3.0.4 is verified.
* fix to bug the occurs when multiple plugins are selected for testing and one of them is detected as untestable

= 1.1.4 =
* compatibility for the new wp version - 3.1 is verified.
* design adjustments in plugins page for the new version

= 1.2 =
* For years after last update :)
* removed compatibility for wp below 3.0
* various UI bugs fixed to match wp newer versions up to 3.9.1

= 1.3 =
* various UI bugs fixed to match wp newer versions up to 4.5.3