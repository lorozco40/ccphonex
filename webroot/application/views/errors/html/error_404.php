<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>404 Page Not Found</title>
<style type="text/css">

::selection { background-color: #E13300; color: white; }
::-moz-selection { background-color: #E13300; color: white; }

body {
	background-color: #0b879a;
	background: linear-gradient(90deg, rgba(147,167,71,1) 0%, rgba(11,135,154,1) 100%);
	margin: 40px;
	font: 13px/20px normal Helvetica, Arial, sans-serif;
	color: #4F5155;
}

a {
	color: #003399;
	background-color: transparent;
	font-weight: normal;
}

h1 {
	color: #444;
	background-color: transparent;
	border-bottom: 1px solid #D0D0D0;
	font-size: 19px;
	font-weight: normal;
	margin: 0 0 14px 0;
	padding: 14px 15px 10px 15px;
}

code {
	font-family: Consolas, Monaco, Courier New, Courier, monospace;
	font-size: 12px;
	background-color: #f9f9f9;
	border: 1px solid #D0D0D0;
	color: #002166;
	display: block;
	margin: 14px 0 14px 0;
	padding: 12px 10px 12px 10px;
}

#container {
	margin: 10px;
	border: 1px solid #D0D0D0;
	box-shadow: 0 0 8px #D0D0D0;
	background-color: #fbf3e6;
}

p {
	margin: 12px 15px 12px 15px;
}
</style>
</head>
<body>
	<div id="container">
		<?php
			if (!function_exists('traduce')) {
				function traduce($txt) {
					$txt = preg_replace('([^A-Za-z0-9 .!])', '', strip_tags($txt));
					$trad = array(
						"404 Page Not Found" => "404 Página no encontrada",
						"The page you requested was not found." => "La página que buscas no se encuentra.",
					);
					return (array_key_exists($txt, $trad)) ? $trad[$txt] : $txt;
				}
			}
		?>
		<br><br><br><center><img src='/assets/img/logo.png'></center><br><br><br>
		<h1><?php echo traduce($heading); ?></h1>
		<p><?php echo traduce($message); ?></p>
	</div>
</body>
</html>
