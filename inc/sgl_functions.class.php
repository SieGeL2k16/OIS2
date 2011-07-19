<?php
/**
 * This class provides a bunch of useful methods for every-day coding.
 * NOTE: This class works ONLY (!) with PHP 5+ !!
 * @package sgl_functions
 * @version 0.15 (03-Jun-2011)
 * $Id$
 * @license http://opensource.org/licenses/bsd-license.php BSD License
 * @filesource
 */

/**
 * Main class definition.
 * @package sgl_functions
 */
class sgl_functions
  {
  /**
   * Class version.
   * @private
   * @var string
   */
  private $classversion = '0.16';

  /**
   * Windows timestamp correction value.
   * Has to be substracted from the original 64-bit value and divided by 1000000 to get the UNIX timestamp.
   */
  const win_ts_factor = 116444736000000000;

  /**
   * Locale settings for formatting numbers etc.
   * @public
   * @var array
   */
  public $arr_localeconv  = array();

  /**#@+
   * Characters for both decimal points and grouping chars.
   */
  public $decimal;
  public $grouping;
  /**#@-*/

  /**
   * Class constructor.
   * Loads locale data from localeconv() to $arr_localeconv;
   * @see localeconv()
   */
  public function __construct()
    {
    $this->arr_localeconv = localeconv();
    if(!defined('LOC_DECIMAL'))
      {
      $this->decimal = $this->arr_localeconv['decimal_point'];
      }
    else
      {
      $this->decimal = LOC_DECIMAL;
      }
    if(!defined('LOC_GROUPING'))
      {
      $this->grouping = $this->arr_localeconv['thousands_sep'];
      }
    else
      {
      $this->grouping = LOC_GROUPING;
      }
    }

  /**
   * Returns class version.
   * @return string The class version in format VERSION.REVISION.
   */
  public function GetClassVersion()
    {
    return($this->classversion);
    }

  /**
   * Determines Client IP address.
   * @return string IP address (unknown IPs are returned as 127.0.0.1)
   */
  public static function getClientIp()
    {
    $return = ( !empty($_SERVER['REMOTE_ADDR']) ) ? $_SERVER['REMOTE_ADDR'] : ( ( !empty($_ENV['REMOTE_ADDR']) ) ? $_ENV['REMOTE_ADDR'] : $REMOTE_ADDR );
    list($return) = explode(",", $return);
    if($return=="")   $return = "127.0.0.1";
    return $return;
    }

  /**
   * Prints out a HTML4 DOCTYPE header.
   */
  public static function DocType()
    {
    echo('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">');
    echo("\n");
    }

  /**
   * Returns microtime, useful for benchmarks.
   * @return float current time in seconds.microseconds.
   */
  public static function getmicrotime()
    {
    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);
    }

  /**
   * Returns given seconds in HH:MM:SS format.
   * @param $seconds integer The number of seconds to convert.
   * @return string The formatted time in format HH:MM:SS
   */
  public static function FormatTime($seconds)
  	{
  	return(sprintf("%02.2d:%02.2d.%02.2d",($seconds / 3600),(($seconds / 60) >= 60) ? ($seconds - ((floor($seconds / 3600) * 3600))) / 60 : ($seconds / 60),($seconds % 60)));
  	}

  /**
   * Wrapper for number_format which only needs number of digits to show with default = 0.
   * Requires defines LOC_GROUPING and LOC_DECIMAL, else "decimal_point" and "thousands_sep" keys from localeconv() are used.
   * @param integer $value Value to convert.
   * @param integer $digits Number of digits to show, defaults to 0.
   * @return string Converted value
   * @see localeconv()
   */
  public function FormatNumber($value,$digits = 0)
    {
    return(number_format($value,$digits,$this->decimal,$this->grouping));
    }

  /**
   * Recursive reading of a directory.
   * @param string $path The directory to read.
   * @return array A sorted array of all found files.
   */
  public function walk_dir($path)
    {
    if ($dir = @opendir($path))
      {
      $retval = array();
      while (false !== ($file = @readdir($dir)))
        {
        if ($file[0]==".")
          {
          continue;
          }
        if (is_dir($path."/".$file))
          {
          $retval = array_merge($retval,$this->walk_dir($path."/".$file));
          }
        else if (is_file($path."/".$file))
          {
          $retval[]=$path."/".$file;
          }
        }
      @closedir($dir);
      }
    sort($retval);
    return $retval;
    }

  /**
   * Function retrieves current limit in bytes for uploading files.
   * @return integer Amount of bytes allowed to upload.
   */
  public static function GetULLimits()
    {
    $maxpost   = @ini_get("post_max_size");
    $maxupload = @ini_get("upload_max_filesize");
    if(stristr($maxpost,"M"))
      {
      $multiply = 1048576;                            // MB
      }
    else
      {
      if(Stristr($maxpost,"k"))
        {
        $multiply = 1024;                             // KB
        }
      else
        {
        $multiply = 1;                                // Just bytes ;)
        }
      }
    if(stristr($maxupload,"M"))
      {
      $multiply = 1048576;                            // MB
      }
    else
      {
      if(Stristr($maxuplad,"k"))
        {
        $multiply = 1024;                             // KB
        }
      else
        {
        $multiply = 1;                                // Just bytes ;)
        }
      }
    $maxupload=intval($maxupload) * $multiply;
    $maxpost=intval($maxpost) * $multiply;
    $dummy = min($maxupload,$maxpost);
    return($dummy);
    }

  /**
   * Returns formatted bytes (either as kB, MB etc.).
   * @param integer $bytes Bytes to convert.
   * @param boolean $HTML If TRUE uses &nbsp; as separator, else a normal whitespace.
   * @return string Formatted bytes with best matching unit.
   */
  public function FormatSize($bytes,$HTML=TRUE)
  	{
    $einheit=array("bytes","kB","MB","GB","TB","PB","EB");
    $ecnt = 0;
    $check = floatval($bytes);
    while(abs($check) >= 1024)
      {
      $check = $check / 1024;
      $ecnt++;
      }
    if($ecnt)
      {
      $check = $this->FormatNumber(round($check,2),2);
      }
    if($HTML == TRUE)
      {
      $sep = '&nbsp;';
      }
    else
      {
      $sep = ' ';
      }
    $retstring = $check.$sep.$einheit[$ecnt];
    return($retstring);
  	}

  /**
   * sgl_strftime() is a wrapper for PHP's own "strftime()" function with one additional parameter.
   * The additional supported parameter is "%@", which will be expanded as the current day number with ordinal suffix in the current language.
   * All other parameters are passed directly to strftime() without further parsing.
   * @param string $fmt The format string to return, see strftime docs for additional informations.
   * @param integer $ts Optional timestamp to convert. If you omit this parameter the current date and time will be used.
   * @return string The converted datestring.
   * @see strftime
   */
  public static function sgl_strftime($fmt, $ts = 0)
    {
    $lang = setLocale(LC_ALL,"0");        // Determine current language
    $ords = array();
    // First make sure that we have a valid timestamp:
    if(!$ts)
      {
      $use_ts = time();
      }
    else
      {
      $use_ts = $ts;
      }
    // Now check for our "special" parameter and substitute it with the necessary value, so that we can pass then the modified $fmt to strftime():
    if(preg_match("/\%@/",$fmt))
      {
      if(substr($lang,0,2) == 'de')         // check if we have german:
        {
        $day = date('d.',$use_ts);
        }
      else                                  // No, so english "special" please:
        {
        $day = date('dS',$use_ts);
        }
      $fmt = Str_Replace("%@",$day,$fmt);
      }
    return(strftime($fmt,$use_ts));
    }

  /**
   * Returns a ISO 8601 conform date (used i.e. in XML feeds)
   * @param integer $ts The Unix timestamp to use for creating the date, 0 to use current date.
   * @return string The ISO 8601 conform date representation.
   * @see date
   */
  public static function TS2ISO8601($ts = 0)
    {
    if(!$ts)
      {
      return(date('c'));
      }
    else
      {
      return(date('c',$ts));
      }
    }

  /**
   * Function prints out an image with an error text if anything goes wrong.
   * @param string $string Error string to be displayed.
   * @param integer $myX Width of error-picture.
   * @param integer $myY Height of error-picture.
   */
  public static function PrintImageError($string,$myX,$myY)
    {
  	@header("Content-Type: image/png\n");
    $im = @ImageCreate($myX,$myY);
    $red = @ImageColorAllocate($im,200,200,200);
    $white = @ImageColorAllocate($im,255,255,255);
    $black = @ImageColorAllocate($im,0,0,0);
    @ImageRectangle($im,1,1,$myX-2,$myY-2,$black);
    $px = (@ImageSX($im)-6.0*strlen($string))/2;
    $py = (@ImageSY($im)-14)/2;
    @ImageString($im,2,$px,$py,$string,$black);
    @ImagePNG($im);
    @ImageDestroy($im);
    exit;
    }

  /**
   * Function returns the file permissions for a given filename (taken from www.php.net).
   * @param integer $mode The file permissions as returned from fileperms().
   * @return string The allowed permissions as flags.
   * @see fileperms()
   */
  public static function GetPerms( $mode )
    {
    $retbuf = "";
    /* Determine Type */
    if( $mode & 0x1000 )       $type='p'; /* FIFO pipe */
    else if( $mode & 0x2000 )  $type='c'; /* Character special */
    else if( $mode & 0x4000 )  $type='d'; /* Directory */
    else if( $mode & 0x6000 )  $type='b'; /* Block special */
    else if( $mode & 0x8000 )  $type='-'; /* Regular */
    else if( $mode & 0xA000 )  $type='l'; /* Symbolic Link */
    else if( $mode & 0xC000 )  $type='s'; /* Socket */
    else $type='u';                       /* UNKNOWN */
    /* Determine permissions */
    $owner['read']    = ($mode & 00400) ? 'r' : '-';
    $owner['write']   = ($mode & 00200) ? 'w' : '-';
    $owner['execute'] = ($mode & 00100) ? 'x' : '-';
    $group['read']    = ($mode & 00040) ? 'r' : '-';
    $group['write']   = ($mode & 00020) ? 'w' : '-';
    $group['execute'] = ($mode & 00010) ? 'x' : '-';
    $world['read']    = ($mode & 00004) ? 'r' : '-';
    $world['write']   = ($mode & 00002) ? 'w' : '-';
    $world['execute'] = ($mode & 00001) ? 'x' : '-';
    /* Adjust for SUID, SGID and sticky bit */
    if( $mode & 0x800 ) $owner['execute'] = ($owner['execute']=='x') ? 's' : 'S';
    if( $mode & 0x400 ) $group['execute'] = ($group['execute']=='x') ? 's' : 'S';
    if( $mode & 0x200 ) $world['execute'] = ($world['execute']=='x') ? 't' : 'T';
    $retbuf = sprintf("%1s", $type);
    $retbuf.= sprintf("%1s%1s%1s", $owner['read'], $owner['write'], $owner['execute']);
    $retbuf.= sprintf("%1s%1s%1s", $group['read'], $group['write'], $group['execute']);
    $retbuf.= sprintf("%1s%1s%1s", $world['read'], $world['write'], $world['execute']);
    return($retbuf);
    }

  /**
   * Calculates size of a given directory by traversing it and counting all file sizes.
   * @param string $dirname Name of directory to count.
   * @return integer Size of all found files in bytes.
   */
  public function CalcDirSize($dirname)
    {
    $files = $this->walk_dir($dirname);
    $fsize = 0;
    for($i = 0; $i < count($files); $i++)
      {
      $fsize+=filesize($files[$i]);
      }
    return($fsize);
    }

  /**
   * Returns a string ready for usage with overlib.js functionality.
   * Currently only the LF and CR characters are removed/replaced and quotes are replaced by entity/backslashes.
   * @param string $istr The original string to convert.
   * @return string The converted string which can be inserted to the overlib call.
   */
  public static function Str2Overlib($istr)
    {
    $search = array("/\n/" ,"/'/" ,"/\"/");
    $replace= array('' ,"\'"  ,"&quot;");
    $dummy = str_replace("\r","",$istr);
    return(preg_replace($search,$replace,$dummy));
    }

  /**
   * Creates an array of HTML color definitions.
   * This is extremly useful for JPGraph rendering with prefilled colors.
   * @param integer $howmany How many color values you need.
   * @return array A numbered array with the requested number of color values as elements.
   */
  public static function CreateColors($howmany = 3)
    {
    $rgb  = array('#ff0000','#00ff00','#0000ff','#ffff00','#773366','#9999ff','#dddddd','#ff00ff','#ff8000','#FFFFFF','#800080');
    if($howmany <= count($rgb)) return(array_slice($rgb,0,$howmany));
    for($i = count($rgb); $i < $howmany; $i++)
      {
      do
        {
        $dummy = sprintf("#%06X",mt_rand(1,0xFFFFFF));
        }
      while(in_array($dummy,$rgb));
      $rgb[$i] = $dummy;
      }
    return($rgb);
    }

  /**
   * Tries to determine the language used for the website or web browser.
   * First checks for existance of the LANG get/post parameter, if not set checks for Browser's prefered language.
   * If no valid language could be determined switches automatically to english (en_US).
   * Note: Supports currently only english/german. You are encouraged to update this for your own language and send the change to me! :)
   * @return array A two-dimensional array with index 0 => Language tag for setLocale() and index 1 => Language tag for lang="" tags in HTML.
   */
  public static function GetBrowserLang()
    {
    $accept_language = '';
    $useLang = array();
    if(isset($_REQUEST['LANG']))
      {
      $accept_language = StrToLower(substr(strip_tags($_REQUEST['LANG']),0,2));
      }
    elseif(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
      {
      if(strchr($_SERVER['HTTP_ACCEPT_LANGUAGE'],',')==true)   // More than one language is defined, use prefered one (1st)
        {
        $dummy = explode(',',StrToLower(strip_tags($_SERVER['HTTP_ACCEPT_LANGUAGE'])));
        $accept_language = substr($dummy[0],0,2);
        }
      else
        {
        $accept_language = StrToLower(strip_tags($_SERVER['HTTP_ACCEPT_LANGUAGE']));
        }
      }
    switch($accept_language)
      {
      case  'de':
      case  'de-de':
      case  'de_de':
            $useLang[0] = "de_DE";
            $useLang[1] = "de";
            define('LOC_GROUPING' , '.');
            define('LOC_DECIMAL'  , ',');
            break;
      default:
            $useLang[0] = "en_US";
            $useLang[1] = "en";
            define('LOC_GROUPING' , ',');
            define('LOC_DECIMAL'  , '.');
            break;
      }
    setLocale(LC_ALL, $useLang[0]);
    return($useLang);
    }

  /**
   * Prints out a dump of a given variable in a pre container.
   * @param mixed $var Variable to dump on screen.
   * @param boolean $useDump If TRUE var_dump() instead of print_r() is used. Defaults to FALSE.
   */
  public static function Debug($var,$useDump = FALSE)
    {
    echo("<div style=\"margin-top: 5px;border: 1px dotted black;padding: 5px;\">\n<pre>\n");
    if($useDump == FALSE)
      {
      print_r($var);
      }
    else
      {
      var_dump($var);
      }
    echo("</pre>\n</div>\n");
    }

  /**
   * Converts a Windows 64 Bit Timestamp value to an unix 32-bit timestamp.
   * @param integer $wints The original windows timestamp.
   * @return integer The Unix Timestamp (32-bit value).
   */
  public static function WinTS2UnixTS($wints)
    {
    return(($wints - sgl_functions::win_ts_factor) / 10000000);
    }

  /**
   * Converts an 32-bit unix timestamp to a Windows 64-Bit timestamp value.
   * @param integer $unixts The unix timestamp (i.e. returned from time()).
   * @return integer The Windows 64 Bit timestamp value.
   */
  public static function UnixTS2WinTS($unixts)
    {
    return(floatval(($unixts * 10000000) + sgl_functions::win_ts_factor));
    }

  /**
   * Creation of thumbnail from picture source.
   * @param integer $type Type of image, can be one of IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG.
   * @param string $source Full path to picture to create thumbnail from.
   * @param string $dest Full path where thumbnail should be written to.
   * @param integer $twidth Width of thumbnail in pixel.
   * @param integer $theihgt Height of thumbnail in pixel.
   * @param integer $quality Quality setting for JPEG in % (0-100).
   * @return boolean FALSE if there was an error, TRUE if all was okay.
   */
  public function CreateDiscThumbNail($type,$source,$dest,$twidth,$theight,$quality)
    {
    switch($type)
      {
      case  IMAGETYPE_JPEG:
            $source_image = ImageCreateFromJPEG($source);
            break;
      case  IMAGETYPE_GIF:
            $source_image = ImageCreateFromGIF($source);
            break;
      case  IMAGETYPE_PNG:
            $source_image = ImageCreateFromPNG($source);
            break;
      default:
            $this->last_error = "Unsupported Image format!\n";
            return(false);
      }
    if(!$source_image)
      {
      $this->last_error = "Unable to load picture ".$source." !!!\n";
      return(false);
      }
    $thumbnail = ImageCreateTrueColor(intval($twidth),intval($theight));
    if(!$thumbnail)
      {
      ImageDestroy($source_image);
      $this->last_error = "Unable to create thumbnail picture!!!";
      return(false);
      }
    if(!ImageCopyResized($thumbnail,$source_image,0,0,0,0,intval($twidth),intval($theight),ImageSX($source_image),ImageSY($source_image)))
      {
      ImageDestroy($thumbnail);
      ImageDestroy($source_image);
      $this->last_error = "Error while scaling picture!\n";
      return(false);
      }
    $rc = FALSE;
    switch($type)
      {
      case  IMAGETYPE_JPEG:
            $rc = ImageJPEG($thumbnail,$dest,$quality);
            break;
      case  IMAGETYPE_GIF:
            $rc = ImageGIF($thumbnail,$dest);
            break;
      case  IMAGETYPE_PNG:
            $rc = ImagePNG($thumbnail,$dest);
            break;
      }
    ImageDestroy($thumbnail);
    ImageDestroy($source_image);
    return($rc);
    }

  /**
   * Retrival of string arguments from _REQUEST.
   * @param string $str_pname The name of parameter you want to get.
   * @param string $str_default Optionally a default parameter
   * @return string The parameter fetched or the default value.
   */
  public static function GetRequestParam($str_pname,$str_default = '')
    {
    return((isset($_REQUEST[$str_pname])==TRUE) ? strip_tags($_REQUEST[$str_pname]) : $str_default);
    }

  /**
   * Retrival of integer arguments from _REQUEST.
   * @param string $str_pname The name of parameter you want to get.
   * @param integer $int_default Optionally a default parameter
   * @return integer The parameter fetched or the default value.
   */
  public static function GetRequestParamInt($str_pname,$int_default = 0)
    {
    return((isset($_REQUEST[$str_pname])==TRUE) ? intval($_REQUEST[$str_pname]) : $int_default);
    }

  /**
   * Method merges a directory and a filename together using the separator for the running OS.
   * @param string $str_dir The directory name.
   * @param string $str_fname the filename to add.
   * @return string The merged filename.
   */
  public static function MergePath($str_dir,$str_fname)
    {
    if(substr($str_dir,strlen($str_dir)-1,1) != DIRECTORY_SEPARATOR)
      {
      return($str_dir.DIRECTORY_SEPARATOR.$str_fname);
      }
    else
      {
      return($str_dir.$str_fname);
      }
    }

  } // End-of-Class
?>
