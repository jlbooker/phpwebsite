<?php

  /**
   * Contains administrative functionality
   *
   * main : controls administrative routing
   *
   * @author Matthew McNaney <mcnaney at gmail dot com>
   * @version $Id$
   */

class Calendar_Admin {
    /**
     * @var pointer to the parent object
     */
    var $calendar = null;
    var $title    = null;
    var $content  = null;
    var $message  = null;


    function Calendar_Admin()
    {
        if (!isset($_SESSION['Calendar_Admin_Message'])) {
            return NULL;
        }

        $this->message = $_SESSION['Calendar_Admin_Message'];
        unset($_SESSION['Calendar_Admin_Message']);
    }


    function checkAuthorization($command, $id)
    {
        if (empty($id)) {
            return Current_User::authorized('calendar', $command);
        } else {
            return Current_User::authorized('calendar', $command, $id);
        }
    }

    function editEvent($event)
    {
        if ($event->id) {
            $this->title = _('Update event');
        } else {
            $this->title = _('Create event');
        }

        $this->content = $this->event_form($event);
    }

    function editSchedule()
    {
        if ($this->calendar->schedule->id) {
            $this->title = _('Update schedule');
        } else {
            $this->title = _('Create schedule');
        }

        $this->content = $this->calendar->schedule->form();
    }

