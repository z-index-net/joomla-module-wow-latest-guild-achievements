<?php

/**
 * @author     Branko Wilhelm <branko.wilhelm@gmail.com>
 * @link       http://www.z-index.net
 * @copyright  (c) 2013 Branko Wilhelm
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die;

JFactory::getDocument()->addStyleSheet(JUri::base(true) . '/modules/' . $module->module . '/tmpl/default.css');
?>
<div class="mod_wow_latest_guild_achievements">
    <?php foreach ($achievements as $achievement): ?>
        <div>
            <?php echo JHtml::link($achievement->link, JHtml::image($achievement->image, $achievement->name), array('title' => $achievement->name, 'target' => '_blank')); ?>
            <?php echo JHtml::link($achievement->link, $achievement->name, array('title' => $achievement->name, 'target' => '_blank')); ?>
        </div>
    <?php endforeach; ?>
</div>
