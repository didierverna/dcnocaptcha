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

if (!defined('DC_CONTEXT_ADMIN')){return;}

# Settings NS
$core->blog->settings->addNamespace ('recaptcha');

# behaviors
$core->addBehavior ('adminBlogPreferencesForm',
		    array ('recaptchaAdmBhv', 'preferencesForm'));
$core->addBehavior ('adminBeforeBlogSettingsUpdate',
		    array ('recaptchaAdmBhv', 'updateSettings'));

# Plugin menu
$_menu['Plugins']->addItem (__('reCAPTCHA'),
			    'plugin.php?p=recaptcha',
			    'index.php?pf=recaptcha/icon.png',
			    preg_match ('/plugin.php\?p=recaptcha(&.*)?$/',
					$_SERVER['REQUEST_URI']),
			    $core->auth->check ('admin', $core->blog->id));


class recaptchaAdmBhv
{
  public static function preferencesForm ($core)
  {
    $settings = $core->blog->settings->recaptcha;
    $theme    = $settings->get ('recaptcha_theme');
    $size     = $settings->get ('recaptcha_size');

    echo "<fieldset>
  <legend>" . __('reCAPTCHA') . "</legend>
  <div class=\"col\">
    <p>
      <label class=\"classic\">"
       . form::checkbox ('recaptcha_blog_enable',
			 1,
			 $settings->get ('recaptcha_blog_enable'))
       . __('Enable reCAPTCHA for this blog')
       . "</label>
    </p>
    <p>
      <label class=\"classic\">" . __('Theme:') . "\n"
       . form::radio (array ('recaptcha_theme'),
		      'light',
		      ($theme == 'light' ? true : false))
       . __("Light") . "\n"
       . form::radio (array ('recaptcha_theme'),
		      'dark',
		      ($theme == 'dark' ? true : false))
       . __("Dark") . "
      </label>
    </p>
    <p>
      <label class=\"classic\">" . __('Size:') . "\n"
	. form::radio (array ('recaptcha_size'),
		       'normal',
		       ($size == 'normal' ? true : false))
	. __("Normal") . "\n"
	. form::radio (array ('recaptcha_size'),
		       'compact',
		       ($size == 'compact' ? true : false))
	. __("Compact") . "
      </label>
    </p>
  </div>
</fieldset>";
  }

  public static function updateSettings ($settings)
  {
    $settings->recaptcha->put ('recaptcha_blog_enable',
			       $_POST['recaptcha_blog_enable']);
    if ($_POST['recaptcha_blog_enable'] == 1)
      {
	if (empty ($_POST['recaptcha_theme']))
	{
	  $_POST['recaptcha_theme'] = 'light';
	}
	if (empty ($_POST['recaptcha_size']))
	{
	  $_POST['recaptcha_size'] = 'normal';
	}
      }
    $settings->recaptcha->put ('recaptcha_theme', $_POST['recaptcha_theme']);
    $settings->recaptcha->put ('recaptcha_size', $_POST['recaptcha_size']);
  }
}

?>
