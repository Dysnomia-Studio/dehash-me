<?php
abstract class SMS {
	/**
	 * Sends a sms to Elanis
	 *
	 * @param      string  $msg    The message
	 */
	public function SendMessage($msg) {
		$msg = $this->check($msg);

        // create curl resource
        $ch = curl_init();

        // set url
        curl_setopt($ch, CURLOPT_URL, 'https://smsapi.free-mobile.fr/sendmsg?user=21251151&pass=BL9jFPxKUuUi0f&msg='.$msg);

        //return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // $output contains the output string
        /*$output = */curl_exec($ch);

        // close curl resource to free up system resources
        curl_close($ch);
	}

	private function check($msg) {
		return $msg;
	}
}