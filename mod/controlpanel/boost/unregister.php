<?php

function controlpanel_unregister($module, &$content){
  PHPWS_Core::initModClass("controlpanel", "Tab.php");
  PHPWS_Core::initModClass("controlpanel", "Link.php");

  $itemnameList = array();
  $cpFile = PHPWS_Core::getConfigFile($module, "controlpanel.php");

  if (!is_file($cpFile))
    return TRUE;

  include_once($cpFile);

  /*** Get all the links associated with a module ***/
  if (isset($link) && is_array($link)){
    foreach ($link as $info){
      if (isset($info['itemname']))
	$itemname = $info['itemname'];
      else
	$itemname = $module;

      if (!in_array($itemname, $itemnameList))
	$itemnameList[] = $itemname;
    }

    $db = & new PHPWS_DB("controlpanel_link");
    foreach ($itemnameList as $itemname){
      $db->addWhere("itemname", $itemname);
      $result = $db->loadObjects("PHPWS_Panel_Link");
      
      if (PEAR::isError($result) || empty($result))
	return $result;

      foreach ($result as $link)
	$link->kill();
    }
  }

  $itemname = $info = NULL;
  $labelList = array();

  /** Get all the tabs associated with a module **/
  if (isset($tabs) && is_array($tabs)){
    foreach ($tabs as $info){
      if (isset($info['label']))
	$label = $info['label'];
      else
	$label = strtolower(preg_replace("/\W/", "_", $info['title']));

      if (!in_array($label, $labelList))
	$labelList[] = $label;
    }

    $db = & new PHPWS_DB("controlpanel_tab");
    foreach ($labelList as $label){
      $db->addWhere("label", $label);
      $result = $db->loadObjects("PHPWS_Panel_Tab");

      if (PEAR::isError($result) || empty($result))
	return $result;

      foreach ($result as $tab)
	$tab->kill();
    }
  }

  PHPWS_ControlPanel::reset();
}
?>