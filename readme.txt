=== OPcache Dashboard ===
Contributors: extendwings,
Donate link: http://www.extendwings.com/donate/
Tags: PHP, Zend, OPcache, monitor, stat, stats, status, server, cache, dashboard
Requires at least: 3.8
Tested up to: 3.9-alpha-27111
Stable tag: 0.1.0
License: AGPLv3 or later
License URI: http://www.gnu.org/licenses/agpl.txt

OPcache dashboard designed for WordPress

== Description ==

As you know, OPcache has no management page. This plugins offers you the OPcache dashboard designed for WordPress.

## Notice
* **Important**: To use this plugin, check following.
	1. **PHP 5.5 or later**, Did you compile PHP with *--enable-opcache option*?
	2. **PHP 5.4 or earlier**, Did you installed *PECL ZendOpcache*?
	3. If not, please see [this document](http://php.net/book.opcache) and enable/install OPcache.

## Thanks
For implementing this plugin, I referred to [OPcache Dashboard](https://github.com/carlosbuenosvinos/opcache-dashboard)([@buenosvinos](https://twitter.com/buenosvinos))

## License

Unless otherwise stated, all files in this repo is licensed under GNU AGPLv3. See "LICENSE" file.

<dl>
	<dt>js/jquery.ceter.js</dt>
	<dt>js/jquery.ceter.min.js</dt>
		<dd>Copyright (c) 2011 [Ben Lin](http://dreamerslab.com/)</dd>
		<dd>Licensed under [the MIT License](https://raw2.github.com/dreamerslab/jquery.center/72408e8ae31ba533f26c976f8a1baca1912adfa4/LICENSE.txt)</dd>
		<dd>Copy license text is also available as *js/jquery.center.license*</dd>
	<dt>js/d3.js</dt>
	<dt>js/d3.min.js</dt>
		<dd>Copyright (c) 2014 Michael Bostock</dd>
		<dd>Licensed under [the BSD License](https://raw2.github.com/mbostock/d3/04fa5dd3856de768b43b4aac9e34c112f1227a17/LICENSE)</dd>
		<dd>Copy license text is also available as *js/d3.license*</dd>
</dl>

== Installation ==

1. Upload the `server-status` folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= There is nothing. =

== Screenshots ==

1. Main page
2. You can add opcode cache

== Changelog ==

= 0.1 =
* Initial Beta Release

== Upgrade Notice ==

= 0.1 =
* None
