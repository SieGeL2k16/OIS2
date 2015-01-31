<?php
/**
 * AJAX backend for jobs plugin.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.02 (21-Jul-2014)
 * $Id: ajax.php 9 2013-07-20 06:34:13Z siegel $
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
        $jobdata      = $retdata['o'];
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
        if(is_array($rc) === FALSE)
          {
          $oerr = $db->GetSQLError();
          echo(json_encode(array('ERROR' => $oerr['msg'])));
          $state = 'FAILED: ORA-'.$rc;
          }
        else
          {
          echo(json_encode($retdata));
          $state = 'OKAY';
          }
        $db->Commit();
        $db->Disconnect();
        WriteLog(sprintf("JOBS: \"%s@%s\" %s %s [%s]",$_SESSION['DBUSER'],$_SESSION['TNSNAME'],$dummy,$jobdata,$state));
        exit;

  default:
        die("ERROR: Unknown value!");
  }
?>
