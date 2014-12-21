<?php

/**
 * @author     Branko Wilhelm <branko.wilhelm@gmail.com>
 * @link       http://www.z-index.net
 * @copyright  (c) 2011 - 2015 Branko Wilhelm
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die;

class ModWowLatestGuildAchievementsHelper extends WoWModuleAbstract
{
    protected function getInternalData()
    {
        $wow = WoW::getInstance();

        try {
            $adapter = $wow->getAdapter('WoWAPI');
            $result = $adapter->getData('guild');
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $achievements = array();
        foreach ($result->body->achievements->achievementsCompleted as $key => $achievement) {
            $achievements[$achievement] = new stdClass;
            $achievements[$achievement]->timestamp = $result->body->achievements->achievementsCompletedTimestamp[$key];
        }

        arsort($achievements);

        $achievements = array_slice($achievements, 0, $this->params->module->get('rows', 10) ? $this->params->module->get('rows', 10) : count($achievements), true);

        foreach ($achievements as $key => $achievement) {
            try {
                $result = $adapter->getAchievement($key);
            } catch (Exception $e) {
                unset($achievements[$key]);
                continue;
            }

            $achievements[$key]->id = $key;
            $achievements[$key]->name = $result->body->title;
            $achievements[$key]->image = 'http://media.blizzard.com/wow/icons/18/' . $result->body->icon . '.jpg';
            $achievements[$key]->link = $wow->getBattleNetUrl() . 'achievement#15080:a' . $key; // TODO 15080 ??
            $achievements[$key]->link = $this->link($achievements[$key]);
            $achievements[$key]->raw = $result->body;
        }

        return $achievements;
    }

    private function link(stdClass $achievement)
    {
        $sites['battle.net'] = $achievement->link;
        $sites['wowhead.com'] = 'http://' . $this->params->global->get('locale') . '.wowhead.com/achievement=' . $achievement->id;

        return $sites[$this->params->global->get('link')];
    }
}