<?php

/**
 * Wiki for phpWebSite
 *
 * See docs/CREDITS for copyright information
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @author      Greg Meiste <blindman1344@NOSPAM.users.sourceforge.net>
 * @version $Id: uninstall.php,v 1.8 2007/05/28 19:00:14 blindman1344 Exp $
 */

function wiki_uninstall(&$content)
{
    PHPWS_DB::dropTable('wiki_pages');
    PHPWS_DB::dropTable('wiki_images');
    PHPWS_DB::dropTable('wiki_interwiki');
    $content[] = dgettext('wiki', 'Wiki tables removed.');
    return TRUE;
}

?>