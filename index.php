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

dcPage::check ('admin');

$action  = isset ($_POST['action']) ? $_POST['action'] : '';
$section = isset ($_REQUEST['section']) ? $_REQUEST['section'] : '';
$msg     = isset ($_REQUEST['msg']) ? $_REQUEST['msg'] : '';

$core->blog->settings->addNamespace ('nocaptcha');
$settings = $core->blog->settings->nocaptcha;

$nocaptcha_active      = (boolean) $settings->nocaptcha_active;
$nocaptcha_public_key  =  (string) $settings->nocaptcha_public_key;
$nocaptcha_private_key =  (string) $settings->nocaptcha_private_key;
$nocaptcha_post_method =  (string) $settings->nocaptcha_post_method;

if (empty ($nocaptcha_post_method))
  $nocaptcha_post_method = 'default';

if ($action == 'savesetting')
{
  try
  {
    $nocaptcha_post_method = $_POST['nocaptcha_post_method'];
    $nocaptcha_private_key = $_POST['nocaptcha_private_key'];
    $nocaptcha_public_key  = $_POST['nocaptcha_public_key'];
    $nocaptcha_active      = ! empty ($_POST['nocaptcha_active']);

    if ((empty ($nocaptcha_public_key) || empty ($nocaptcha_private_key))
	&& $nocaptcha_active)
    {
      $nocaptcha_active = false;
      throw new Exception (__('You must enter a public and a private key before activating this plugin.'));
    }

    $settings->put ('nocaptcha_active',      $nocaptcha_active);
    $settings->put ('nocaptcha_public_key',  $nocaptcha_public_key);
    $settings->put ('nocaptcha_private_key', $nocaptcha_private_key);
    $settings->put ('nocaptcha_post_method', $nocaptcha_post_method);

    $core->blog->triggerBlog ();

    http::redirect ('plugin.php?p=nocaptcha&section=' . $section
		  . '&msg=' . $action);
  }
  catch (Exception $exception)
  {
    $core->error->add ($exception->getMessage ());
  }
}

$msg_list = array ('savesetting' => __('Configuration successfully saved'));

?>
<html>
  <head>
    <title>noCAPTCHA</title>
  </head>
  <body>
    <h2>noCAPTCHA</h2>
<?
  if (isset ($msg_list[$msg]))
    echo sprintf ('<p class="message">%s</p>', $msg_list[$msg]);
?>
    <form method="post" action="<? echo $p_url; ?>" id="setting-form">
      <fieldset id="plugin"><h4><? echo __('Plugin activation'); ?></h4>
	<p class="field">
	  <label>
	    <?
	       echo __('Public Key:') . ' '
		  . form::field (array ('nocaptcha_public_key'), 50, 255,
				 $nocaptcha_public_key);
	    ?>
	  </label>
	</p>
	<p class="field">
	  <label>
	    <?
	       echo __('Private Key:') . ' '
		  . form::field (array ('nocaptcha_private_key'), 50, 255,
				 $nocaptcha_private_key);
	    ?>
	  </label>
	</p>
	<p class="form-note">
	  <?
	     echo __('To activate this plugin you need to enter your reCAPTCHA public and private keys. If you don\'t have them, go to <a href="https://www.google.com/recaptcha/admin/create?app=php" target="_blank">reCAPTCHA</a> and create them.');
	  ?>
	</p>
	<p class="field">
	  <label>
	    <?
	    echo __('Post Method:') . ' '
	       . form::radio (array ('nocaptcha_post_method'),
			      'default',
			      ($nocaptcha_post_method == 'default'
			       ? true : false))
	       . __('Default') . ' '
	       . form::radio (array ('nocaptcha_post_method'),
			      'curl',
			      ($nocaptcha_post_method == 'curl'
			       ? true : false))
	       . 'cURL';
	    ?>
	  </label>
	</p>
	<p class="form-note">
	  <?
	     echo __('Try the cURL post method if you're experiencing problems with the CAPTCHA response, e.g. errors such as "Invalid JSON".');
	  ?>
	</p>
	<p class="field">
	  <label>
	    <?
	       echo form::checkbox (array ('nocaptcha_active'), '1',
				    $nocaptcha_active)
		  . __('Enable extension');
	    ?>
	  </label>
	</p>
      </fieldset>

      <div class="clear">
	<p>
	  <input type="submit" name="save" value="<? echo __('save'); ?>" />
	  <?
	     echo $core->formNonce ()
		. form::hidden (array ('p'),       'nocaptcha')
		. form::hidden (array ('action'),  'savesetting')
		. form::hidden (array ('section'), $section);
	  ?>
	</p>
      </div>
    </form>

    <? dcPage::helpBlock ('nocaptcha'); ?>

    <hr class="clear" />
    <p class="right">
      noCAPTCHA
      - <? echo $core->plugins->moduleInfo ('nocaptcha', 'version'); ?>
      &nbsp; <img alt="noCAPTCHA" src="index.php?pf=nocaptcha/icon.png" />
    </p>
  </body>
</html>