    /**
     * Creates the edit form for an event
     */
    function event_form(&$event)
    {
        Layout::addStyle('calendar');
        
        // the form id is linked to the check_date javascript
        $form = & new PHPWS_Form('event_form');
        if (isset($_REQUEST['js'])) {
            $form->addHidden('js', 1);
        }

        $form->addHidden('module', 'calendar');
        $form->addHidden('aop', 'post_event');
        $form->addHidden('event_id', $event->id);
        $form->addHidden('sch_id', $event->_schedule->id);

        $form->addText('summary', $event->summary);
        $form->setLabel('summary', _('Summary'));
        $form->setSize('summary', 60);

        $form->addText('location', $event->location);
        $form->setLabel('location', _('Location'));
        $form->setSize('location', 60);

        $form->addText('loc_link', $event->loc_link);
        $form->setLabel('loc_link', _('Location link'));
        $form->setSize('loc_link', 60);

        $form->addTextArea('description', $event->description);
        $form->useEditor('description');
        $form->setLabel('description', _('Description'));

        $form->addText('start_date', $event->getStartTime('%Y/%m/%d'));
        $form->setLabel('start_date', _('Start time'));
        $form->setExtra('start_date', 'onblur="check_start_date()"');

        $form->addText('end_date', $event->getEndTime('%Y/%m/%d'));
        $form->setLabel('end_date', _('End time'));
        $form->setExtra('end_date', 'onblur="check_end_date()" onfocus="check_start_date()"');

        $form->addButton('close', _('Cancel'));
        $form->setExtra('close', 'onclick="window.close()"');

        $event->timeForm('start_time', $event->start_time, $form);
        $event->timeForm('end_time', $event->end_time, $form);

        $form->setExtra('start_time_hour', 'onchange="check_start_date()"');
        $form->setExtra('end_time_hour', 'onchange="check_end_date()"');

        $form->addCheck('all_day', 1);
        $form->setMatch('all_day', $event->all_day);
        $form->setLabel('all_day', _('All day event'));
        $form->setExtra('all_day', 'onchange="alter_date(this)"');

        $form->addCheck('show_busy', 1);
        $form->setMatch('show_busy', $event->show_busy);
        $form->setLabel('show_busy', _('Show busy'));

        /**
         * Repeat form elements
         */

        $form->addCheck('repeat_event', 1);
        $form->setLabel('repeat_event', _('Make a repeating event'));

        $form->addText('end_repeat_date', $event->getEndRepeat('%Y/%m/%d'));
        $form->setLabel('end_repeat_date', _('Repeat event until:'));

        $modes = array('daily',
                       'weekly',
                       'monthly',
                       'yearly',
                       'every');


        $modes_label = array(_('Daily'),
                             _('Weekly'),
                             _('Monthly'),
                             _('Yearly'),
                             _('Every'));

        $form->addRadio('repeat_mode', $modes);
        $form->setLabel('repeat_mode', $modes_label);

        $weekdays = array(1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7);

        $weekday_labels = array(1=>strftime('%A', mktime(0,0,0,1,5,1970)),
                                2=>strftime('%A', mktime(0,0,0,1,6,1970)),
                                3=>strftime('%A', mktime(0,0,0,1,7,1970)),
                                4=>strftime('%A', mktime(0,0,0,1,8,1970)),
                                5=>strftime('%A', mktime(0,0,0,1,9,1970)),
                                6=>strftime('%A', mktime(0,0,0,1,10,1970)),
                                7=>strftime('%A', mktime(0,0,0,1,11,1970))
                                );

        $form->addCheck('weekday_repeat', $weekdays);
        $form->setLabel('weekday_repeat', $weekday_labels);

        $monthly = array('begin' => _('Beginning of each month'),
                         'end'   => _('End of each month'),
                         'start' => _('Every month on start date')
                         );

        $form->addSelect('monthly_repeat', $monthly);

        $every_repeat_week = array(1   => _('1st'),
                                   2   => _('2nd'),
                                   3   => _('3rd'),
                                   4   => _('4th'),
                                   'last' => _('Last')
                                   );

        $frequency = array('every_month' => _('Every month'),
                           1 => strftime('%B', mktime(0,0,0,1,1,1970)),
                           2 => strftime('%B', mktime(0,0,0,2,1,1970)),
                           3 => strftime('%B', mktime(0,0,0,3,1,1970)),
                           4 => strftime('%B', mktime(0,0,0,4,1,1970)),
                           5 => strftime('%B', mktime(0,0,0,5,1,1970)),
                           6 => strftime('%B', mktime(0,0,0,6,1,1970)),
                           7 => strftime('%B', mktime(0,0,0,7,1,1970)),
                           8 => strftime('%B', mktime(0,0,0,8,1,1970)),
                           9 => strftime('%B', mktime(0,0,0,9,1,1970)),
                           10 => strftime('%B', mktime(0,0,0,10,1,1970)),
                           11 => strftime('%B', mktime(0,0,0,11,1,1970)),
                           12 => strftime('%B', mktime(0,0,0,12,1,1970)));

        $form->addSelect('every_repeat_number', $every_repeat_week);
        $form->addSelect('every_repeat_weekday', $weekday_labels);
        $form->addSelect('every_repeat_frequency', $frequency);

        /* set repeat form matches */
        if (!empty($event->repeat_type)) {
            $repeat_info = explode(':', $event->repeat_type);
            $repeat_mode_match = $repeat_info[0];
            if (isset($repeat_info[1])) {
                $repeat_vars = explode(';', $repeat_info[1]);
            }

            $form->setMatch('repeat_mode', $repeat_mode_match);

            switch($repeat_mode_match) {
            case 'weekly':
                $form->setMatch('weekday_repeat', $repeat_vars);
                break;

            case 'monthly':
                $form->setMatch('monthly_repeat', $repeat_vars[0]);
                break;

            case 'every':
                $form->setMatch('every_repeat_number', $repeat_vars[0]);
                $form->setMatch('every_repeat_weekday', $repeat_vars[1]);
                $form->setMatch('every_repeat_frequency', $repeat_vars[2]);
                break;
            }

            $form->setMatch('repeat_event', 1);
        }


        if ($event->pid) {
            $form->addHidden('pid', $event->pid);
            // This is a repeat copy, if saved it removes it from the copy list
            $form->addSubmit('save', _('Save and remove repeat'));
            $form->setExtra('save', sprintf('onclick="return confirm(\'%s\')"',
                                            _('Remove event from repeat list?')) );
        } elseif ($event->repeat_type) {
            // This is event is a source repeating event

            // Save this 
            // Not sure if coding this portion. commenting for now
            // $form->addSubmit('save_source', _('Save this event only'));
            $form->addSubmit('save_copy', _('Save and apply to repeats'));
            $form->setExtra('save_copy', sprintf('onclick="return confirm(\'%s\')"',
                                            _('Apply changes to repeats?')) );
        } else {
            // this is a non-repeating event
            $form->addSubmit('save', _('Save event'));
        }

        $tpl = $form->getTemplate();

        $js_vars['date_name'] = 'start_date';
        $tpl['START_CAL'] = javascript('js_calendar', $js_vars);

        $js_vars['date_name'] = 'end_date';
        $tpl['END_CAL'] = javascript('js_calendar', $js_vars);

        $js_vars['date_name'] = 'end_repeat_date';
        $tpl['END_REPEAT'] = javascript('js_calendar', $js_vars);


        if (isset($event->_error)) {
            $tpl['ERROR'] = implode('<br />', $event->_error);
        }

        if ($event->pid) {
            $linkvar['aop']      = 'edit_event';
            $linkvar['sch_id']   = $event->_schedule->id;
            $linkvar['event_id'] = $event->pid;
            if (javascriptEnabled()) {
                $linkvar['js'] = 1;
            }

            $source_link = PHPWS_Text::moduleLink(_('Click here if you would prefer to edit the source event.'), 'calendar', $linkvar);
            $tpl['REPEAT_WARNING'] = _('This is a repeat of another event.') . '<br />' . $source_link;
        }

        javascript('modules/calendar/edit_event');
        javascript('modules/calendar/check_date');
        $tpl['EVENT_TAB'] = _('Event');
        $tpl['REPEAT_TAB'] = _('Repeat');
        return PHPWS_Template::process($tpl, 'calendar', 'admin/forms/edit_event.tpl');
    }


