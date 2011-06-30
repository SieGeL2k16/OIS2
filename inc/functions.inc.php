<?php
/**
 * All global functions.
 *
 * - getClientIp()              => Retrieves user's current IP address.
 * - getmicrotime()             => Returns microtime.
 * - FormatTime()               => Formats given seconds into H:MM:SS.
 * - FormatNumber()             => Wrapper for number_format().
 * - UTF8ToISO()                => Converts UTF-8 string to ISO-8859-1 character set.
 * - ISOToUTF8()                => Converts ISO-8859-1 string to UTF-8 character set.
 * - FormatSize()               => Returns the passed bytes formatted with unit info.
 * - sgl_strftime()             => Wrapper for strftime().
 * - ReadDirectory()            => Recursive scanning of a given directory with optional pattern matching.
 * @package OIS2
 * @subpackage Includes
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.00 (21-Mar-2009)
 * $Id$
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
 * System check function.
 * We check here for the php version, and if it is >= 5.1.2 we check for the privileged_connect setting.
 * Also additonal system informations is read, if available.
 */
function checkSystemData()
  {
  $rc = array();
  $rc['PHPVERSION'] = phpversion();
  $is512 = version_compare($rc['PHPVERSION'],'5.1.2');
  if($is512 >= 0)
    {
    $rc['PRIV_CONNECT'] = ini_get('oci8.privileged_connect');
    }
  else
    {
    $rc['PRIV_CONNECT'] = FALSE;
    }
  if(function_exists('posix_uname'))
    {
    $data = posix_uname();
    $rc['SYSNAME'] = $data['sysname'];
    $rc['NODENAME']= $data['nodename'];
    $rc['RELEASE'] = $data['release'];
    $rc['VERSION'] = $data['version'];
    $rc['MACHINE'] = $data['machine'];
    }
  return($rc);
  }

/**
 * Recursive reading of a directory.
 * @param string $path The directory to read.
 * @param boolean $withpath Set to FALSE to store only filenames, else complete path is stored (default).
 * @param string $pattern Optional a pattern to include only files matching this pattern.
 * @return array A non-sorted array of all found files
 */
function ReadDirectory($path, $withpath = TRUE, $pattern = '*')
  {
  $retval = array();
  if ($dir = @opendir($path))
    {
    while (false !== ($file = @readdir($dir)))
      {
      if ($file[0]=="." || $file[0] == "..") continue;
      if (is_dir($path."/".$file))
        {
        $retval = @array_merge($retval,ReadDirectory($path."/".$file));
        }
      else if (is_file($path."/".$file))
        {
        if($pattern != '*')
          {
          if(!preg_match('/'.$pattern.'/',$file))
            {
            continue;
            }
          }
        if($withpath == TRUE)
          {
          $retval[]=$path."/".$file;
          }
        else
          {
          $retval[]=$file;
          }
        }
      }
    @closedir($dir);
    }
  return $retval;
  }

/**
 * Prints out the header div with title tag.
 * @param string $title The title to display.
 */
function printHeader($title)
  {
  echo('<div id="page_header">');
  echo('<h1>'.$title.'</h1>');
  echo('</div>'."\n");
  }

/**
 * Function is used to inform the user about something.
 * @param string $infostring The text to display to the user
 * @param string $target Optional url to use as target. If ommited no link is actually displayed.
 */
function InformUser($infostring,$target='')
  {
  echo("<div id=\"informUser\">\n");
  echo("<div class=\"ui-widget\">\n");
  echo("<div class=\"ui-state-highlight ui-corner-all\" style=\"padding: 0 .7em;\">\n");
  echo("<p>".$infostring."</p>\n");
  if($target != '')
    {
    echo("<br><a href=\"".$target."\" class=\"fakebtn\">Continue</a>\n<br>\n");
    }
  echo("</div>\n</div>\n</div>\n");
  }

/**
 * Function is used to inform the user about errors.
 * @param string $infostring The text to display to the user
 * @param string $target Optional url to use as target. If ommited no link is actually displayed.
 * @param boolean $include_header TRUE if a complete HTML skeleton should be printed, else FALSE (default).
 */
function Error($errorstring,$target='',$include_header = FALSE)
  {
  global $db;
  if($include_header == TRUE)
    {
    echo("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n");
    echo("<html lang=\"en\">\n");
    echo("<head>\n<title>ERROR OCCURED!</title>\n");
    echo('<link type="text/css" href="css/styles.css" rel="stylesheet" />'."\n");
    echo('<link type="text/css" href="css/'.UI_THEME.'" rel="stylesheet" />'."\n");
    echo("</head>\n<body>\n");
    }
  echo("<div id=\"informUser\">\n");
  echo("<div class=\"ui-widget\">\n");
  echo("<div class=\"ui-state-error ui-corner-all\" style=\"padding: 0 .7em;\">\n");
  echo("<p>".$errorstring."</p>\n");
  if($target != '')
    {
    echo("<br><a href=\"".$target."\" class=\"fakebtn\">Continue</a>\n<br>\n");
    }
  echo("</div>\n</div>\n</div>\n");
  if($include_header == TRUE)
    {
    echo("</body>\n</html>\n");
    }
  }

/**
 * Reads the meta description files for a given extension.
 * Returns the parsed informations from the file or an empty array if anything goes wrong.
 * @param string $extdir The name of the extension directory.
 * @param string $fname The full filename to the meta description file.
 * @return array The parsed data or an empty array if an error occured during parsing.
 */
function ReadExtInfo($extdir,$fname)
  {
  $known_tags = array('EXTENSION','MENUNAME','VERSION','AUTHOR','SCRIPTNAME');
  $arr = array();
  $fdata = file($fname,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  for($i = 0; $i < count($fdata); $i++)
    {
    $dummy = explode('=',$fdata[$i]);
    $tname = trim(StrToUpper($dummy[0]));
    if(in_array($tname,$known_tags))
      {
      if($tname == 'SCRIPTNAME')
        {
        $arr[$tname] = 'ext/'.$extdir.'/'.trim($dummy[1]);
        }
      else
        {
        $arr[$tname] = trim($dummy[1]);
        }
      }
    }
  return($arr);
  }

/**
 * Sorts the list of plugins based on the plugin menu name.
 * @param array $a One array member.
 * @param array $b And the other to compare the field "MENUNAME".
 * @return integer Return value of strnatcasecmp().
 * @see sessionheader.inc.php
 * @see strnatcasecmp
 */
function sort_plugins($a,$b)
  {
  return(strnatcasecmp($a['MENUNAME'],$b['MENUNAME']));
  }
?>
