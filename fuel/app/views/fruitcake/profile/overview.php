
<div class="container" id="profileDetails">
	<?= $profileDetails ?>
</div>

<?php foreach ($games as $game) {
	echo $game;
}  ?>


<div class="hide">
	<button id="refreshProfileButton" class="btn btn-mini" type="button" data-loading-text="loading..." >
		<i class="icon-refresh"></i>
	</button>
	<div id="delayAlert" class="alert alert-error">
		<a class="close" data-dismiss="alert" href="#">&times;</a>
		<h4>Please wait...</h4>
		<p>
			You cannot refresh your profile data for an additional <em>?</em> seconds.
		</p>
	</div>
</div>

<script type="text/javascript">
// <![CDATA[

var updateLimit = 2*1000;
var lastUpdate = new Date().getTime();

function mayRefresh() {
	var curTime = new Date().getTime();
	var diff = curTime - lastUpdate;
	if (diff < updateLimit) {
		var waitSec = (updateLimit - diff);
		var msg = $('#delayAlert').clone().hide().alert();
		$('em', msg).html(Math.round(waitSec / 1000));
		$('#messages').prepend(msg);
		msg.fadeIn().delay(waitSec).fadeOut();
		return false;
	}
	lastUpdate = curTime;
	return true;
}

function processProfileUpdate(data) {
	var msg = $($.parseHTML($('messages', data).text()));
	var msgHolder = $('#messages');
	msg.each(function() {
		var jq = $(this).clone();
		console.log(jq);
		msgHolder.append(jq);
		jq.fadeIn();
	});
	console.log(data);
}

function refreshProfile() {
	if (!mayRefresh()) {
		return;
	}
	$(this).fadeOut().delay(updateLimit).fadeIn();
	$('#throbber').fadeIn();
	$.ajax({
		url: "<?= Uri::current() ?>"
	}).done(processProfileUpdate);
}

function profileSectionInit() {
	var updateBtn = $('#refreshProfileButton').clone().hide();
	$('#profileDetails dl dd:last-child').append(updateBtn);
	updateBtn.click(refreshProfile).delay(updateLimit).fadeIn();
}

$(document).ready(profileSectionInit);

// ]]>
</script>