    function &getPanel()
    {
        $panel = & new PHPWS_Panel('calendar');


        $vars['aop'] = 'schedules';
        $tabs['schedules'] = array('title' => _('Schedules'),
                                   'link' => PHPWS_Text::linkAddress('calendar', $vars));

        $vars['aop'] = 'settings';                                   
        if (Current_User::allow('calendar', 'settings')) {
            $tabs['settings']    = array('title' => _('Settings'),
                                         'link' => PHPWS_Text::linkAddress('calendar', $vars));
        }

        $panel->quickSetTabs($tabs);
        return $panel;
    }

    /**
     * routes administrative commands
     */
    function main()
    {
        if (!Current_User::allow('calendar')) {
            Current_User::disallow();
            return;
        }

        $panel = $this->getPanel();

        if (isset($_REQUEST['aop'])) {
            $command = $_REQUEST['aop'];
        } elseif (isset($_REQUEST['tab'])) {
            $command = $_REQUEST['tab'];
        } else {
            $command = $panel->getCurrentTab();
        }

        switch ($command) {
        case 'create_event':
            $panel->setCurrentTab('schedules');
            $event = $this->calendar->schedule->loadEvent();
            $this->editEvent($event);
            break;

        case 'create_schedule':
            if (!Current_User::allow('calendar', 'create_schedule')) {
                Current_User::disallow();
            }
            $panel->setCurrentTab('schedules');
            $this->editSchedule();
            break;

        case 'delete_event':
            $event = $this->calendar->schedule->loadEvent();
            $result = $event->delete();
            if (PEAR::isError($result)) {
                PHPWS_Error::log($result);
            }
            PHPWS_Core::goBack();
            break;

        case 'delete_schedule':
            $this->calendar->schedule->delete();
            $this->sendMessage(_('Schedule deleted.'), 'schedules');
            break;

        case 'edit_event':
            $panel->setCurrentTab('schedules');
            $event = $this->calendar->schedule->loadEvent();
            $this->editEvent($event);
            break;

        case 'edit_schedule':
            if (empty($_REQUEST['sch_id'])) {
                PHPWS_Core::errorPage('404');
            }

            if (!Current_User::allow('calendar', 'edit_schedule', (int)$_REQUEST['sch_id'])) {
                Current_User::disallow();
            }
            $panel->setCurrentTab('schedules');
            $this->editSchedule();
            break;

        case 'make_default_public':
            if (Current_User::isUnrestricted('calendar')) {
                PHPWS_Settings::set('calendar', 'public_schedule', (int)$_REQUEST['sch_id']);
                PHPWS_Settings::save('calendar');
                $this->message =_('Default public schedule set.');
            }
            $this->scheduleListing();
            break;

        case 'my_schedule':
            $panel->setCurrentTab('my_schedule');
            $this->mySchedule();
            break;

        case 'post_event':
            if (!$this->checkAuthorization('edit_schedule', $_POST['sch_id'])) {
                Current_User::disallow();
            }
            $this->postEvent();
            break;

        case 'post_schedule':
            if (!$this->checkAuthorization('edit_schedule', $_POST['sch_id'])) {
                Current_User::disallow();
            }
            $this->postSchedule();
            break;

        case 'repeat_event':
            $panel->setCurrentTab('schedules');
            $event = $this->calendar->schedule->loadEvent();
            $this->repeatEvent($event);
            break;

        case 'schedules':
            $this->scheduleListing();
            break;

        case 'settings':

            break;
        }

        $tpl['CONTENT'] = $this->content;
        $tpl['TITLE']   = $this->title;

        if (is_array($this->message)) {
            $tpl['MESSAGE'] = implode('<br />', $this->message);
        } else {
            $tpl['MESSAGE'] = $this->message;
        } 

        // Clears in case of js window opening
        $this->content = $this->title = $this->message = null;

        $final = PHPWS_Template::process($tpl, 'calendar', 'admin/main.tpl');

        if (PHPWS_Calendar::isJS()) {
            Layout::nakedDisplay($final);
        } else {
            $panel->setContent($final);
            Layout::add(PHPWS_ControlPanel::display($panel->display()));
        }

    }

