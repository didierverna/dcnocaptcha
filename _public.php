<?php

## Copyright (C) 2011 Tof, reCAPTCHA and contributors
## Copyright (C) 2016 Didier Verna

## Author:     Tof <xtophe@free.fr>
## Maintainer: Didier Verna <didier@didierverna.net>

## This file is part of noCAPTCHA.

## noCAPTCHA is free software; you can redistribute it and/or modify
## it under the terms of the GNU General Public License version 3,
## as published by the Free Software Foundation.

## noCAPTCHA is distributed in the hope that it will be useful,
## but WITHOUT ANY WARRANTY; without even the implied warranty of
## MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
## GNU General Public License for more details.

## You should have received a copy of the GNU General Public License
## along with this program; if not, write to the Free Software
## Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.


if (! defined ('DC_RC_PATH'))
  return;

$core->blog->settings->addNamespace ('nocaptcha');

require_once dirname(__FILE__).'/lib/recaptcha/src/autoload.php';

$core->addBehavior ('publicHeadContent',
		    array ('nocaptchaBhv', 'publicheadContent'));
$core->addBehavior ('publicCommentFormAfterContent',
		    array ('nocaptchaBhv','publicCommentFormAfterContent'));
$core->addBehavior ('publicBeforeCommentCreate',
		    array ('nocaptchaBhv','publicBeforeCommentCreate'));

class nocaptchaBhv
{
  public static function publicheadContent ($core)
  {
    if (! $core->blog->settings->nocaptcha->nocaptcha_active)
      return;

    echo '<script type="text/javascript" '
       . 'src="https://www.google.com/recaptcha/api.js">'
       . '</script>';
  }

  public static function publicCommentFormAfterContent ($core, $_ctx)
  {
    $settings = $core->blog->settings->nocaptcha;

    if ((! $settings->nocaptcha_active || ! $settings->nocaptcha_blog_enable)
	&& empty ($_POST['preview']))
    return;

    if ($_POST['preview'])
    {
      if (isset ($_POST['g-recaptcha-response']))
      {
	$nocaptcha = new \ReCaptcha\ReCaptcha
	($settings->nocaptcha_private_key,
	 // #### FIXME: because OVH doesn't support allow_url_fopen. Should
	 // probably be a plugin option.
	 new \ReCaptcha\RequestMethod\CurlPost ());
	$response = $nocaptcha->verify ($_POST['g-recaptcha-response'],
					$_SERVER['REMOTE_ADDR']);

	if (! $response->isSuccess ())
	{
	  echo '<p class="error" id="pr">';
	  foreach ($response->getErrorCodes () as $code)
	  {
	    if ($code == 'missing-input-response')
	      echo __('The CAPTCHA wasn\'t entered correctly.');
	    else
	      echo "<tt>$code</tt>";
	    echo '<br />';
	  }
	  echo '</p>';
	  echo '<div class="g-recaptcha" data-sitekey="'
	     . $settings->nocaptcha_public_key
	     . '" data-theme="'
	     . $settings->nocaptcha_theme
	     . '" data-size="'
	     . $settings->nocaptcha_size
	     . '"></div>';
	}
	else
	  echo '<input type="hidden" name="nocaptcha" value="1" />';
      }
      else
	echo '<input type="hidden" name="nocaptcha" value="1" />';
    }
    else
    {
      if (empty ($_POST['nocaptcha']))
	echo '<div class="g-recaptcha" data-sitekey="'
	   . $settings->nocaptcha_public_key
	   . '" data-theme="'
	   . $settings->nocaptcha_theme
	   . '" data-size="'
	   . $settings->nocaptcha_size
	   . '"></div>' . "\n";
      else
	echo '<input type="hidden" name="nocaptcha" value="1" />';
    }
  }

  public static function publicBeforeCommentCreate ($cur)
  {
    global $core;
    $settings = $core->blog->settings->nocaptcha;

    if (! $settings->nocaptcha_active
	|| ! $settings->nocaptcha_blog_enable
	|| $_POST['nocaptcha'])
    return;

    $nocaptcha = new \ReCaptcha\ReCaptcha
    ($settings->nocaptcha_private_key,
     // #### FIXME: because OVH doesn't support allow_url_fopen. Should
     // probably be a plugin option.
     new \ReCaptcha\RequestMethod\CurlPost ());
    $response = $nocaptcha->verify ($_POST['g-recaptcha-response'],
				    $_SERVER['REMOTE_ADDR']);

    if (! $response->isSuccess ())
      throw new Exception (__('The CAPTCHA wasn\'t entered correctly.'));
  }
}

?>
