<?php
/**
 * Extension: Show Users.
 * Displays details for a given username.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.00 (20-Jul-2010)
 * $Id$
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
/**
 */
define('IS_EXTENSION' , 1);
require_once('../../inc/sessionheader.inc.php');

$uid = (isset($_GET['UID'])) ? strip_tags($_GET['UID']) : -1;

// Retrieve the meta data for our extension:
$extdata = $OIS2EXT->GetExtInfo($OIS_EXTENSIONS,'show_users.php');
$OIS2EXT->Add_JS_Ready_Call('$(".btn").button();');
$OIS2EXT->PrintExtHeader('Show user details for USER_ID='.$uid,'',TRUE);
?>
<div id="fullpage_content">
<?php
// 8i/9i DBA_USERS:
/*
USERNAME                    VARCHAR2(30) NOT NULL           Name of the user
USER_ID                     NUMBER NOT NULL                 ID number of the user
PASSWORD                    VARCHAR2(30)                    Encrypted password
ACCOUNT_STATUS              VARCHAR2(30) NOT NULL           Indicates if the account is locked, expired, or unlocked
LOCK_DATE                   DATE                            Date the account was locked if account status was locked
EXPIRY_DATE                 DATE                            Date of expiration of the account
DEFAULT_TABLESPACE          VARCHAR2(30) NOT NULL           Default tablespace for data
TEMPORARY_TABLESPACE        VARCHAR2(30) NOT NULL           Default tablespace for temporary table
CREATED                     DATE NOT NULL                   User creation date
PROFILE                     VARCHAR2(30) NOT NULL           User resource profile name
INITIAL_RSRC_CONSUMER_GROUP VARCHAR2(30)                    The initial resource consumer group for the user
EXTERNAL_NAME               VARCHAR2(4000)                  User external name

11g adds the following:

PASSWORD_VERSIONS           VARCHAR2(8)                     Database version in which the password was created or changed
EDITIONS_ENABLED            VARCHAR2(1)                     Indicates whether editions have been enabled for the corresponding user (Y) or not (N)
AUTHENTICATION_TYPE         VARCHAR2(8)                     Indicates the authentication mechanism for the user:
                                                            ¦ EXTERNAL - CREATE USER user1 IDENTIFIED EXTERNALLY;
                                                            ¦ GLOBAL - CREATE USER user2 IDENTIFIED GLOBALLY;
                                                            ¦ PASSWORD - CREATE USER user3 IDENTIFIED BY user3;
*/
if($OIS2EXT->Get_Oracle_Version() >= 11)
  {
  $addq = ',PASSWORD_VERSIONS,EDITIONS_ENABLED,AUTHENTICATION_TYPE';
  }
else
  {
  $addq = '';
  }
$query = <<<EOM
SELECT USERNAME,
       USER_ID,
       PASSWORD,
       ACCOUNT_STATUS,
       TO_CHAR(LOCK_DATE,'DD-Mon-YYYY HH24:MI:SS') AS LOCK_DATE,
       TO_CHAR(EXPIRY_DATE,'DD-Mon-YYYY HH24:MI:SS') AS EXPIRY_DATE,
       DEFAULT_TABLESPACE,
       TEMPORARY_TABLESPACE,
       TO_CHAR(CREATED,'DD-Mon-YYYY HH24:MI:SS') AS CREATED,
       PROFILE,
       INITIAL_RSRC_CONSUMER_GROUP,
       EXTERNAL_NAME,
       (SELECT COUNT(*) FROM DBA_OBJECTS do WHERE do.OWNER = USERNAME) AS OBJECTS_OWNED,
       (SELECT COUNT(*) FROM DBA_ROLE_PRIVS WHERE GRANTEE = USERNAME) AS ROLECNT
$addq
  FROM DBA_USERS
 WHERE USER_ID=:userid