    function mySchedule()
    {
        echo 'my schedule needs work or deletion';
        return;
        //        $this->title = _('My Schedule');
        if (!PHPWS_Settings::get('calendar', 'personal_schedules')) {
            return _('Sorry, personal schedules are disabled.');
        }

        $schedule = Calendar_Schedule::getCurrentUserSchedule();

        if (PEAR::isError($schedule)) {
            PHPWS_Error::log($schedule);
            $this->sendMessage(_('An error occurred when accessing the schedules.'));
            return NULL;
        } elseif (!$schedule) {
            $this->sendMessage(_('You currently do not have a personal schedule. Please create one.'), 'create_personal_schedule');
        }

        $this->content = $schedule->view();
    }

    /**
     * Checks the legitimacy of the event and saves the results
     */
    function postEvent()
    {
        $event = $this->calendar->schedule->loadEvent();
        $event->loadPrevious();
        if ($event->post()) {
            if ($event->pid) {
                /**
                 * if the pid is set, then it's saving a copy event
                 * copy events are changed to source events so 
                 * the pid and key are reset
                 */
                $event->pid = 0;
                $event->key_id = 0;
            }

            $result = $event->save();
            $this->saveRepeat($event);

            if (PEAR::isError($result)) {
                PHPWS_Error::log($result);
                if(PHPWS_Calendar::isJS()) {
                    $this->sendMessage(_('An error occurred when saving your event.'), null, false);
                    javascript('close_refresh');
                    Layout::nakedDisplay();
                    exit();
                } else {
                    $this->sendMessage(_('An error occurred when saving your event.'), 'schedules');
                }
            } else {
                if(PHPWS_Calendar::isJS()) {
                    javascript('close_refresh');
                    Layout::nakedDisplay();
                    exit();
                } else {
                    $this->sendMessage(_('Event saved.'), 'schedules');
                }
            }
        } else {
            $this->editEvent($event);
        }
    }


    /**
     * Saves a repeated event
     */
    function saveRepeat(&$event)
    {

        // if this event has a parent id, don't try and save repeats
        if ($event->pid) {
            return true;
        }

        // This event is not repeating
        if (empty($event->repeat_type)) {
            // Previously, the event repeated, remove the copies
            $result = $event->clearRepeats();
            if (PEAR::isError($result)) {
                PHPWS_Error::log($result);
            }
            return true;
        }

        // Event is repeating

        // First check if the repeat scheme changed

        if ($event->_previous_repeat && $event->getCurrentHash() == $event->_previous_settings) {
            // The event has not changed, so we just update the repeats
            // that exist and return
            return $event->updateRepeats();
        }

        // The repeat setting changed or were never set, so need to recreate the copies
        $result = $event->clearRepeats();
        if (PEAR::isError($result)) {
            PHPWS_Error::log($result);
        }


        $repeat_info = explode(':', $event->repeat_type);
        $repeat_mode = $repeat_info[0];
        if (isset($repeat_info[1])) {
            $repeat_vars = explode(';', $repeat_info[1]);
        }

        switch ($repeat_mode) {
        case 'daily':
            $result = $this->repeatDaily($event);
            break;

        case 'weekly':
            $result = $this->repeatWeekly($event);
            break;

        case 'monthly':
            $result = $this->repeatMonthly($event);
            break;

        case 'yearly':
            $result = $this->repeatYearly($event);
            break;

        case 'every':
            $result = $this->repeatEvery($event);
            break;
        }

        if (!$result) {
            return false;
        }

        if (PEAR::isError($result)) {
            if (PHPWS_Calendar::isJS()) {
                PHPWS_Error::log($result);
                $this->sendMessage(_('An error occurred when trying to repeat an event.', null, false));
                javascript('close_refresh');
                Layout::nakedDisplay();
                exit();
            } else {
                $this->sendMessage(_('An error occurred when trying to repeat an event.', 'schedules'));
            }
        } else {
            if (PHPWS_Calendar::isJS()) {
                PHPWS_Error::log($result);
                $this->sendMessage(_('Event repeated.', null, false));
                javascript('close_refresh');
                Layout::nakedDisplay();
                exit();
            } else {
                $this->sendMessage(_('Event repeated.', 'schedules'));
            }
        }
    }

