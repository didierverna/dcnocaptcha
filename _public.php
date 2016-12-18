<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
# This file is part of reCAPTCHA, a plugin for Dotclear 2.
# 
# Copyright (c) 2011 Tof, reCAPTCHA and contributors
# xtophe@free.fr
# 
# Licensed under the GPL version 2.0 license.
# A copy of this license is available in LICENSE file or at
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
# -- END LICENSE BLOCK ------------------------------------

if (!defined('DC_RC_PATH')){return;}

# Settings NS
$core->blog->settings->addNamespace('recaptcha');

# Lib
require_once dirname(__FILE__).'/lib/recaptcha-php-1.11/recaptchalib.php';

# behaviors
$core->addBehavior('publicHeadContent',array('recaptchaBhv','publicheadContent'));
$core->addBehavior('publicCommentFormAfterContent',array('recaptchaBhv','publicCommentFormAfterContent'));
$core->addBehavior('publicBeforeCommentCreate',array('recaptchaBhv','publicBeforeCommentCreate'));

class recaptchaBhv
{
	
	public static function publicheadContent($core)
	{
		if (!$core->blog->settings->recaptcha->recaptcha_active)
		{
				return;
		}

		// Add reCAPTCHA options and CSS pos
		echo '<script type="text/javascript">
  var RecaptchaOptions = {
    theme : \'' . $core->blog->settings->recaptcha->recaptcha_theme . '\',
    lang : \'' . $core->blog->settings->recaptcha->recaptcha_lang . '\'
  };
</script>
<style type="text/css">
#recaptcha_area {
  margin: 0 auto !important;
}
</style>';
	}

	public static function publicCommentFormAfterContent($core,$_ctx)
	{
		if ((!$core->blog->settings->recaptcha->recaptcha_active  ||
				!$core->blog->settings->recaptcha->recaptcha_blog_enable) &&
				empty($_POST['preview']))
		{
				return;
		}

		if ($_POST['preview'])
		{
			if ($_POST['recaptcha_challenge_field'])
			{
				$resp = recaptcha_check_answer ($core->blog->settings->recaptcha->recaptcha_private_key,
				                                $_SERVER["REMOTE_ADDR"],
				                                $_POST["recaptcha_challenge_field"],
				                                $_POST["recaptcha_response_field"]);
				if (!$resp->is_valid) {
					echo '				<p class="error" id="pr">'.__('The CAPTCHA wasn\'t entered correctly. Try it again.').'</p>';
					echo '        <div id="recaptcha_div">'.recaptcha_get_html($core->blog->settings->recaptcha->recaptcha_public_key).'
	        </div>';
				} else {
					echo '				<input type="hidden" name="recaptcha" value="1" />';
				}
			} else {
				echo '				<input type="hidden" name="recaptcha" value="1" />';
			}
		} else {
			if (empty($_POST['recaptcha']))
			{
				echo '        <div id="recaptcha_div">'.recaptcha_get_html($core->blog->settings->recaptcha->recaptcha_public_key).'
        </div>';
      } else {
				echo '				<input type="hidden" name="recaptcha" value="1" />';
      }
		}
	}

	public static function publicBeforeCommentCreate($cur)
	{
		global $core;

		if (!$core->blog->settings->recaptcha->recaptcha_active ||
				!$core->blog->settings->recaptcha->recaptcha_blog_enable ||
				$_POST['recaptcha'])
		{
				return;
		}

		$resp = recaptcha_check_answer ($core->blog->settings->recaptcha->recaptcha_private_key,
		                                $_SERVER["REMOTE_ADDR"],
		                                $_POST["recaptcha_challenge_field"],
		                                $_POST["recaptcha_response_field"]);
		if (!$resp->is_valid) {
			throw new Exception(__('The CAPTCHA wasn\'t entered correctly. Try it again.'));
		}
	}

}
?>