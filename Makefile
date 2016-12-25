## Copyright (C) 2016 Didier Verna

## Author: Didier Verna <didier@didierverna.net>

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


zip:
	git archive --output=recaptcha.zip \
	  --prefix=recaptcha/ --worktree-attributes HEAD


clean:

distclean: clean
	-rm recaptcha.zip


.PHONY: zip clean distclean


### Makefile ends here
