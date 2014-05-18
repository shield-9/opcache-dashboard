=== OPcache Dashboard ===
Contributors: extendwings, headonfire,
Donate link: http://www.extendwings.com/donate/
Tags: PHP, Zend, OPcache, monitor, stat, stats, status, server, cache, dashboard
Requires at least: 3.8
Tested up to: 3.9
Stable tag: 0.2.3
License: AGPLv3 or later
License URI: http://www.gnu.org/licenses/agpl.txt

OPcache dashboard designed for WordPress

== Description ==

As you know, OPcache has no management page. This plugins offers you the OPcache dashboard designed for WordPress.

***WARNING***:This is experimental and in development. All files in this repo is licensed under [GNU AFFERO GENERAL PUBLIC LICENSE, Version 3](http://www.gnu.org/licenses/agpl.txt).

### Notice
* **Important**: To use this plugin, check following.
	1. **PHP 5.5 or later**, Did you compile PHP with *--enable-opcache option*?
	2. **PHP 5.4 or earlier**, Did you installed *PECL ZendOpcache*?
	3. If not, please see [this document](http://php.net/book.opcache) and enable/install OPcache.

### Thanks
For implementing this plugin, I referred to [OPcache Dashboard](https://github.com/carlosbuenosvinos/opcache-dashboard)([@buenosvinos](https://twitter.com/buenosvinos))

### License
* Copyright (c) 2012-2014 [Daisuke Takahashi(Extend Wings)](http://www.extendwings.com/)
* Portions (c) 2010-2012 Web Online.
* Unless otherwise stated, all files in this repo is licensed under *GNU AFFERO GENERAL PUBLIC LICENSE, Version 3*. See *LICENSE* file.

#### GNU AFFERO GENERAL PUBLIC LICENSE, Version 3
* agpl.svg
	* Copyright (c) [Free Software Foundation, Inc.](http://www.fsf.org/)
	* Licensed under GNU AFFERO GENERAL PUBLIC LICENSE, Version 3
	* Fetched from [Stickers ? Free Software Foundation ? working together for free software](https://www.fsf.org/resources/stickers)

#### The MIT License
* js/jquery.center.js
* js/jquery.center.min.js
	* Copyright (c) 2011 [Ben Lin](http://dreamerslab.com/)
	* Licensed under [the MIT License](https://raw2.github.com/dreamerslab/jquery.center/72408e8ae31ba533f26c976f8a1baca1912adfa4/LICENSE.txt)
	* Copy license text is also available as *js/jquery.center.license*
	* Fetched from [dreamerslab/jquery.center](https://github.com/dreamerslab/jquery.center)
	* Version: 1.1.1
	* Commit: [72408e8ae31ba533f26c976f8a1baca1912adfa4](https://github.com/dreamerslab/jquery.center/commit/72408e8ae31ba533f26c976f8a1baca1912adfa4)

#### The BSD 3-Clause License
* js/d3.js
* js/d3.min.js
	* Copyright  2014 [Michael Bostock](http://d3js.org/)
	* Licensed under [the BSD 3-Clause License](https://raw2.github.com/mbostock/d3/04fa5dd3856de768b43b4aac9e34c112f1227a17/LICENSE)
	* Copy license text is also available as *js/d3.license*
	* Fetched from [mbostock/d3](https://github.com/mbostock/d3)
	* Version: 3.4.6
	* Commit: [49ba8afebb2ae813ab66dc2f48f533aa7f333c3c](https://github.com/mbostock/d3/commit/49ba8afebb2ae813ab66dc2f48f533aa7f333c3c)

#### Apache License, Version 2.0
* github-btn.html
* css/github-btn.css
	* Copyright (c) 2011 [Mark Otto](http://ghbtns.com/)
	* Licensed under [Apache License, Version 2.0](http://www.apache.org/licenses/LICENSE-2.0)
	* Modified for compatible with HTML5.
	* Portions Copyright (c) 2014 Daisuke Takahashi(Extend Wings)
	* Fetched from [mdo/github-buttons](https://github.com/mdo/github-buttons)
	* Commit: [1d5c41ba98737deb5d0089c3917875ad14099227](https://github.com/mdo/github-buttons/commit/1d5c41ba98737deb5d0089c3917875ad14099227)

== Installation ==

1. Upload the `opcache` folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= This plugin is broken! Thanks for nothing! =
First of all, we supports PHP 5.4+, MySQL 5.5+, WordPress 3.8+. Old software(vulnerable!) is not supported.
If you're in supported environment, please create [pull request](https://github.com/shield-9/opcache-dashboard/compare/) or [issue](https://github.com/shield-9/opcache-dashboard/issues/new).

== Screenshots ==

1. Main Page
2. Status Page

== Changelog ==

= 0.2.3 =
* WordPress Bug Fix: Reset all caches after updating WordPress Core/Plugins/Themes.
* github-buttons is updated.
* More on [GitHub](https://github.com/shield-9/opcache-dashboard/releases/tag/0.2.3).
* [`git diff 0.2.2...0.2.3`](https://github.com/shield-9/opcache-dashboard/compare/0.2.2...0.2.3)

= 0.2.2 =
* Bug Fix: `Invalidate` and `Force Invalidate` now works well.
* Manual Cache Control Page is available. You can compile file without running.
* Compatible with sortable widget UI! Drag, Drop, Collapse, and Expand is supported.
* D3.js is updated. (3.4.2 -> 3.4.6)
* Auto deactivation for older WordPress(~3.7.x). More compatible with MP6 Admin theme!
* More on [GitHub](https://github.com/shield-9/opcache-dashboard/releases/tag/0.2.2).
* [`git diff 0.2.1...0.2.2`](https://github.com/shield-9/opcache-dashboard/compare/0.2.1...0.2.2)

* **Contributor**: [Ihor Vorotnov](http://ihorvorotnov.com/) reported 3 issues!

= 0.2.1 =
* Minor Bug Fix See [GitHub](https://github.com/shield-9/opcache-dashboard/releases/tag/0.2.1).
* [`git diff 0.2.0...0.2.1`](https://github.com/shield-9/opcache-dashboard/compare/0.2.0...0.2.1)

= 0.2.0 =
* Compatible with many kinds of screen, including Smartphone!
* Better Main Dashboard Page
* Now Status, Scripts and Configurations Pages are all available. This is one of the big progress.
* More on [GitHub](https://github.com/shield-9/opcache-dashboard/releases/tag/0.2.0).
* [`git diff 0.1.0...0.2.0`](https://github.com/shield-9/opcache-dashboard/compare/0.1.0...0.2.0)

= 0.1.0 =
* Initial Beta Release

== Upgrade Notice ==

= 0.2.2 =
* None

= 0.2.1 =
* None

= 0.2.0 =
* None

= 0.1.0 =
* None
