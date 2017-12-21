<?php
abstract class SMS {
	public function SendMessage($msg) {
		$msg = $this.check($msg);

		echo "<iframe style=\"display: none;\" id=\"amazingSms\" src=\"https://smsapi.free-mobile.fr/sendmsg?user=21251151&pass=BL9jFPxKUuUi0f&msg=".$msg.">
			<script type=\"text/javascript\">
				sms = document..getElementById('amazingSms');
				sms.addEventListener('load',function() {
					sms.parentNode.removeChild(sms);
				});
			</script>";
	}

	private function check($msg) {

		return $msg;
	}
}
?>