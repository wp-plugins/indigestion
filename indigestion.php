<?php
/*
Plugin Name: Indigestion
Plugin URI: http://clearfix.net/labs/indigestion
Description: Generates a daily post digest from many customisable Feed sources.
Version: 0.1
Author: Evelio Tarazona Cáceres
Author URI: http://evelio.info
*/

/*  Copyright 2009 Evelio Tarazona Cáceres <evelio@evelio.info>

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
*/

/*
	TODO: ADD GUI FOR OPTIONS
	START editing your settings HERE
*/
function indigestion_settings($which)
{
	$setting = null;
	switch ($which)
	{
		case 'time':
			// Time of the day to say Indigestion get feeds and post the digest, on 24h format.
			$setting = '00:05';
		break;
		case 'min':
			// Number of minimus items total, sum of all Feeds, meaning at least min items or links to create a post, if not enought items on total Indigestion will not create the post.
			$setting = 5;
		break;
		case 'preamble':
			// Text or code to insert before the list of links.
			$setting = 'Enlaces muchos enlaces: ';
		break;
		case 'epilogue':
			// Text or code to insert after the list of links.
			$setting = 'Eso es todo por hoy :P';
		break;
		case 'feeds':
			/*
			Feeds
			Multidimension array wich contains the Feeds info (each array is an Feed), meaning 

			* title: if you don't wan't a title before each Feed's link list, just put it as '' or null if you don't want it.
			* url: Obviously the Feed's URL, mandatory!.
			* options An array containing the individual Feed options, allowed options are:
				- disable_creator: boolean, disable the insertion of "by [author]" after item link
				- disable_annotation_content: boolean, disable the insertion of Google Reader's Annotation if apply.

			*/
		    $setting = array
			(
				array('title' => 'Evelio`s Google Reader shared items', 'url' => 'http://feeds2.feedburner.com/EvelioGoogleReader'),
				array('title' => 'Evelio`s Google Reader starred items', 'url' => 'http://feeds2.feedburner.com/EvelioGoogleReaderEstrellados'),
				array('title' => 'Evelio`s Delicious saved items', 'url' => 'http://feeds.delicious.com/v2/rss/eveliotc', 'options' => array('disable_creator'=>true))
			);
	        break;
		case 'post':
		default:
			//Post propierties
			$setting = array
			(
				'post_author' => 1, // default: admin
				//'post_date'		=> $post_dt,
				//'post_date_gmt'	=> $post_dt,
				//'post_modified'	=> $post_modified_gmt,
				//'post_modified_gmt'	=> $post_modified_gmt,
				'post_title'	=> 'Links for %s', // %s will be replaced by current date
				//'post_content'	=> $post_content,
				//'post_excerpt'	=> $post_excerpt,
				'post_status'	=> 'publish', // or 'draft' or 'private'
				//'post_name'		=> $post_title,
				'post_type'	 => 'post',  // or 'page'
				//'comment_status'	=> $comment_status_map[$post_open_comment],
				//'ping_status'	=> $comment_status_map[$post_open_tb],
				//'comment_count'	=> $post_nb_comment + $post_nb_trackback,
				'post_category'	 => array(1), // category array
				'tags_input' => 'Links, Internet' //tags
			);
		break;	
	}
	return $setting;
}
/*
	STOP editing HERE
*/

/*
Gets Feed items and remove already posted items
*/
function indigestion_fetch_feed($uri)
{
	$rssdata = fetch_rss($uri);
	// filter out already posted items, if necessary
	if ($lastpost = get_option('indigestion_last'))
	{
		$datefilter = create_function('$item', 'return ('.$lastpost.' < strtotime($item[\'pubdate\']));');
		$rssdata->items = array_filter($rssdata->items, $datefilter);
	}
	return $rssdata;
}

/*
Generates list digest for each feed.
*/
function indigestion_create_digest($rssdata, $options, $title)
{
	$digest = '';
	if($title && !empty($title))
		$digest .= '<h3>' . $title . '</h3>';
	$digest .= '<ul>';
	foreach ($rssdata->items as $item)
	{
		$digest .= '<li><a href="' . $item['link'] .'">' . $item['title'] .'</a>';

		if (!$options['disable_creator'] && $item['dc']['creator'] != '(author unknown)')
			$digest .= ' por <strong>' . $item['dc']['creator'] . '</strong>';
		
		if (!$options['disable_annotation_content'] && $item['gr']['annotation_content'])
			$digest .= '<p>' . $item['gr']['annotation_content'] . '</p>';

		$digest .= '</li>';
	}
	$digest .= '</ul>';
	return $digest;
}

/*
Post it
*/
function indigestion_post_digest($content)
{
	if($content && !empty($content))
	{
		$digest = indigestion_settings('post');
		$digest['post_title'] = sprintf($digest['post_title'], strftime('%d-%m-%y'));
		$digest['post_content'] = $content;
		wp_insert_post($digest);
		update_option('indigestion_last', time());
	}
}

/*
Main function, the action itself.
*/
function indigestion_run()
{
	include_once(ABSPATH . WPINC . '/rss.php');
	$preamble = indigestion_settings('preamble');
	$feeds = indigestion_settings('feeds');
	$count = 0;
	$content = ($preamble && !empty($preamble)) ? '<p>' . $preamble . '</p>' : '';
	foreach ($feeds as $feed)
	{
		$rssdata = indigestion_fetch_feed($feed['url']);
		if ($rssdata && !empty($rssdata->items) && sizeof($rssdata->items) > 0)
		{
			$content .= indigestion_create_digest($rssdata, $feed['options'],$feed['title']);
			$count += sizeof($rssdata->items);
		}
		
	}
	$epilogue = indigestion_settings('epilogue');
	$content .= ($epilogue && !empty($epilogue)) ? '<p>' . $epilogue . '</p>' : '';
	if($count >= indigestion_settings('min'))
		indigestion_post_digest($content);
}

/*
Activation hook
*/
function indigestion_install()
{
	indigestion_log(indigestion_settings('time'));
	$sched_time = explode(':',  indigestion_settings('time'));
	wp_schedule_event(mktime($sched_time[0], $sched_time[1]), 'daily', 'indigestion_run');
	add_option('indigestion_last', null, 'Timestamp of last Indigestion post');
}

/*
Deactivation hook
*/	
function indigestion_uninstall()
{
	remove_action('indigestion_run', 'indigestion_run');
	wp_clear_scheduled_hook('indigestion_run');
	delete_option('indigestion_last');
}

/* Hook register  */
$indigestion_filename = __FILE__;
register_activation_hook($indigestion_filename, 'indigestion_install');
register_deactivation_hook($indigestion_filename, 'indigestion_uninstall');
add_action('indigestion_run', 'indigestion_run');

