<?php
  /**
   * Contains extra content for Flash video filter.
   * Go to http://flowplayer.sourceforge.net/howto.html
   * for information on customizing your flash template.
   * 
   * @version $Id$
   * @author Matthew McNaney <mcnaney at gmail dot com>
   */

$tpl['HEIGHT'] = $tpl['HEIGHT'] + 22; //the FlowPlayer requires 22 extra height pixels

$fa = explode('.', $this->file_name);
array_pop($fa);
$tn = $this->file_directory . 'tn/' . implode('.', $fa) . '.jpg';

if (is_file($tn)) {
    $tpl['START_SCREEN'] = "overlayId: 'play',url: '$tn'";
} else {
    $tpl['START_SCREEN'] = "url: 'templates/filecabinet/filters/flash/play-button-328x240.jpg'";
}
$tpl['ID'] = mt_rand();

Layout::addJSHeader('<script type="text/javascript" src="templates/filecabinet/filters/flash/swfobject.js"></script>', 'swfobject');
?>