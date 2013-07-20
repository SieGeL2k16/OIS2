<?php
/**
 * AJAX backend for jobs plugin.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.02 (30-May-2013)
 * $Id$
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');
$MODE = $SGLFUNC->GetRequestParam('MODE');
if($MODE == '')
  {
  die("MODE VALUE IS EMPTY - ABORTING !!");
  }
switch($MODE)
  {
  case  'TOGGLE':
        $retdata = array();
        $retdata['o'] = $SGLFUNC->GetRequestParam('JOB');
        $dummy = $SGLFUNC->GetRequestParam('NEWSTATE');
        if(StrToLower($dummy) == 'enable')
          {
          $SQL = "BEGIN DBMS_SCHEDULER.ENABLE(:o); END;";
          }
        else if(StrToLower($dummy) == 'disable')
          {
          $SQL = "BEGIN DBMS_SCHEDULER.DISABLE(:o); END;";
          }
        else
          {
          die("NEWSTATE PARAMETER HAS INVALID VALUE - ABORTING!!");
          }
        $rc = $db->QueryHash($SQL,OCI_ASSOC,1,$retdata);
        $db->Commit();
        $db->Disconnect();
        if(is_array($rc) === FALSE) $retdata = $rc;
        echo(json_encode($retdata));
        exit;

  default:
        die("ERROR: Unknown value!");
  }
?>
