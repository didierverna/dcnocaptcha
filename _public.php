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
require_once dirname(__FILE__).'/lib/recaptcha/src/autoload.php';

# behaviors
$core->addBehavior('publicHeadContent',array('recaptchaBhv','publicheadContent'));
$core->addBehavior('publicCommentFormAfterContent',array('recaptchaBhv','publicCommentFormAfterContent'));
$core->addBehavior('publicBeforeCommentCreate',array('recaptchaBhv','publicBeforeCommentCreate'));

class recaptchaBhv
{

  public static function publicheadContent($core)
  {
    if (!$core->blog->settings->recaptcha->recaptcha_active)
      return;

    echo '<script type="text/javascript" src="https://www.google.com/recaptcha/api.js" /></script>';
  }

  public static function publicCommentFormAfterContent ($core, $_ctx)
  {
    if ((!$core->blog->settings->recaptcha->recaptcha_active
      || !$core->blog->settings->recaptcha->recaptcha_blog_enable)
	&& empty ($_POST['preview']))
    {
      return;
    }

    if ($_POST['preview'])
    {
      if (isset ($_POST['g-recaptcha-response']))
      {
	$recaptcha = new \ReCaptcha\ReCaptcha
	($core->blog->settings->recaptcha->recaptcha_private_key,
	 // #### FIXME: because OVH doesn't support allow_url_fopen. Should
	 // probably be a plugin option.
	 new \ReCaptcha\RequestMethod\CurlPost ());
	$response = $recaptcha->verify ($_POST['g-recaptcha-response'],
					$_SERVER["REMOTE_ADDR"]);

	if (! $response->isSuccess ())
	{
	  echo '				<p class="error" id="pr">'
	     . "\n";
	  foreach ($response->getErrorCodes () as $code)
	  {
	    if ($code == "missing-input-response")
	      echo __('The CAPTCHA wasn\'t entered correctly.');
	    else
	      echo '<tt>', $code ,'</tt> ';
	    echo "<br />";
	  }
	  echo '				</p>';
	  echo '          <div class="g-recaptcha" data-sitekey="'
	     . $core->blog->settings->recaptcha->recaptcha_public_key
	     . '" data-theme="'
	     . $core->blog->settings->recaptcha->recaptcha_theme
	     . '" data-size="'
	     . $core->blog->settings->recaptcha->recaptcha_size
	     . '"></div>' . "\n";
	}
	else
	{
	  echo '				<input type="hidden" name="recaptcha" value="1" />';
	}
      }
      else
      {
	echo '				<input type="hidden" name="recaptcha" value="1" />';
      }
    }
    else
    {
      if (empty ($_POST['recaptcha']))
      {
	echo '        <div class="g-recaptcha" data-sitekey="'
	   . $core->blog->settings->recaptcha->recaptcha_public_key
	   . '" data-theme="'
	   . $core->blog->settings->recaptcha->recaptcha_theme
	   . '" data-size="'
	   . $core->blog->settings->recaptcha->recaptcha_size
	   . '"></div>' . "\n";
      }
      else
      {
	echo '				<input type="hidden" name="recaptcha" value="1" />';
      }
    }
  }

  public static function publicBeforeCommentCreate ($cur)
  {
    global $core;

    if (!$core->blog->settings->recaptcha->recaptcha_active
	|| !$core->blog->settings->recaptcha->recaptcha_blog_enable
	|| $_POST['recaptcha'])
    {
      return;
    }

    $recaptcha = new \ReCaptcha\ReCaptcha
    ($core->blog->settings->recaptcha->recaptcha_private_key,
     // #### FIXME: because OVH doesn't support allow_url_fopen. Should
     // probably be a plugin option.
     new \ReCaptcha\RequestMethod\CurlPost ());
    $response = $recaptcha->verify ($_POST['g-recaptcha-response'],
				    $_SERVER["REMOTE_ADDR"]);

    if (!$response->isSuccess ())
    {
      throw new Exception ( __('The CAPTCHA wasn\'t entered correctly.'));
    }
  }
}
?>
