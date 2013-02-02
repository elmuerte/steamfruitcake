<html>
<body>

	<form method="post" action="<?= Uri::create('fruitcake/report') ?>">
		<h1>
			You are about to report <em>"<?= $name ?>"
			</em> as a fruit cake. Are you sure?
		</h1>
		<p>
			You have
			<?= $quantity ?>
			copies of this game in your current collection.
		</p>
		<input type="hidden" name="appID" value="<?= $appID ?>">
		<button type="submit" name="confirm" value="1">Yes, fruitcake it</button>
		<a href="<?= Uri::create('fruitcake/profile') ?>">No, go back</a>

	</form>


</body>
</html>