    function postSchedule()
    {
        if ($this->calendar->schedule->post()) {
            $result = $this->calendar->schedule->save();
            if (PEAR::isError($result)) {
                PHPWS_Error::log($result);
                if(PHPWS_Calendar::isJS()) {
                    $this->sendMessage(_('An error occurred when saving your schedule.'), null, false);
                    javascript('close_refresh');
                    Layout::nakedDisplay();
                    exit();
                } else {
                    $this->sendMessage(_('An error occurred when saving your schedule.'), 'schedules');
                }
            } else {
                if(PHPWS_Calendar::isJS()) {
                    $this->sendMessage(_('Schedule saved.'), null, false);
                    javascript('close_refresh');
                    Layout::nakedDisplay();
                    exit();
                } else {
                    $this->sendMessage(_('Schedule saved.'), 'schedules');
                }
            }
        } else {
            $this->message = $this->calendar->schedule->_error;
            $this->editSchedule();
        }
    }

    function repeatDaily(&$event)
    {
        $time_unit = $event->start_time + 86400;

        $copy_event = $event->repeatClone();
        $time_diff = $event->end_time - $event->start_time;

        $max_count = 0;
        while($time_unit <= $event->end_repeat) {
            $copy_event->id = 0;

            $max_count++;
            if ($max_count > CALENDAR_MAXIMUM_REPEATS) {
                return PHPWS_Error::get(CAL_REPEAT_LIMIT_PASSED, 'calendar', 'Calendar_Admin::repeatDaily');
            }
            $copy_event->start_time = $time_unit;
            $copy_event->end_time = $time_unit + $time_diff;
            $time_unit += 86400;
            $result = $copy_event->save();
            if (PEAR::isError($result)) {
                return $result;
            }
        }
        return true;
    }

    function repeatEvent($event)
    {
        if (!$event->id) {
            $this->content = _('This event does not exist.');
            return;
        }

        $this->title = sprintf(_('Repeat event - %s'), $event->summary);
        if (@$_REQUEST['js']) {
            $js = true;
        } else {
            $js = false;
        }
        $this->content = $event->repeat($js);
    }

    function repeatEvery(&$event)
    {

    }


    function repeatMonthly(&$event)
    {
        require_once 'Calendar/Month.php';

        if (!isset($_POST['monthly_repeat'])) {
            return false;
        }

        $max_count = 0;
        $copy_event = $event;

        $current_m = (int)strftime('%m', $event->start_time);
        $current_y = (int)strftime('%Y', $event->start_time);

        $time_diff = $event->end_time - $event->start_time;

        $cMonth = & new Calendar_Month($current_y, $current_m);
        $ctm = $cMonth->getTimestamp();
        $ntm = $cMonth->nextMonth('timestamp');
        $cMonth->setTimestamp($ntm);

        switch ($_POST['monthly_repeat']) {
        case 'begin':
            while (1) {
                $ctm = $cMonth->getTimestamp();
                if ($ctm > $event->end_repeat) {
                    break;
                }

                $copy_event->id = 0;
                $copy_event->key_id = 0;
                $max_count++;
                if ($max_count > CALENDAR_MAXIMUM_REPEATS) {
                    return PHPWS_Error::get(CAL_REPEAT_LIMIT_PASSED, 'calendar', 'Calendar_Admin::repeatMonthly');
                }

                $copy_event->start_time = $ctm;
                $copy_event->end_time = $ctm + $time_diff;

                $result = $copy_event->save();
                if (PEAR::isError($result)) {
                    return $result;
                }

                $ntm = $cMonth->nextMonth('timestamp');
                $cMonth->setTimestamp($ntm);
            }
            break;

        case 'end':

            break;

        case 'start':

            break;
        }
        return true;
    }


