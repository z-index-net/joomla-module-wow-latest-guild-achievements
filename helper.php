<?php

/**
 * @author     Branko Wilhelm <branko.wilhelm@gmail.com>
 * @link       http://www.z-index.net
 * @copyright  (c) 2013 Branko Wilhelm
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die;

abstract class mod_wow_latest_guild_achievements
{

    public static function _(JRegistry &$params)
    {
        $url = 'http://' . $params->get('region') . '.battle.net/wow/' . $params->get('lang') . '/guild/' . $params->get('realm') . '/' . $params->get('guild') . '/achievement';

        $cache = JFactory::getCache('wow', 'output');
        $cache->setCaching(1);
        $cache->setLifeTime($params->get('cache_time', 60));

        $key = md5($url);

        if (!$result = $cache->get($key)) {
            try {
                $http = new JHttp(new JRegistry, new JHttpTransportCurl(new JRegistry));
                $http->setOption('userAgent', 'Joomla! ' . JVERSION . '; WoW latest Guild Achievements Module; php/' . phpversion());

                $result = $http->get($url, null, $params->get('timeout', 10));
            } catch (Exception $e) {
                return $e->getMessage();
            }

            $cache->store($result, $key);
        }

        if ($result->code != 200) {
            return __CLASS__ . ' HTTP-Status ' . JHtml::_('link', 'http://wikipedia.org/wiki/List_of_HTTP_status_codes#' . $result->code, $result->code, array('target' => '_blank'));
        }

        if (strpos($result->body, '<div class="achievements-recent profile-box-full">') === false) {
            return 'no achievements found';
        }

        // get only achievement data
        preg_match('#<div class="achievements-recent profile-box-full">(.+?)</div>#is', $result->body, $result->body);

        $result->body = $result->body[1];

        // find all achievements
        preg_match_all('#<a.*href="achievement\#([0-9:]+):a(\d+)".*>.*background-image: url\("(.*)"\);.*<strong class="title">(.*)</strong>#Uis', $result->body, $matches, PREG_SET_ORDER);

        foreach ($matches as $key => $match) {
            $achievements[$key] = new stdClass;
            $achievements[$key]->name = $match[4];
            $achievements[$key]->image = $match[3];
            $achievements[$key]->id = $match[2];
            $achievements[$key]->link = $url . '#' . $match[1] . ':a' . $match[2];
            $achievements[$key]->link = self::link($achievements[$key], $params);
        }

        return $achievements;
    }

    private static function link(stdClass $achievement, JRegistry &$params)
    {
        $sites['battle.net'] = $achievement->link;
        $sites['wowhead.com'] = 'http://' . $params->get('lang') . '.wowhead.com/achievement=' . $achievement->id;
        $sites['wowdb.com'] = 'http://www.wowdb.com/achievements/' . $achievement->id;
        $sites['buffed.de'] = 'http://wowdata.buffed.de/?a=' . $achievement->id;
        return $sites[$params->get('link')];
    }
}