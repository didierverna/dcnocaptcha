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
    $theme = $settings->get ('recaptcha_theme');
    $lang_values = array (__('English')    => 'en',
			  __('Dutch')      => 'nl',
			  __('French')     => 'fr',
			  __('German')     => 'de',
			  __('Portuguese') => 'pt',
			  __('Russian')    => 'ru',
			  __('Spanish')    => 'es',
			  __('Turkish')    => 'tr');

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
      . "Light\n"
       . form::radio (array ('recaptcha_theme'),
		      'dark',
		      ($theme == 'dark' ? true : false))
       . "Dark
      </label>
    </p>
    <p>
      <label class=\"classic\">" . __('Language:') . '&nbsp;'
       . form::combo (array ('recaptcha_lang'),
		      $lang_values,
		      $settings->get('recaptcha_lang'))
	. "</label>
    </p>
  </div>
</fieldset>";
  }

  public static function updateSettings ($settings)
  {
    $settings->recaptcha->put ('recaptcha_blog_enable',
			       $_POST['recaptcha_blog_enable']);
    if ($_POST['recaptcha_blog_enable'] == 1
	&& empty ($_POST['recaptcha_theme']))
    {
      $_POST['recaptcha_theme'] = 'light';
    }
    $settings->recaptcha->put ('recaptcha_theme', $_POST['recaptcha_theme']);
    $settings->recaptcha->put ('recaptcha_lang',  $_POST['recaptcha_lang']);
  }
}

?>
