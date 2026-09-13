<head>
		<meta charset="utf-8">
		<link href="https://cdn.prod.website-files.com" rel="preconnect" crossorigin="anonymous">
		
		
		
		
		
		
		
		
		<meta content="width=device-width, initial-scale=1" name="viewport">
		
		
		<link href="https://fonts.googleapis.com" rel="preconnect">
		<link href="https://fonts.gstatic.com" rel="preconnect" crossorigin="anonymous">
		<script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js" type="text/javascript"></script>
		<script type="text/javascript">WebFont.load({  google: {    families: ["Geologica:300,400,500,600,700","Inter:300,400,500,600,700","Lora:300,400,500,600,700"]  }});</script>
		<script type="text/javascript">!function(o,c){var n=c.documentElement,t=" w-mod-";n.className+=t+"js",("ontouchstart"in o||o.DocumentTouch&&c instanceof DocumentTouch)&&(n.className+=t+"touch")}(window,document);</script>
		
		
		
		
		
		<style type="text/css">
  .w-webflow-badge {
    display: none !important;
  }
	.text-h1-color{
  	background-clip: text;
    -webkit-background-clip: text;
    color: transparent;
  }
		</style>
	<script id="query_vars">var query_vars='<?php global $wp_query;echo serialize($wp_query->query)?>';</script>
<?php wp_head(); ?>
<?php if(function_exists('get_field')) { echo get_field('head_code', 'option'); } ?>
<?php if(file_exists(dirname( __FILE__ ).'/header_code.php')){ include_once 'header_code.php'; } ?></head>