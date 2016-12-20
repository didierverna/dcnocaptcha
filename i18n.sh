#!/bin/sh

# Usage: i18n [-r|--regenerate]

# Template generation
regenerate ()
{
  echo "Regenerating the template..."
  xgettext -L PHP -k"__:1,2" -k"__:1"			\
	   --sort-by-file --no-wrap			\
	   --foreign-user --from-code=UTF-8		\
	   -d main -p locales/_pot/			\
	   --copyright-holder='Didier Verna'		\
	   --package-name=noCAPTCHA			\
	   --package-version=1.0			\
	   --msgid-bugs-address=didier@didierverna.net	\
	   *.php
  perl -pi -e "s|YEAR|2017|sgi;" locales/_pot/main.po
  perl -pi								   \
       -e "s|FIRST AUTHOR.*|Didier Verna <didier\@didierverna.net>\n|sgi;" \
       locales/_pot/main.po
  perl -pi -e "s|; charset=CHARSET|; charset=UTF-8|sgi;" locales/_pot/main.po
}

# Language update
update () # <lang subdir>
{
  echo "Updating the $1 locale..."
  template=locales/_pot/main.po
  file=locales/$1/main.po
  dir=locales/$1

  test -d $dir  || mkdir $dir
  test -f $file || cp $template $file
  msgmerge --update --sort-by-file --no-location --no-wrap $file $template
}


langs=fr
regenerate=no

while test $# != 0; do
  arg="$1"; shift
  case "$arg" in
    -r) regenerate=yes ;;
    --regenerate) regenerate=yes ;;
  esac
done

test "x${regenerate}" = "xyes" && regenerate
for i in $langs; do update $i; done
