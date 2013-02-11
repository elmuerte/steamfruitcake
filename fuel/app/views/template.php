<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title><?= (strlen($title)>0)?$title." :: ":"" ?> SteamFruitcake&trade;</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?= Asset::css('bootstrap.css') ?>
<?= Asset::js('jquery-1.9.0.js') ?>
<?= Asset::js('bootstrap.js') ?>
<style>
body {
	/* 60px to make the container go all the way to the bottom of the topbar */
	padding-top: 60px;
}

.footer {
	text-align: center;
	padding: 30px 0;
	margin-top: 70px;
	border-top: 1px solid #e5e5e5;
	background-color: #f5f5f5;
}

.footer p {
	margin-bottom: 0;
	color: #777;
}

.footer-links {
	margin: 10px 0;
}

.footer-links li {
	display: inline;
	padding: 0 2px;
}

.footer-links li:first-child {
	padding-left: 0;
}
</style>
<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<link rel="shortcut icon" href="assets/img/favicon.png">
</head>

<body>

	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<button class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span>
				</button>
				<a class="brand" href="<?= Uri::base() ?>"><strong>Steam</strong><em>Fruitcake</em>&trade;</a>
				<div class="nav-collapse collapse">
					<ul class="nav">
						<li class="active"><a href="<?= Uri::base() ?>"><i class="icon-home icon-white"></i> Scoreboard</a></li>
					</ul>
					<form class="navbar-search pull-left">
						<input type="text" class="search-query" placeholder="Find fruitcake">
					</form>
					<ul class="nav pull-right">
						<?php if ($steamID === false): ?>
						<li><a href="<?= Router::get('profile') ?>"><i class="icon-user icon-white"></i> Log in</a></li>
						<?php else: ?>
						<li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" role="button" href="<?= Router::get('profile') ?>"> <img
								src="<?= $steamProfile->avatarIcon ?>" height="18" width="18"
							/> <?= $steamID ?><b class="caret"></b>
						</a>
							<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
								<li><a href="<?= Router::get('profile') ?>"><i class="icon-user icon"></i> Profile </a></li>
								<li><a href="<?= Router::get('logout') ?>"><i class="icon-off icon"></i> Logout</a></li>
							</ul>
						</li>
						<?php endif; ?>
						<li><a href="<?= Router::get('about') ?>"><i class="icon-info-sign icon-white"></i> About</a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>

	<div class="container">

		<div id="messages">
			<?php foreach ($messages as $msg) echo $msg; ?>
		</div>

		<?= $content ?>

	</div>

	<footer class="footer">
		<div class="container">
			<p>&copy; 2013 elmuerte</p>
			<p>
				<a href="http://steampowered.com">Powered by Steam</a>
			</p>
			<p>Disclaimer etc.</p>
		</div>
	</footer>

	<!-- <script src="../assets/js/jquery.js"></script>  -->
</body>
</html>
