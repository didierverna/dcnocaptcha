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


if (! defined ('DC_CONTEXT_ADMIN'))
  return;

$core->blog->settings->addNamespace ('recaptcha');

$core->addBehavior ('adminBlogPreferencesForm',
		    array ('recaptchaAdmBhv', 'preferencesForm'));
$core->addBehavior ('adminBeforeBlogSettingsUpdate',
		    array ('recaptchaAdmBhv', 'updateSettings'));

$_menu['Plugins']->addItem ('reCAPTCHA',
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

    echo '<fieldset>
  <legend>reCAPTCHA</legend>
  <div class="col">
    <p>
      <label class="classic">'
       . form::checkbox ('recaptcha_blog_enable',
			 1,
			 $settings->get ('recaptcha_blog_enable'))
       . __('Enable reCAPTCHA for this blog')
       . '      </label>
    </p>
    <p>
      <label class="classic">' . __('Theme:') . ' '
       . form::radio (array ('recaptcha_theme'),
		      'light',
		      ($theme == 'light' ? true : false))
       . __('Light') . ' '
       . form::radio (array ('recaptcha_theme'),
		      'dark',
		      ($theme == 'dark' ? true : false))
       . __('Dark')
       . '      </label>
    </p>
    <p>
      <label class="classic">' . __('Size:') . ' '
	. form::radio (array ('recaptcha_size'),
		       'normal',
		       ($size == 'normal' ? true : false))
	. __('Normal') . ' '
	. form::radio (array ('recaptcha_size'),
		       'compact',
		       ($size == 'compact' ? true : false))
	. __('Compact') . '      </label>
    </p>
  </div>
</fieldset>';
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
