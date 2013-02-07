<div class="media">
	<img class="pull-left media-object img-polaroid" src="<?= $avatarFull ?>" />
	<div class="media-body">
		<div class="page-header">
			<h1 class="media-heading">
				<?= $steamID ?>
				<small><?= $realname ?> </small>
			</h1>
		</div>
		<dl class="dl-horizontal">
			<dt>Profile visibility</dt>
			<dd>
				<?= $privacyState ?>
				<?php if (!$profile->isPublic()): ?>
				<span class="badge badge-important">!</span>
				<?php endif;?>
			</dd>
			<dt>Member since</dt>
			<dd>
				<?= $timecreated ?>
			</dd>
			<dt>Last profile update</dt>
			<dd>
				<?= $lastUpdate ?>
			</dd>
		</dl>
	</div>
</div>
