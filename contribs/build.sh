#!/bin/bash

echo -n "Please, enter module version: "
read VERSION

SRCDIR=`pwd`
NAME=`basename $SRCDIR`
TMPDIR='/tmp'

# preparing creation
cd $TMPDIR
rm -r p4apackages
mkdir p4apackages
cd p4apackages
PKGDIR=`pwd`
cp -r $SRCDIR .

rm -r `find -type d -name 'CVS'`
rm `find -name '.cvsignore'`

cd $NAME
tar zcf ../$NAME-$VERSION.tar.gz *
phpdoc -q on -d "$SRCDIR" -ti "$NAME" -dn 'p4a-contribs' -dc 'P4A Contribs' -i 'libraries/' -pp on -dh off -t $PKGDIR/codereference-$VERSION -o 'HTML:frames:earthli' -ric 'CHANGELOG,README,COPYING'
cd ..
rm -r $NAME
