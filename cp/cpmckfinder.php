<?php
require_once('dpc/system/pcntlhtml.lib.php'); 
$page = &new pcntl('
super javascript;
super rcserver.rcssystem;

load_extension adodb refby _ADODB_;
super database; 
use xwindow.window;
use gui.swfcharts;
include networlds.clientdpc;
private frontpage.fronthtmlpage /cgi-bin;
#ifdef SES_LOGIN
public shop.rcitems;
public rc.rclogo;
#endif
private phpdac.rccontrolpanel /cgi-bin;
',1);
$cptemplate = GetGlobal('controller')->calldpc_method('rcserver.paramload use FRONTHTMLPAGE+cptemplate');

if ($cptemplate) {
    $mc_page = (GetSessionParam('LOGIN')) ? 'cp-ckfinder' : 'cp-login';
	echo $page->render(null,getlocal(), null, $cptemplate.'/index.php');
	die();
}
else
	echo $page->render(null,getlocal(),null,'cp_em.html');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--
 * CKFinder
 * ========
 * http://ckfinder.com
 * Copyright (C) 2007-2011, CKSource - Frederico Knabben. All rights reserved.
 *
 * The software, this file and its contents are subject to the CKFinder
 * License. Please read the license.txt file before using, installing, copying,
 * modifying or distribute this file or part of its contents. The contents of
 * this file is part of the Source Code of CKFinder.
-->
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>CKFinder</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="robots" content="noindex, nofollow" />
	<!--link href="sample.css" rel="stylesheet" type="text/css" /-->
	<script type="text/javascript" src="ckfinder/ckfinder.js"></script>
	<style type="text/css">
		body, html, iframe, #ckfinder {
			margin: 0;
			padding: 0;
			border: 0;
			width: 100%;
			height: 100%;
			overflow: hidden;
		}
	</style>	
</head>
<body>
	<div id="ckfinder"></div>
	<script type="text/javascript">
(function()
{
		var config = {
		};
		var get = CKFinder.tools.getUrlParam;
		var getBool = function( v )
		{
			var t = get( v );

			if ( t === null )
				return null;

			return t == '0' ? false : true;
		};

		var tmp;
		if ( tmp = get( 'basePath' ) )
			CKFINDER.basePath = tmp;

		if ( tmp = get( 'startupPath' ) )
			config.startupPath = decodeURIComponent( tmp );

		config.id = get( 'id' ) || '';

		if ( ( tmp = getBool( 'rlf' ) ) !== null )
			config.rememberLastFolder = tmp;

		if ( ( tmp = getBool( 'dts' ) ) !== null )
			config.disableThumbnailSelection = tmp;

		if ( tmp = get( 'data' ) )
			config.selectActionData = tmp;

		if ( tmp = get( 'tdata' ) )
			config.selectThumbnailActionData = tmp;

		if ( tmp = get( 'type' ) )
			config.resourceType = tmp;

		if ( tmp = get( 'skin' ) )
			config.skin = tmp;

		if ( tmp = get( 'langCode' ) )
			config.language = tmp;

		// Try to get desired "File Select" action from the URL.
		var action;
		if ( tmp = get( 'CKEditor' ) )
		{
			if ( tmp.length )
				action = 'ckeditor';
		}
		if ( !action )
			action = get( 'action' );

		var parentWindow = ( window.parent == window )
			? window.opener : window.parent;

		switch ( action )
		{
			case 'js':
				var actionFunction = get( 'func' );
				if ( actionFunction && actionFunction.length > 0 )
					config.selectActionFunction = parentWindow[ actionFunction ];

				actionFunction = get( 'thumbFunc' );
				if ( actionFunction && actionFunction.length > 0 )
					config.selectThumbnailActionFunction = parentWindow[ actionFunction ];
				break ;

			case 'ckeditor':
				var funcNum = get( 'CKEditorFuncNum' );
				if ( parentWindow['CKEDITOR'] )
				{
					config.selectActionFunction = function( fileUrl, data )
					{
						parentWindow['CKEDITOR'].tools.callFunction( funcNum, fileUrl, data );
					};

					config.selectThumbnailActionFunction = config.selectActionFunction;
				}
				break;

			default:
				if ( parentWindow && parentWindow['FCK'] && parentWindow['SetUrl'] )
				{
					action = 'fckeditor' ;
					config.selectActionFunction = parentWindow['SetUrl'];

					if ( !config.disableThumbnailSelection )
						config.selectThumbnailActionFunction = parentWindow['SetUrl'];
				}
				else
					action = null ;
		}

		config.action = action;

		// Always use 100% width and height when nested using this middle page.
		config.width = config.height = '100%';

		var ckfinder = new CKFinder( config );
		ckfinder.replace( 'ckfinder', config );
})();
	</script>
</body>
</html>
