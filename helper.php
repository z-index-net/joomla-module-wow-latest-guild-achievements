<?php

/**
 * WoW latest Guild Achievements Module
 *
 * @author     Branko Wilhelm <bw@z-index.net>
 * @link       http://www.z-index.net
 * @copyright  (c) 2011 - 2012 Branko Wilhelm
 * @package    mod_wow_latest_guild_achievements
 * @license    GNU Public License <http://www.gnu.org/licenses/gpl.html>
 * @version    $Id$
 */
// no direct access
defined('_JEXEC') or die;

jimport('joomla.cache.cache');

class mod_wow_latest_guild_achievements {

    public static $overall_points = null;

    public static function onload(&$params) {

        // all required paramters set?
        if (!$params->get('lang') || !$params->get('realm') || !$params->get('guild')) {
            return array('please configure Module' . ' - ' . __CLASS__);
        }

        // if curl installed?
        if (!function_exists('curl_init')) {
            return array('php-curl extension not found');
        }

        $scheme = JURI::getInstance()->getScheme();
        $realm = rawurlencode(strtolower($params->get('realm')));
        $guild = rawurlencode(strtolower($params->get('guild')));
        $lang = strtolower($params->get('lang'));
        $region = strtolower($params->get('region'));
        $wowhead_lang = strtolower($params->get('wowhead_lang'));
        $url = 'http://' . $region . '.battle.net/wow/' . $lang . '/guild/' . $realm . '/' . $guild . '/achievement';

        // wowhead script integration if wanted
        if ($params->get('wowhead') == 'yes') {
            JFactory::getDocument()->addScript($scheme . '://static.wowhead.com/widgets/power.js');
        }

        $cache = & JFactory::getCache(); // get cache obj
        $cache->setCaching(1); // enable cache for this module
        $cache->setLifeTime($params->get('cache_time', 60)); // time to cache

        $result = $cache->call(array(__CLASS__, 'curl'), $url, $params->get('timeout')); // get cache data or reload cache

        $cache->setCaching(JFactory::getConfig()->getValue('config.caching')); // restore default cache mode

        if (!strpos($result['body'], '<div class="achievements-recent')) { // check if guild data exists
            $err[] = '<strong>no guild data found</strong>';
            if($result['errno'] != 0) {
                $err[] = 'Error: ' . $result['error'] . ' (' . $result['errno'] . ')';
            }
            $err[] = 'battle.net URL: ' . JHTML::link($url, $guild);
            $err[] = 'HTTP Code: ' . $result['info']['http_code'];
            return $err;
        }

        // get only achievement data
        preg_match('#<div class="achievements-recent.*">(.*)<div id="achievement-list"#Uis', $result['body'], $data);

        // find all achievements
        preg_match_all('#<a.*href="achievement\#[0-9:]+:a(\d+)".*>.*background-image: url\("(.*)"\);.*<strong class="title">(.*)</strong>#Uis', $data[1], $matches, PREG_SET_ORDER);

        foreach ($matches as $av) {
            $link = 'http://' . $params->get('wowhead_lang') . '.wowhead.com/achievement=' . $av[1];
            $achievements[] = JHTML::link($link, JHTML::image($av[2], $av[3]), array('title' => $av[3], 'target' => '_blank')) . ' ' . JHTML::link($link, $av[3], array('title' => $av[3], 'target' => '_blank'));
        }

        return $achievements;
    }

    public static function curl($url, $timeout=10) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Joomla! ' . JVERSION . '; WoW latest Guild Achievements Module; php/' . phpversion());
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Connection: Close'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);

        $body = curl_exec($curl);
        $info = curl_getinfo($curl);
        $errno = curl_errno($curl);
        $error = curl_error($curl);

        curl_close($curl);

        return array('info' => $info, 'errno' => $errno, 'error' => $error, 'body' => $body);
    }

}