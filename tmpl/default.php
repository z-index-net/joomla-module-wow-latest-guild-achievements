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
// no direct accesss
defined('_JEXEC') or die;

JFactory::getDocument()->addStyleSheet(JURI::base(true) . '/modules/mod_wow_latest_guid_achievements/tmpl/stylesheet.css');
?>
<div class="mod_wow_latest_guid_achievements<?php echo $params->get('moduleclass_sfx'); ?>">
    <ul>
        <?php foreach ($achievements as $row) { ?>
            <li title="<?php echo $row->desc; ?>"><a href="http://<?php echo $params->get('wowhead_lang'); ?>.wowhead.com/achievement=<?php echo $row->av; ?>" target="_blank"><img src="<?php echo $row->icon; ?>" width="18" height="18" alt="" /></a> <a href="http://<?php echo $params->get('wowhead_lang'); ?>.wowhead.com/achievement=<?php echo $row->av; ?>" target="_blank"><?php echo $row->desc; ?></a></li>
        <?php } ?>
    </ul>
</div>
