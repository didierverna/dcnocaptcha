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

$core->blog->settings->addNamespace ('nocaptcha');

$core->addBehavior ('adminBlogPreferencesForm',
		    array ('nocaptchaAdmBhv', 'preferencesForm'));
$core->addBehavior ('adminBeforeBlogSettingsUpdate',
		    array ('nocaptchaAdmBhv', 'updateSettings'));

$_menu['Plugins']->addItem ('noCAPTCHA',
			    'plugin.php?p=nocaptcha',
			    'index.php?pf=nocaptcha/icon.png',
			    preg_match ('/plugin.php\?p=nocaptcha(&.*)?$/',
					$_SERVER['REQUEST_URI']),
			    $core->auth->check ('admin', $core->blog->id));


class nocaptchaAdmBhv
{
  public static function preferencesForm ($core)
  {
    $settings = $core->blog->settings->nocaptcha;
    $theme    = $settings->get ('nocaptcha_theme');
    $size     = $settings->get ('nocaptcha_size');

    if (empty ($theme))
      $theme = 'light';
    if (empty ($size))
      $size = 'normal';
?>
<div class="fieldset">
  <h4>noCAPTCHA</h4>
  <p>
    <label class="classic">
    <?
       echo __('Theme:') . ' '
	  . form::radio (array ('nocaptcha_theme'),
			 'light',
			 ($theme == 'light' ? true : false))
	  . __('Light') . ' '
	  . form::radio (array ('nocaptcha_theme'),
			 'dark',
			 ($theme == 'dark' ? true : false))
	  . __('Dark');
    ?>
    </label>
  </p>
  <p>
    <label class="classic">
    <?
       echo __('Size:') . ' '
	  . form::radio (array ('nocaptcha_size'),
			 'normal',
			 ($size == 'normal' ? true : false))
	  . __('Normal') . ' '
	  . form::radio (array ('nocaptcha_size'),
			 'compact',
			 ($size == 'compact' ? true : false))
	  . __('Compact');
    ?>
    </label>
  </p>
  <p>
    <label class="classic">
      <?
      echo form::checkbox ('nocaptcha_blog_enable',
			   1,
			   $settings->get ('nocaptcha_blog_enable'))
	       . __('Enable noCAPTCHA for this blog');
      ?>
    </label>
  </p>
</div>
<?
  }

  public static function updateSettings ($settings)
  {
    $settings->nocaptcha->put ('nocaptcha_blog_enable',
			       $_POST['nocaptcha_blog_enable']);
    if ($_POST['nocaptcha_blog_enable'] == 1)
      {
	if (empty ($_POST['nocaptcha_theme']))
	  $_POST['nocaptcha_theme'] = 'light';

	if (empty ($_POST['nocaptcha_size']))
	  $_POST['nocaptcha_size'] = 'normal';
      }

    $settings->nocaptcha->put ('nocaptcha_theme', $_POST['nocaptcha_theme']);
    $settings->nocaptcha->put ('nocaptcha_size',  $_POST['nocaptcha_size']);
  }
}

?>
