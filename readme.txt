=== OPcache Dashboard ===
Contributors: extendwings,
Donate link: http://www.extendwings.com/donate/
Tags: PHP, Zend, OPcache, monitor, stat, stats, status, server, cache, dashboard
Requires at least: 3.8
Tested up to: 3.9-beta1-27501
Stable tag: 0.2.1
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
* Copyright (c) 2012-2014 [Daisuke Takahashi(Extend Wings)](http://www.extendwings.com/)
* Portions (c) 2010-2012 Web Online.
* Unless otherwise stated, all files in this repo is licensed under *GNU AFFERO GENERAL PUBLIC LICENSE, Version 3*. See *LICENSE* file.

### GNU AFFERO GENERAL PUBLIC LICENSE, Version 3
* agpl.svg
	* Copyright (c) [Free Software Foundation, Inc.](http://www.fsf.org/)
	* Licensed under GNU AFFERO GENERAL PUBLIC LICENSE, Version 3

### The MIT License
* js/jquery.center.js
* js/jquery.center.min.js
	* Copyright (c) 2011 [Ben Lin](http://dreamerslab.com/)
	* Licensed under [the MIT License](https://raw2.github.com/dreamerslab/jquery.center/72408e8ae31ba533f26c976f8a1baca1912adfa4/LICENSE.txt)
	* Copy license text is also available as *js/jquery.center.license*

### The BSD 3-Clause License
* js/d3.js
* js/d3.min.js
	* Copyright  2014 [Michael Bostock](http://d3js.org/)
	* Licensed under [the BSD 3-Clause License](https://raw2.github.com/mbostock/d3/04fa5dd3856de768b43b4aac9e34c112f1227a17/LICENSE)
	* Copy license text is also available as *js/d3.license*

### Apache License, Version 2.0
* github-btn.html
* css/github-btn.css
	* Copyright (c) 2011 [Mark Otto](http://ghbtns.com/)
	* Licensed under [Apache License, Version 2.0](http://www.apache.org/licenses/LICENSE-2.0)
	* Modified for compatible with HTML5.
	* Portions Copyright (c) 2014 Daisuke Takahashi(Extend Wings)

== Installation ==

1. Upload the `opcache` folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= There is nothing. =

== Screenshots ==

1. Main Page
2. Status Page

== Changelog ==

= 0.2.1 =
* Minor Bug Fix See [GitHub](https://github.com/shield-9/opcache-dashboard).

= 0.2.0 =
* Compatible with many kinds of screen, including Smartphone!
* Better Main Dashboard Page
* Now Status, Scripts and Configurations Pages are all available. This is one of the big progress.
* There is more improvements. See [GitHub](https://github.com/shield-9/opcache-dashboard).

= 0.1.0 =
* Initial Beta Release

== Upgrade Notice ==

= 0.2.0 =
* None

= 0.1.0 =
* None
