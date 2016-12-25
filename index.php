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

$core->blog->settings->addNamespace ('recaptcha');
$settings = $core->blog->settings->recaptcha;

$recaptcha_active      = (boolean) $settings->recaptcha_active;
$recaptcha_public_key  = (string) $settings->recaptcha_public_key;
$recaptcha_private_key = (string) $settings->recaptcha_private_key;

if ($action == 'savesetting')
{
  try
  {
    $recaptcha_public_key  = $_POST['recaptcha_public_key'];
    $recaptcha_private_key = $_POST['recaptcha_private_key'];
    $recaptcha_active      = ! empty ($_POST['recaptcha_active']);

    if ((empty ($recaptcha_public_key) || empty ($recaptcha_private_key))
	&& $recaptcha_active)
    {
      $recaptcha_active = false;
      throw new Exception (__('You must enter a public and a private key before activating this plugin.'));
    }

    $settings->put ('recaptcha_active',      $recaptcha_active);
    $settings->put ('recaptcha_public_key',  $recaptcha_public_key);
    $settings->put ('recaptcha_private_key', $recaptcha_private_key);

    $core->blog->triggerBlog ();

    http::redirect ('plugin.php?p=recaptcha&section=' . $section
		  . '&msg=' . $action);
  }
  catch (Exception $e)
  {
    $core->error->add ($e->getMessage ());
  }
}

$msg_list = array ('savesetting' => __('Configuration successfully saved'));

echo '<html><head><title>' . __('reCAPTCHA') .'</title></head>
<body>
<h2>' . __('reCAPTCHA') . '</h2>';

if (isset ($msg_list[$msg]))
  echo sprintf ('<p class="message">%s</p>', $msg_list[$msg]);

echo '<form method="post" action="' . $p_url . '" id="setting-form">

<fieldset id="plugin"><legend>' . __('Plugin activation') . '</legend>
<p class="field"><label>' . __('Public Key:')
  . form::field (array ('recaptcha_public_key'), 50, 255,
		  $recaptcha_public_key)
  .'
</label></p>
<p class="field"><label>' . __('Private Key:')
  . form::field (array ('recaptcha_private_key'), 50, 255,
		 $recaptcha_private_key)
  .'
</label></p>
<p class="form-note">'
  . __('To activate this plugin you need to enter your reCAPTCHA public and private keys. If you don\'t have them, go to <a href="https://www.google.com/recaptcha/admin/create?app=php" target="_blank">reCAPTCHA</a> and create them.')
  . '</p>
<p class="field"><label>'
  . form::checkbox (array ('recaptcha_active'), '1', $recaptcha_active)
  . __('Enable extension') . '</label></p>
</fieldset>

<div class="clear">
<p><input type="submit" name="save" value="' . __('save') . '" />'
  . $core->formNonce ()
  . form::hidden (array ('p'),       'recaptcha')
  . form::hidden (array ('action'),  'savesetting')
  . form::hidden (array ('section'), $section) .'
</p></div>
</form>';

dcPage::helpBlock ('recaptcha');

echo '<hr class="clear"/><p class="right">
reCAPTCHA - ' . $core->plugins->moduleInfo ('recaptcha', 'version')
. '&nbsp;
<img alt="' . __('reCAPTCHA') . '" src="index.php?pf=recaptcha/icon.png" />
</p>
</body>
</html>';
?>
