<?php
/**
 * AJAX backend for Flashback/Recyclebin plugin.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.01 (19-Jul-2014)
 * $Id$
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');
$MODE = $SGLFUNC->GetRequestParam('MODE');
if($MODE == '')
  {
  echo(json_encode(array('ERROR' => 'ERROR: "MODE" VALUE IS EMPTY - ABORTING !!')));
  exit;
  }
switch($MODE)
  {
  /** Perform FLASHBACK TABLE <TNAME> TO BEFORE DROP */
  case  'U':
        $tname= sprintf("%s.%s",$SGLFUNC->GetRequestParam('OWNER'),$SGLFUNC->GetRequestParam('ORG'));
        $SQL  = sprintf('FLASHBACK TABLE %s TO BEFORE DROP',$tname);
        $rc  = $db->Query($SQL,OCI_ASSOC,1);
        if(is_array($rc) === false)
          {
          $oerr = $db->GetSQLError();
          echo(json_encode(array('ERROR' => $oerr['msg'])));
          }
        else
          {
          echo(json_encode(array('OK' => 1)));
          }
        $db->Disconnect();
        break;


  case  'P':
        $SQL = sprintf("PURGE %s %s.\"%s\"",$SGLFUNC->GetRequestParam('TTYPE'),$SGLFUNC->GetRequestParam('OWNER'),$SGLFUNC->GetRequestParam('OBJ'));
        $rc  = $db->Query($SQL,OCI_ASSOC,1);
        if(is_array($rc) === false)
          {
          $oerr = $db->GetSQLError();
          echo(json_encode(array('ERROR' => $oerr['msg'])));
          }
        else
          {
          echo(json_encode(array('OK' => 1)));
          }
        $db->Disconnect();
        exit;

  default:
        echo(json_encode(array('ERROR' => 'ERROR: Unknown "MODE" value!')));
  }
?>
