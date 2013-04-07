<?php
/**
 * WoW latest Guild Achievements Module
 *
 * @author     Branko Wilhelm <bw@z-index.net>
 * @link       http://www.z-index.net
 * @copyright  (c) 2011 - 2013 Branko Wilhelm
 * @package    mod_wow_latest_guild_achievements
 * @license    GNU General Public License v3
 * @version    $Id: default.php 23 2013-03-31 17:42:41Z bRunO $
 */

defined('_JEXEC') or die;

JFactory::getDocument()->addStyleSheet(JURI::base(true) . '/modules/' . $module->module . '/tmpl/stylesheet.css');
?>
<div class="mod_wow_latest_guild_achievements">
    <?php foreach ($achievements as $achievement): ?>
    <?php $attributes = array('target' => '_blank', 'data-darktip' => 'wow.achievement:'.$params->get('region').'.'.$achievement->id.'('.$params->get('lang').')'); ?>
    <div>
    <?php echo JHTML::link($achievement->link, JHTML::image($achievement->image, $achievement->name), $attributes); ?>
    <?php echo JHTML::link($achievement->link, $achievement->name, $attributes); ?>
    </div>
    <?php endforeach; ?>
</div>
