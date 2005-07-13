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

tar zcf $NAME-$VERSION.tar.gz $NAME/*
rm -r $NAME