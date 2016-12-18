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

global $__autoload, $core;

$__autoload['recaptcha'] = dirname(__FILE__).'/inc/class.recaptcha.php';

$core->url->register('recaptcha','recaptcha','^recaptcha/(.+)$',array('recaptchaUrl','recaptcha'));
?>