EOM;
$sp = array('userid' => $uid);
$data = $db->QueryHash($query,OCI_ASSOC,0,$sp);
?>
<table cellspacing="1" cellpadding="2" border="0" class="datatable" width="98%" summary="User details">
<caption>Details for User &quot;<?php echo($data['USERNAME']);?>&quot;</caption>
<tbody>
<?php
$lv = 0;
foreach($data as $key => $val)
  {
  if($lv % 2)
    {
    $myback = 'td_odd';
    }
  else
    {
    $myback = 'td_even';
    }
  if($val == '') $val = '---';
  echo("<tr class=\"".$myback."\">\n");
  echo("  <td>".htmlentities(UCWords(StrToLower(str_replace("_"," ",$key))),ENT_COMPAT,'utf-8')."</td>\n");
  echo("  <td>".htmlentities($val,ENT_COMPAT,'utf-8')."</td>\n");
  echo("</tr>\n");
  $lv++;
  }
?>
</tbody>
</table>
<br>

<?php
$OIS_IMG = $OIS2EXT->Get_OIS2_Image_URL();
$yesno = array( 'YES' => '<img src="'.$OIS_IMG.'tick.png" alt="Yes" border="0" title="Yes">',
                'NO'  => '<img src="'.$OIS_IMG.'cross.png" alt="No" border="0" title="No">'
              );
$sp = array('uname' => $data['USERNAME']);

// Read out the roles assigned for selected user and display on screen:
$t0 = array
  (
  'CAPTION'     => sprintf("%s assigned role(s) for user &quot;%s&quot;",$data['ROLECNT'],$data['USERNAME']),
  'THEAD'       => array('Granted role','Admin option?','Default role?'),
  'DATA_LOOKUP' => array('1' => $yesno, '2' => $yesno)
  );
$OIS2EXT->RenderQuery("SELECT GRANTED_ROLE,ADMIN_OPTION,DEFAULT_ROLE FROM DBA_ROLE_PRIVS WHERE GRANTEE = :uname ORDER BY GRANTED_ROLE",$sp,$t0);

// Read out the system privileges for selected user and display on screen:
$table_options = array();
$table_options['THEAD']       = array("System privilege","Admin option?");
$table_options['DATA_LOOKUP'] = array('1' => $yesno);
$table_options['CAPTION']     = sprintf('List of system privileges assigned to user &quot;%s&quot;',$data['USERNAME']);
$query = "SELECT PRIVILEGE,ADMIN_OPTION FROM DBA_SYS_PRIVS WHERE GRANTEE = :uname ORDER BY PRIVILEGE";
$OIS2EXT->RenderQuery($query,$sp,$table_options);
echo("<br>\n");

// Read out the object privileges for selected user and display on screen:
$t1 = array
 (
 'THEAD'        => array('Object privilege','Owner','Object','Grant option'),
 'DATA_LOOKUP'  => array('3' => $yesno),
 'CAPTION'      => sprintf('List of object privileges assigned to user &quot;%s&quot;',$data['USERNAME'])
 );
$OIS2EXT->RenderQuery("SELECT PRIVILEGE,OWNER,TABLE_NAME,GRANTABLE FROM DBA_TAB_PRIVS WHERE GRANTEE = :uname ORDER BY PRIVILEGE",$sp,$t1);

// Read quota on tablespaces:
$t2 = array (
  'THEAD'       => array('Tablespace','Quota','Value'),
  'CAPTION'     => 'Quota'
);
$OIS2EXT->RenderQuery("SELECT TABLESPACE_NAME,DECODE(MAX_BYTES,-1,'Unlimited','Value') AS QUOTATEXT,MAX_BYTES from DBA_TS_QUOTAS WHERE USERNAME = :uname",$sp,$t2);
?>
</div>
<?php
// Call this method to dump out the footer and disconnect from database:
$OIS2EXT->PrintExtFooter('<div align="center"><a href="javascript:self.close()" class="btn">Close window</a></div>');
?>
