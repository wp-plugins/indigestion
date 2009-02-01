=== Indigestion ===
Contributors: evelio
Donate link: http://clearfix.net/labs/indigestion#donate
Tags: automatic, Post, posts, plugin, links, digest, rss, atom, feed, bookmark, bookmarking, bookmarks, content, feeds, link, list, page, pages, plugins, recent, Share, related, social, social bookmarking, url
Requires at least: 2.6
Tested up to: 2.7
Stable tag: 0.1

Generates a daily post digest from many customisable Feed sources.

== Description ==

Generates a daily post digest from many customisable Feed sources. i. e. I used it to generates a daily post with my Google Reader Shared Items, also I've put as public my Starred Items to post it, and finally my public bookmarks on Delicious.

See an [working example at my blog](http://evelio.info/2009/01/31/enlaces-del-31-01-09/ "Indigestion on Action")

Based on [Digest Post by Frederic Wenzel](http://wordpress.org/extend/plugins/digest-post/ "Digest Post on Wordpress.org")

== Installation ==

1. Upload *indigestion.php* to the **/wp-content/plugins/** directory
1. With the Plugins Editor, change the settings on **indigestion_settings function** as you like
1. Activate the plugin through the *Plugins* menu in WordPress
1. Done enjoy Indigestion :)

== Frequently Asked Questions ==

= How to edit settings or options? =

In this version only is allowed editing your *indigestion.php* file directly, is planned add an easy GUI for configure it on WordPress Settings.

= Which options are allowed? =

If you read the embebed documentation you'll understand but...

1. **time**
Time of the day to say Indigestion get feeds and post the digest, on **24h format**.

1. **min**
Number of minimus items total, sum of all Feeds, meaning at least **min** items or links to create a post, if not enought items on total Indigestion will not create the post.

1. **preamble**
Text or code to insert before the list of links.

1. **epilogue**
Text or code to insert after the list of links.

1. **feeds**
Multidimension array wich contains the Feeds info (each array is an Feed), meaning 

* **title** if you don't wan't a title before each Feed's link list, just put it as '' or null if you don't want it.
* **url** Obviously the Feed's URL, mandatory!.
* **options** An array containing the individual Feed options, allowed options are:
	* *disable_creator*, boolean, disable the insertion of "by [author]" after item link
	* *disable_annotation_content*, boolean, disable the insertion of Google Reader's Annotation if apply.

== Screenshots ==

1. An Indigestion post digest on my blog.

== Website ==

[Indigestion Website on Clearfix.net](http://clearfix.net/labs/indigestion "Indigestion Website")

== Licence ==

Copyright 2009 Evelio Tarazona Cáceres <evelio@evelio.info>

Based on Digest Post by Frederic Wenzel at http://wordpress.org/extend/plugins/digest-post/

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
