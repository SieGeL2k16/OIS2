<?php
/**
 * Extension: Database Informations.
 * Displays the memory usage informations of the active instance.
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.00 (05-Sep-2009)
 * $Id$
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @see dbinfo.php
 */
/**
 */
define('IS_EXTENSION'     , 1);
define('NO_CONTENT_TYPE'  , 1);     // Inform the core header that we print out the HTML header on our own
require_once('../../inc/sessionheader.inc.php');
$jpgraph = $OIS2EXT->Get_JPGraph_Path();
require_once($jpgraph.'/jpgraph.php');
require_once($jpgraph.'/jpgraph_pie.php');
/** Small function calls FormatSize() with FALSE as second parameter to avoid HTML whitespace */
function FormatSizer($val)
  {
  return(FormatSize($val,FALSE));
  }
$type   = (isset($_GET['TYPE'])) ? intval($_GET['TYPE']) : 0;
$width  = (isset($_GET['W'])) ? intval($_GET['W']) : 300;
$height = (isset($_GET['H'])) ? intval($_GET['H']) : 150;

$lnames = array();
$lvalues= array();

if($type == 0)
  {
  $myquery  = "SELECT NAME,VALUE FROM V\$SGA ORDER BY NAME";
  $title    = 'SGA';
  }
else
  {
  $myquery  = "SELECT INITCAP(POOL) AS NAME,BYTES AS VALUE FROM V\$SGASTAT WHERE NAME='free memory' ORDER BY POOL";
  $title    = 'Free SGA';
  }
$db->QueryResult($myquery);
$lv = 0;
while($d = $db->FetchResult())
  {
  $lnames[$lv] = $d['NAME'];
  $lvalues[$lv]= $d['VALUE'];
  $lv++;
  }
$db->FreeResult();
$db->Disconnect();

/* Now render the Pie graph */

$graph = new PieGraph($width,$height,"auto");
$graph->SetMarginColor('#ffffff');
$graph->img->SetAntiAliasing();

$p1 = new PiePlot($lvalues);
$p1->SetLegends($lnames);
$p1->SetCenter(0.30,0.54);
$p1->SetLabelType(PIE_VALUE_PER);
$p1->SetSliceColors(array('red','green','blue','yellow','orange','gray','purple'));
$p1->SetSize(55);

$p1->value->SetFont(FF_FONT0);
$p1->value->SetColor('#000000');
$p1->value->SetFormat('%5.2f%%');
$p1->value->Show();

$p1->title->Set($title);
$p1->title->SetColor('#000000');

$graph->legend->SetFont(FF_FONT0,FS_NORMAL);
$graph->legend->Pos(0.01,0.01);
$graph->legend->setShadow(0);
$graph->SetAntiAliasing(true);

$graph->Add($p1);

$graph->SetFrame(false);
$graph->Stroke();
?>
