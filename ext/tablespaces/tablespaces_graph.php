<?php
/**
 * Extension: Tablespace Informations.
 * Renders a pie chart with all tablespaces.
 * NOTE: The data to render is passed via session variables $_SESSION['TS_NAMES'] & $_SESSION['TS_SIZES'] !
 * @package OIS2
 * @author Sascha 'SieGeL' Pfalz <php@saschapfalz.de>
 * @version 2.00 (06-Sep-2009)
 * $Id$
 * @filesource
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
/**
 */
define('IS_EXTENSION'     , 1);
define('NO_CONTENT_TYPE'  , 1);     // Inform the core header that we print out the HTML header on our own
require_once('../../inc/sessionheader.inc.php');
$jpgraph = $OIS2EXT->Get_JPGraph_Path();
require_once($jpgraph.'/jpgraph.php');
require_once($jpgraph.'/jpgraph_pie.php');
$width  = (isset($_GET['W'])) ? intval($_GET['W']) : 300;
$height = (isset($_GET['H'])) ? intval($_GET['H']) : 150;

// Retrieve data from session:

if(isset($_SESSION['TS_NAMES']))
  {
  $lnames = $_SESSION['TS_NAMES'];
  unset($_SESSION['TS_NAMES']);
  }

if(isset($_SESSION['TS_SIZES']))
  {
  $lvalues = $_SESSION['TS_SIZES'];
  unset($_SESSION['TS_SIZES']);
  }

// And render graph:

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

$p1->title->Set('Tablespace sizes');
$p1->title->SetColor('#000000');

$graph->legend->SetFont(FF_FONT0,FS_NORMAL);
$graph->legend->Pos(0.01,0.01);
$graph->legend->setShadow(0);
$graph->SetAntiAliasing(true);

$graph->Add($p1);

$graph->SetFrame(false);
$graph->Stroke();
?>
