<?php

/**
 * WoW latest Guild Achievements Module
 *
 * @author     Branko Wilhelm <bw@z-index.net>
 * @link       http://www.z-index.net
 * @copyright  (c) 2011 - 2013 Branko Wilhelm
 * @package    mod_wow_latest_guild_achievements
 * @license    GNU General Public License v3
 * @version    $Id$
 */

defined('_JEXEC') or die;

jimport('joomla.http.http');

abstract class mod_wow_latest_guild_achievements {
	
    public static function _(JRegistry &$params) {

        if (!$params->get('lang') || !$params->get('realm') || !$params->get('guild')) {
            return 'please configure Module - ' . __CLASS__;
        }

        $url = 'http://' . $params->get('region') . '.battle.net/wow/' . $params->get('lang') . '/guild/' . $params->get('realm') . '/' . $params->get('guild') . '/achievement';
        
        $cache = JFactory::getCache(__CLASS__, 'output');
        $cache->setCaching(1);
        $cache->setLifeTime($params->get('cache_time', 60) * 60);
         
        $key = md5($url);
         
        if(!$result = $cache->get($key)) {
        	$http = new JHttp;
        	$http->setOption('userAgent', 'Joomla! ' . JVERSION . '; WoW latest Guild Achievements Module; php/' . phpversion());

        	try {
        		$result = $http->get($url, null, $params->get('timeout', 10));
        	}catch(Exception $e) {
        		return $e->getMessage();
        	}
        	
        	$cache->store($result, $key);
        }
         
        if($result->code != 200 || !strpos($result->body, '<div class="achievements-recent')) {
        	return __CLASS__ . ' HTTP-Status ' . JHTML::_('link', 'http://wikipedia.org/wiki/List_of_HTTP_status_codes#'.$result->code, $result->code, array('target' => '_blank'));
        }

        // get only achievement data
        preg_match('#<div class="achievements-recent.*">(.*)<div id="achievement-list"#Uis', $result->body, $result->body);
        
        $result->body = $result->body[1];

        // find all achievements
        preg_match_all('#<a.*href="achievement\#([0-9:]+):a(\d+)".*>.*background-image: url\("(.*)"\);.*<strong class="title">(.*)</strong>#Uis', $result->body, $matches, PREG_SET_ORDER);
        
        if(empty($matches)) {
        	return __CLASS__ . 'no achievements found?!';
        }
        
        $achievements = array();
        foreach($matches as $key => $match) {
            $achievements[$key] = new stdClass;
            $achievements[$key]->name = $match[4];
            $achievements[$key]->image = $match[3];
            $achievements[$key]->id = $match[2];
            $achievements[$key]->link = $url . '#' . $match[1] . ':' . $match[2];
            $achievements[$key]->link = self::link($achievements[$key], $params);
       }
        
        return $achievements;
    }
    
    private static function link(stdClass $achievement, JRegistry &$params) {
    	$sites['battle.net'] = $achievement->link;
    	$sites['wowhead.com'] = 'http://' . $params->get('lang') . '.wowhead.com/achievement=' . $achievement->id;
    	$sites['wowdb.com'] = 'http://www.wowdb.com/achievements/' . $achievement->id;
    	$sites['buffed.de'] = 'http://wowdata.buffed.de/?a=' . $achievement->id;
    	return $sites[$params->get('link')];
    }
}