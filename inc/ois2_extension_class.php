<?php
/**
 * OIS2 extension support class.
 * All extensions must use this class to build up the core HTML page elements.
 * This allows to easily update the look & feel of OIS2 without having the need to update all extensions, too.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.00 (31-Aug-2009)
 * $Id$
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

/**
 * OIS2 extension support Class.
 * @package OIS2_extension
 */
class OIS2_extension
  {
  public $JQUERY_READY = array();

  public $dbh;

  /**
   * Class constructor.
   */

  public function __construct(&$dbh)
    {
    $this->dbh = $dbh;
    }

  /**
   * Function returns metadata for current extension.
   * The current extension is determined by comparing the script name with the SCRIPTNAME tag in the extension array.
   * The array returned has the following keys: EXTENSION, MENUNAME, VERSION, AUTHOR, SCRIPTNAME.
   * @param string $extname Optional name of an extension. If none given the name is determined from $_SERVER['SCRIPT_FILENAME'].
   * @param array &$EXTARRAY The array of loaded extensions.
   * @return array The meta data or an empty array if no extension could be found.
   */
  public function GetExtInfo(&$EXTARRAY, $extname='')
    {
    $retarr = array();
    if($extname == '')
      {
      $ename = basename($_SERVER['SCRIPT_FILENAME']);
      }
    else
      {
      $ename = $extname;
      }
    foreach($EXTARRAY AS $en => $meta)
      {
      if(basename($meta['SCRIPTNAME']) == $ename)
        {
        return($meta);
        }
      }
    return($retarr);
    }

  /**
   * Function dumps out the HTML header until the open body tag.
   * Every extension should use ONLY (!) this method to create the necessary HTML head,
   * this way a consistent design is guaranteed and also updates to the design are automatically
   * applied to every extension without further interaction.
   * @param string $pagetitle The title to print on top of page.
   * @param string $addheader Optional additional tags to include in the <head> tag. You can pass i.e. additional style sheet commands with this parameter.
   * @param string $no_navigation Optional set to TRUE to disable the inclusion of a navigation, useful for popup windows.
   */
  public function PrintExtHeader($pagetitle,$addheader = '',$no_navigation = FALSE)
    {
    $ptitle = 'OIS '.SITE_VERSION.': '.$pagetitle;
    echo("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n");
    echo("<html lang=\"en\">\n");
    echo("<head>\n");
    echo("<title>".$ptitle." (".sprintf("%s@%s",$_SESSION['DBUSER'],$_SESSION['TNSNAME']).")</title>\n");
    require_once(OIS_INSTALL_PATH.'/inc/metatags.inc.php');
    if($addheader != '')
      {
      echo($addheader."\n");
      }
    if(count($this->JQUERY_READY))
      {
      echo("<script type=\"text/javascript\">\n");
      echo("\$(document).ready(function(){\n");
      for($i = 0; $i < count($this->JQUERY_READY); $i++)
        {
        echo($this->JQUERY_READY[$i]."\n");
        }
      echo("});\n");
      echo("</script>\n");
      }
    echo("</head>\n");
    echo("<body>\n");
    echo("<div id=\"page_header\">\n");
    echo("<h1>".$pagetitle."</h1>\n");
    echo("</div>\n");
    if($no_navigation == false)
      {
      require_once(OIS_INSTALL_PATH.'/navigation.inc.php');
      }
    }

  /**
   * Prints the footer on bottom of page.
   * Always use this method to "finish" your pages to provide a consistent design.
   * @param string $addstring Optional HTML content to be printed before the footer is displayed.
   */
  public function PrintExtFooter($addstring = '')
    {
    if($addstring != '')
      {
      echo($addstring);
      }

    require_once(OIS_INSTALL_PATH.'/inc/footer.inc.php');
    echo("</body>\n</html>\n");
    }

  /**
   * Prints the footer on bottom of tabbed pages.
   * Always use this method to "finish" your pages to provide a consistent design.
   * In difference to PrintExtFooter() no line won't be printed, as the jQuery UI Tabs already framed.
   * @param string $addstring Optional HTML content to be printed before the footer is displayed.
   */
  public function PrintExtTabFooter($addstring = '')
    {
    if($addstring != '')
      {
      echo($addstring);
      }
    $this->dbh->Disconnect();
    echo("</body>\n</html>\n");
    }

  /**
   * Returns the complete URL to the current defined JQuery.UI css file.
   * The theme can be changed in the file "defines.inc.php", see UI_THEME.
   * @return string The complete URL ready to include in the HEAD section.
   */
  public function Get_UI_Theme_Path()
    {
    return(OIS_INSTALL_URL.'/css/'.UI_THEME);
    }

  /**
   * Returns the URL to the images directory of OIS2.
   * @return string The URL
   */
  public function Get_OIS2_Image_URL()
    {
    return(sprintf('%s/images/',OIS_INSTALL_URL));
    }

  /**
   * Returns TRUE if current user has the right to select DBA_* views, else false.
   * Please use this method only to test for this functionality!
   * @return boolean TRUE if user can select DBA_ views, else false.
   */
  public function Get_DBA_Flag()
    {
    return($_SESSION['DBA_VIEWS']);
    }

  /**
   * Returns TRUE if current user has the right to select V$* views, else false.
   * Please use this method only to test for this functionality!
   * @return boolean TRUE if user can select V$ views, else false.
   */
  public function Get_V_Flag()
    {
    return($_SESSION['V_VIEWS']);
    }

  /**
   * Exits a plugin in case of an error.
   * Please use only this method if you want to abort execution of plugins in error conditions.
   * NOTE: This method terminates execution!
   * @param string $errmsg The message to show to the user before aborting execution.
   */
  public function ErrorExit($errmsg)
    {
    echo("<div id=\"informUser\">\n");
    echo("<div class=\"ui-widget\">\n");
    echo("<div class=\"ui-state-error ui-corner-all\" style=\"padding: 0 .7em;\">\n");
    echo("<p>".$errmsg."</p>\n");
    echo("</div>\n</div>\n</div>\n</div>\n");
    $this->PrintExtFooter();
    exit;
    }

  /**
   * Method returns either an empty string or the JPGraph installation path.
   * If string is non-empty the JPGraph classes can be used, else no JPGraph support exist.
   * @return string Empty string indicates no jpgraph support, else the full path to the installation.
   */
  public function Get_JPGraph_Path()
    {
    $jpg = (defined('JPGRAPH_PATH')) ? JPGRAPH_PATH : '';
    if($jpg == '')
      {
      return('');
      }
    if(@file_exists(JPGRAPH_PATH.'/jpgraph.php') == TRUE)
      {
      return(JPGRAPH_PATH);
      }
    return('');
    }

  /**
   * Returns the database major version number (i.e. 8,9,10,11).
   * @return integer The connected database major version number.
   */
  public function Get_Oracle_Version()
    {
    return($_SESSION['DBVERSION']);
    }

  /**
   * Adds a call to the document.ready function from jQuery.
   * Normally this is empty and no ready function is shown.
   * @param string $jscode The call you want to execute in a ready() function.
   */
  public function Add_JS_Ready_Call($jscode)
    {
    $this->JQUERY_READY[] = $jscode;
    }

  /**
   * RenderQuery() reads data for a given query from current db connection, and renders the result from query on screen.
   * @param string $query The SELECT query to perform against the database.
   * @param array &$hash Optional a hash with bind variables.
   * @param array %$th_options Optionally an array to configure the output table.
   */
  public function RenderQuery($query,&$hash = NULL,&$th_options = array())
    {
    $colcount = 0;

    if(isset($th_options['TABLE_CLASS']) == FALSE)
      {
      $th_options['TABLE_CLASS'] = 'datatable';
      }
    if(isset($th_options['TABLE_SUMMARY']) == FALSE)
      {
      $th_options['TABLE_SUMMARY'] = '--- N/A ---';
      }
    if(isset($th_options['TD_ODD']) == FALSE)
      {
      $th_options['TD_ODD'] = 'td_odd';
      }
    if(isset($th_options['TD_EVEN']) == FALSE)
      {
      $th_options['TD_EVEN'] = 'td_even';
      }
    printf("<table class=\"%s\" summary=\"%s\">\n",$th_options['TABLE_CLASS'],$th_options['TABLE_SUMMARY']);
    if(isset($th_options['CAPTION']) == TRUE && $th_options['CAPTION'] != '')
      {
      printf("<caption>%s</caption>\n",$th_options['CAPTION']);
      }
    if(isset($th_options['THEAD']) == TRUE && count($th_options['THEAD']) > 0)
      {
      $colcount = count($th_options['THEAD']);
      echo("<thead><tr>\n");
      for($i = 0; $i < $colcount; $i++)
        {
        printf("  <th>%s</th>\n",$th_options['THEAD'][$i]);
        }
      echo("</tr></thead>\n<tbody>\n");
      }
    if(is_null($hash) == FALSE)
      {
      $this->dbh->QueryResultHash($query,$hash);
      }
    else
      {
      $this->dbh->QueryResult($query);
      }
    $tdata = array();
    $lv = 0;
    while($d = $this->dbh->FetchResult(OCI_NUM))
      {
      if($lv % 2)
        {
        $myback = 'td_odd';
        }
      else
        {
        $myback = 'td_even';
        }
      printf("<tr class=\"%s\">\n",$myback);
      for($i = 0; $i < count($d); $i++)
        {
        if(isset($th_options['DATA_LOOKUP'][$i]) == TRUE)
          {
          printf("  <td align=\"center\">%s</td>\n",$th_options['DATA_LOOKUP'][$i][$d[$i]]);
          }
        else
          {
          printf("  <td>%s</td>\n",$d[$i]);
          }
        }
      echo("</tr>\n");
      $lv++;
      }
    $this->dbh->FreeResult();
    if(!$lv)
      {
      printf("<tr class=\"%s\">\n",$th_options['TD_EVEN']);
      if($colcount)
        {
        printf("  <td colspan=\"%d\" align=\"center\">%s</td>\n",$colcount,'<b>--- No data found ---</b>');
        }
      else
        {
        printf("  <td align=\"center\">%s</td>\n",$colcount,'<b>--- No data found ---</b>');
        }
      echo("</tr>\n");
      }
    echo("</tbody>\n</table>\n");
    }

  } // EOF
?>
