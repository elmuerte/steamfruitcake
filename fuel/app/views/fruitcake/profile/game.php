<div>

	<h2>
		<img src="<?= $logo ?>" />
		<?= $name ?>
		(
		<?= $appID ?>
		)
	</h2>
	Quantity:
	<?= $quantity ?>

	<form method="post" action="<?= Uri::create('fruitcake/report') ?>">
		<input type="hidden" name="appID" value="<?= $appID ?>">
		<button type="submit">fruitcake it</button>
	</form>

</div>
