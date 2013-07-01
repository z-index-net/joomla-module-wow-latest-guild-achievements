<?php

/**
 * @author     Branko Wilhelm <branko.wilhelm@gmail.com>
 * @link       http://www.z-index.net
 * @copyright  (c) 2013 Branko Wilhelm
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die;

require_once dirname(__FILE__) . '/helper.php';

$params->set('guild', rawurlencode(str_replace(' ', '_', JString::strtolower($params->get('guild')))));
$params->set('realm', rawurlencode(JString::strtolower($params->get('realm'))));
$params->set('region', JString::strtolower($params->get('region')));
$params->set('lang', JString::strtolower($params->get('lang', 'en')));
$params->set('link', $params->get('link', 'battle.net'));

$achievements = mod_wow_latest_guild_achievements::_($params);

if(!is_array($achievements)) {
	echo $achievements;
	return;
}

require JModuleHelper::getLayoutPath($module->module, $params->get('layout', 'default'));