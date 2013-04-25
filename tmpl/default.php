<?php
/**
 * WoW latest Guild Achievements Module
 *
 * @author     Branko Wilhelm <bw@z-index.net>
 * @link       http://www.z-index.net
 * @copyright  (c) 2011 - 2013 Branko Wilhelm
 * @package    mod_wow_latest_guild_achievements
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @version    $Id$
 */

defined('_JEXEC') or die;

JFactory::getDocument()->addStyleSheet(JUri::base(true) . '/modules/' . $module->module . '/tmpl/stylesheet.css');
?>
<div class="mod_wow_latest_guild_achievements">
    <?php foreach ($achievements as $achievement): ?>
    <div>
    <?php echo JHtml::link($achievement->link, JHtml::image($achievement->image, $achievement->name), array('title' => $achievement->name, 'target' => '_blank')); ?>
    <?php echo JHtml::link($achievement->link, $achievement->name, array('title' => $achievement->name, 'target' => '_blank')); ?>
    </div>
    <?php endforeach; ?>
</div>