    function repeatWeekly(&$event)
    {
        if (!isset($_POST['weekday_repeat']) || !is_array($_POST['weekday_repeat'])) {
            $this->message = _('You must choose which weekdays to repeat.');
            return false;
        }

        $time_unit = $event->start_time + 86400;

        $copy_event = $event->repeatClone();
        $time_diff = $event->end_time - $event->start_time;

        $max_count = 0;
        $repeat_days = &$_REQUEST['weekday_repeat'];

        while($time_unit <= $event->end_repeat) {
            if (!in_array(strftime('%u', $time_unit), $repeat_days)) {
                $time_unit += 86400;
                continue;
            }
            $copy_event->id = 0;

            $max_count++;
            if ($max_count > CALENDAR_MAXIMUM_REPEATS) {
                return PHPWS_Error::get(CAL_REPEAT_LIMIT_PASSED, 'calendar', 'Calendar_Admin::repeatWeekly');
            }
            $copy_event->start_time = $time_unit;
            $copy_event->end_time = $time_unit + $time_diff;
            $result = $copy_event->save();
            if (PEAR::isError($result)) {
                return $result;
            }
            $time_unit += 86400;
        }
        return TRUE;
    }


    function repeatYearly(&$event)
    {

    }


    /**
     * Saves the settings posted from the settings page
     */
    function saveSettings()
    {
        PHPWS_Settings::set('calendar', 'info_panel',         $_POST['info_panel']);
        PHPWS_Settings::set('calendar', 'starting_day',       $_POST['starting_day']);
        PHPWS_Settings::set('calendar', 'personal_schedules', $_POST['personal_schedules']);
        PHPWS_Settings::set('calendar', 'hour_format',        $_POST['hour_format']);
        PHPWS_Settings::set('calendar', 'display_mini',       $_POST['display_mini']);
        PHPWS_Settings::save('calendar');
    }

    function scheduleListing()
    {
        $this->title = _('Schedules');

        PHPWS_Core::initCoreClass('DBPager.php');
        PHPWS_Core::initModClass('calendar', 'Schedule.php');

        $page_tags['TITLE_LABEL']        = _('Title');
        $page_tags['DESCRIPTION_LABEL']  = _('Description');
        $page_tags['PUBLIC_LABEL']       = _('Public');
        $page_tags['DISPLAY_NAME_LABEL'] = _('User');
        $page_tags['AVAILABILITY_LABEL'] = _('Availability');

        $vars = array('aop'=>'create_schedule');
        $label = _('Create schedule');

        if (javascriptEnabled()) {
            $vars['js'] = 1;
            $js_vars['address'] = PHPWS_Text::linkAddress('calendar', $vars);
            $js_vars['label']   = $label;
            $js_vars['width']   = 640;
            $js_vars['height']  = 600;
            $page_tags['ADD_CALENDAR']       = javascript('open_window', $js_vars);
        } else {
            $page_tags['ADD_CALENDAR'] = PHPWS_Text::secureLink($label, 'calendar', $vars);
        }
            
        $page_tags['ADMIN_LABEL']        = _('Options');

        $pager = & new DBPager('calendar_schedule', 'Calendar_Schedule');
        $pager->setModule('calendar');
        $pager->setTemplate('admin/schedules.tpl');
        $pager->addPageTags($page_tags);
        $pager->addRowTags('rowTags');
        $pager->setEmptyMessage(_('No schedules have been created.'));
        
        $pager->db->addWhere('user_id', 0);
        $pager->db->addWhere('user_id', 'users.id', '=', 'or');
        
        $pager->db->addColumn('*');
        $pager->db->addColumn('users.display_name');
        $pager->db->addJoin('left', 'calendar_schedule', 'users', 'user_id', 'id');

        $pager->initialize();
 
        $this->content = $pager->get();
    }

    function sendMessage($message, $command=null, $route=true)
    {
        $_SESSION['Calendar_Admin_Message'] = $message;
        if ($route && !empty($command)) {
            PHPWS_Core::reroute('index.php?module=calendar&aop=' . $command);
        }
    }

}

?>