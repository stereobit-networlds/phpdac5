<?php
$encoding = $_GET['encoding']?$_GET['encoding']:'utf-8';
//echo '>',$encoding;
//$page = pathinfo($_SERVER['PHP_SELF']);//parse_url($_SERVER['PHP_SELF'],PHP_URL_PAGE);
//print_r($_GET);
$query = '?encoding=' . $_GET['encoding'] . "&htmlfile=" . $_GET['htmlfile'];//$page['query'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Tab control</title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $encoding ?>">

<link rel="STYLESHEET" type="text/css" href="../javascripts/dhtmlx/tabbar/codebase/dhtmlxtabbar.css">
<script  src="../javascripts/dhtmlx/tabbar/codebase/dhtmlxcommon.js"></script>
<script  src="../javascripts/dhtmlx/tabbar/codebase/dhtmlxtabbar.js"></script>

<div id="init_tabbar_from_script" style="Z-INDEX: 1; LEFT: -500px; VISIBILITY: hidden; WIDTH: 323px; POSITION: absolute; TOP: -500px; HEIGHT: 584px">test</div>
<div id="a_tabbar" style="width:100%; height:380px;"/>
<script>
tabbar = new dhtmlXTabBar("a_tabbar", "top");
tabbar.setSkin('dhx_skyblue');
tabbar.setImagePath("../javascripts/dhtmlx/tabbar/codebase/imgs/");
tabbar.addTab("a1", "Html Editor", "100px");
tabbar.addTab("a2", "File Explorer", "100px");
tabbar.addTab("a3", "Image Explorer", "100px");
tabbar.setHrefMode("iframes-on-demand");
tabbar.setContentHref("a1", "cpmhtmleditor.php<?php echo $query ?>");
tabbar.setContentHref("a2", "cpmhtmleditor.php<?php echo $query ?>);
tabbar.setContentHref("a3", "cpmhtmleditor.php<?php echo $query ?>");
tabbar.setTabActive("a1");
</script>
</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
</body>
</html>