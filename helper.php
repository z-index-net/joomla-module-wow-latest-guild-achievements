<?php

/**
 * WoW latest Guild Achievements Module
 *
 * @author     Branko Wilhelm <bw@z-index.net>
 * @link       http://www.z-index.net
 * @copyright  (c) 2011 Branko Wilhelm
 * @package    mod_wow_latest_guid_achievements
 * @license    GNU Public License <http://www.gnu.org/licenses/gpl.html>
 * @version    $Id$
 */
// no direct access
defined('_JEXEC') or die;

jimport('joomla.cache.cache');

class mod_wow_latest_guid_achievements {

    public static $overall_points = null;

    public static function onload(&$params) {

        // all required paramters set?
        if (!$params->get('lang') || !$params->get('realm') || !$params->get('guild')) {
            JError::raiseWarning(500, JText::_('please configure Module') . ' - ' . __CLASS__);
            return;
        }

        // if curl installed?
        if (!function_exists('curl_init')) {
            JError::raiseWarning(500, JText::_('php-curl extension not found'));
            return;
        }

        // wowhead script integration if wanted
        if ($params->get('wowhead')) {
            JFactory::getDocument()->addScript(JURI::getInstance()->getScheme() . '://static.wowhead.com/widgets/power.js');
        }

        // build battle net URL
        $url = 'http://' . strtolower($params->get('region')) . '.battle.net/wow/' . strtolower($params->get('lang')) . '/guild/' . urlencode(strtolower($params->get('realm'))) . '/' . urlencode(strtolower($params->get('guild'))) . '/achievement';

        $cache = & JFactory::getCache(); // get cache obj
        $cache->setCaching(1); // enable cache for this module
        $cache->setLifeTime($params->get('cache_time', 60)); // time to cache

        $result = $cache->call(array(__CLASS__, 'curl'), $url, $params->get('timeout')); // get cache data or reload cache

        $cache->setCaching(JFactory::getConfig()->getValue('config.caching')); // restore default cache mode

        if (!strpos($result, '<div class="achievements-recent')) { // check if guild data exists
            return JText::_('no guild data found');
        }

        // TEST - overall achievement point
        preg_match('#<div class="achievement-points">(.*)</div>#', $result, self::$overall_points);

        // get only achievement data
        preg_match('#<div class="achievements-recent.*">(.*)<div id="achievement-list"#Uis', $result, $data);

        // find all achievements
        preg_match_all('#<a.*href="achievement\#[0-9:]+:a(\d+)".*>.*background-image: url\("(.*)"\);.*<strong class="title">(.*)</strong>#Uis', $data[1], $matches, PREG_SET_ORDER);

        foreach ($matches as $av) {
            $ret[$av[1]] = new stdClass;
            $ret[$av[1]]->av = $av[1];
            $ret[$av[1]]->icon = $av[2];
            $ret[$av[1]]->desc = $av[3];
        }

        return $ret;
    }

    public static function curl($url, $timeout=10) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Joomla! WoW latest Guild Achievements Module; php/' . phpversion());
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Connection: Close'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);

        $body = curl_exec($curl);

        curl_close($curl);

        return $body;
    }

}