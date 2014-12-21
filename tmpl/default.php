<?php

/**
 * @author     Branko Wilhelm <branko.wilhelm@gmail.com>
 * @link       http://www.z-index.net
 * @copyright  (c) 2011 - 2015 Branko Wilhelm
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @var        array $achievements
 * @var        stdClass $module
 * @var        Joomla\Registry\Registry $params
 */

defined('_JEXEC') or die;

JFactory::getDocument()->addStyleSheet('media/' . $module->module . '/css/default.css');
?>
<?php if ($params->get('ajax')) : ?>
    <div class="mod_wow_latest_guild_achievements ajax"></div>
<?php else: ?>
    <div class="mod_wow_latest_guild_achievements">
        <?php foreach ($achievements as $achievement): ?>
            <div>
                <?php echo JHtml::_('link', $achievement->link, JHtml::_('image', $achievement->image, $achievement->name), array('title' => $achievement->name, 'target' => '_blank')); ?>
                <?php echo JHtml::_('link', $achievement->link, $achievement->name, array('title' => $achievement->name, 'target' => '_blank')); ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>