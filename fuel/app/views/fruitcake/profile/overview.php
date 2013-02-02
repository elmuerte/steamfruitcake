<html>
<body>

	<h1>
		<img src="<?= $avatarIcon ?>" />
		<?= $steamID ?>
	</h1>
	Privacy state:
	<?= $privacyState ?>
	<br /> Last profile update:
	<?= $lastUpdate ?>
	<br />

	<p>
		<?= $messages ?>
	</p>

	<?= $games ?>

	<form method="post">
		<button type="submit" name="forceUpdate" value="1">Refresh data</button>
	</form>

</body>
</html>
