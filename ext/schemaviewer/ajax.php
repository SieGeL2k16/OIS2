<?php
/**
 * Extension: Schema Viewer.
 * AJAX backend for schema viewer, loads and populates the various form elements.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.00 (24-Dec-2010)
 * $Id$
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
/**
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');
$mode = (isset($_GET['MODE'])) ? strip_tags($_GET['MODE']) : '';
if($mode == '')
  {
  $db->Disconnect();
  die("ERROR: Invalid Mode parameter value!");
  }
switch($mode)
  {
  /* Returns a list of available users inside the database */
  case  'SCHEMA':
        $unames = array();
        $uquery='SELECT USERNAME FROM ALL_USERS ORDER BY USERNAME';
        $db->QueryResult($uquery);
        while($u = $db->FetchResult())
          {
          $unames[]=$u['USERNAME'];
          }
        $db->FreeResult();
        echo(json_encode($unames));
        break;

  /* Returns list of objects for a given username */
  case  'OBJECTS':
        $user = (isset($_GET['USER'])==TRUE) ? strip_tags($_GET['USER']) : '';
        $objects = array();
        if($user != '')
          {
          $sp = array('u' => $user);
          $db->QueryResultHash("SELECT OBJECT_TYPE,COUNT(*) AS ANZ FROM ALL_OBJECTS WHERE OWNER=:u GROUP BY OBJECT_TYPE ORDER BY OBJECT_TYPE",$sp);
          while($o = $db->FetchResult())
            {
            $objects[]=$o;
            }
          $db->FreeResult();
          }
        echo(json_encode($objects));
        break;

  /* Returns list of objects for a given object type and username */
  case  'LIST':
        $user   = (isset($_GET['USER'])==TRUE)    ? strip_tags($_GET['USER']) : '';
        $object = (isset($_GET['OBJECT'])==TRUE)  ? strip_tags($_GET['OBJECT']) : '';
        if($user == "" || $object == "")
          {
          $db->Disconnect();
          die("ERROR: Either USER or OBJECT or both are empty!!!");
          }
        if($object != '---')
          {
          $sp     = array('un' => $user, 'obj' => $object);
          $query  = "SELECT OWNER,OBJECT_NAME,SUBOBJECT_NAME,OBJECT_TYPE,TO_CHAR(CREATED,'DD-Mon-YYYY HH24:MI:SS') AS CD,TO_CHAR(LAST_DDL_TIME,'DD-Mon-YYYY HH24:MI:SS') AS MD,TIMESTAMP,STATUS,DECODE(TEMPORARY,'Y','YES','N','NO') AS ISTEMP,DECODE(GENERATED,'Y','YES','N','NO') AS ISGEN,SECONDARY FROM ALL_OBJECTS WHERE OWNER=:un AND OBJECT_TYPE=:obj ORDER BY OBJECT_NAME";
          }
        else
          {
          $sp     = array('un' => $user);
          $query  = "SELECT OWNER,OBJECT_NAME,SUBOBJECT_NAME,OBJECT_TYPE,TO_CHAR(CREATED,'DD-Mon-YYYY HH24:MI:SS') AS CD,TO_CHAR(LAST_DDL_TIME,'DD-Mon-YYYY HH24:MI:SS') AS MD,TIMESTAMP,STATUS,DECODE(TEMPORARY,'Y','YES','N','NO') AS ISTEMP,DECODE(GENERATED,'Y','YES','N','NO') AS ISGEN,SECONDARY FROM ALL_OBJECTS WHERE OWNER=:un ORDER BY OBJECT_NAME";
          }
        /*
          11g adds two columns to the ALL_OBJECTS view:
            NAMESPACE     NUMBER NOT NULL Namespace for the object
            EDITION_NAME  VARCHAR2(30)    Name of the edition in which the object is actual
        */
        $db->QueryResultHash($query,$sp);
        $list = array();
        while($d = $db->FetchResult())
          {
          $list[]=$d;
          }
        $db->FreeResult();
        echo(json_encode($list));
        break;
  }
$db->Disconnect();
?>

