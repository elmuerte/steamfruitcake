<html>
<body>

	<h1>Congratulations!</h1>
	<p>
		You added
		<?= $quantity ?>
		counts towards <em>"<?= $name ?>"
		</em> as fruitcake.
	</p>
	<a href="<?= Uri::create('fruitcake/profile') ?>">Back to profile</a>

</body>
</html>
