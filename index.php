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

dcPage::check('admin');

# Requests
$action = isset($_POST['action']) ? $_POST['action'] : '';
$section = isset($_REQUEST['section']) ? $_REQUEST['section'] : '';
$msg = isset($_REQUEST['msg']) ? $_REQUEST['msg'] : '';

# Read settings
$core->blog->settings->addNamespace('recaptcha');
$s = $core->blog->settings->recaptcha;

$recaptcha_active = (boolean) $s->recaptcha_active;
$recaptcha_public_key = (string) $s->recaptcha_public_key;
$recaptcha_private_key = (string) $s->recaptcha_private_key;
$recaptcha_plugins_path = (string) $s->recaptcha_plugins_path;

# Save settings
if ($action == 'savesetting')
{
	try
	{
		$recaptcha_public_key = $_POST['recaptcha_public_key'];
		$recaptcha_private_key = $_POST['recaptcha_private_key'];
		$recaptcha_active = !empty($_POST['recaptcha_active']);
		$recaptcha_plugins_path = $_POST['recaptcha_plugins_path'];

		if ((empty($recaptcha_public_key) ||
				empty($recaptcha_private_key)) &&
				$recaptcha_active)
		{
			$recaptcha_active = false;
			throw new Exception(__('You must enter a public and a private key before activating this plugin.'));
		}

		$s->put('recaptcha_active',$recaptcha_active);
		$s->put('recaptcha_public_key',$recaptcha_public_key);
		$s->put('recaptcha_private_key',$recaptcha_private_key);
		$s->put('recaptcha_plugins_path',$recaptcha_plugins_path);

		$core->blog->triggerBlog();

		http::redirect('plugin.php?p=recaptcha&section='.$section.'&msg='.$action);
	}
	catch (Exception $e)
	{
		$core->error->add($e->getMessage());
	}
}

# Messages
$msg_list = array(
	'savesetting' => __('Configuration successfully saved')
);

# Display
echo '
<html><head><title>'.__('reCAPTCHA').'</title>'.
'</head>
<body>
<h2>'.__('reCAPTCHA').'</h2>';

if (isset($msg_list[$msg]))
{
	echo sprintf('<p class="message">%s</p>',$msg_list[$msg]);
}

if (empty($recaptcha_plugins_path)) {
	$recaptcha_plugins_path = '/plugins';
}

echo
'<form method="post" action="'.$p_url.'" id="setting-form">

<fieldset id="plugin"><legend>'. __('Plugin activation').'</legend>
<p class="field"><label>'.__('Public Key:').
form::field(array('recaptcha_public_key'),50,255,$recaptcha_public_key).'
</label></p>
<p class="field"><label>'.__('Private Key:').
form::field(array('recaptcha_private_key'),50,255,$recaptcha_private_key).'
</label></p>
<p class="form-note">'.__('To activate this plugin you need to enter your reCAPTCHA public and private keys. If you don\'t have them, go to <a href="https://www.google.com/recaptcha/admin/create?app=php" target="_blank">reCAPTCHA</a> and create them.').'</p>
<p class="field"><label>'.
form::checkbox(array('recaptcha_active'),'1',$recaptcha_active).
__('Enable extension').'</label></p>
</fieldset>

<fieldset id="plugin"><legend>'. __('Parameters').'</legend>
<p class="field"><label>'.__('Plugins path:').
form::field(array('recaptcha_plugins_path'),30,255,$recaptcha_plugins_path).'
<p class="form-note">'.__('Enter plugins URL path (default: /plugins). If you installed Dotclear in a subdirectory enter something like /dotclear/plugins without trailing slash.').'</p>
</label></p>
</fieldset>';

echo '
<div class="clear">
<p><input type="submit" name="save" value="'.__('save').'" />'.
$core->formNonce().
form::hidden(array('p'),'recaptcha').
form::hidden(array('action'),'savesetting').
form::hidden(array('section'),$section).'
</p></div>
</form>';
dcPage::helpBlock('recaptcha');
echo '
<hr class="clear"/><p class="right">
reCAPTCHA - '.$core->plugins->moduleInfo('recaptcha','version').'&nbsp;
<img alt="'.__('reCAPTCHA').'" src="index.php?pf=recaptcha/icon.png" />
</p>
</body>
</html>';
?>