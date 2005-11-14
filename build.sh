#!/bin/bash

echo -n "Please, enter P4A version: "
read VERSION

SRCDIR=`pwd`
TMPDIR='/tmp'

# preparing creation
cd $TMPDIR
rm -r p4apackages
mkdir p4apackages
cd p4apackages
PKGDIR=`pwd`
cp -r $SRCDIR .

###########################
# BUILDING CODE REFERENCE #
###########################

cd $PKGDIR
mkdir codereference-$VERSION
cd $SRCDIR
phpdoc -q -d 'p4a/,docs/phpdoc-tutorials/' -ti 'P4A - PHP For Applications - Code Reference' -dn 'p4a' -dc 'PHP For Applications' -pp on -dh off -t $PKGDIR/codereference-$VERSION -i 'pdf/,pear/,phpsniff/,formats/,messages/' -o 'HTML:frames:earthli'

##########################
# cleaning master source #
##########################

cd $PKGDIR
rm p4a/.project
rm p4a/.buildpath
rm p4a/p4a.kdevelop
rm p4a/p4a.kdevses
rm p4a/build.sh
rm -r p4a/contribs
rm -r `find -type d -name 'CVS'`
rm `find -name '.cvsignore'`

##############################################
# COPYING DEFAULT DOCUMENTATION INTO PACKAGE #
##############################################

cd $PKGDIR
rm -r p4a/docs
cp -r codereference-$VERSION p4a/docs

##############################
# creating framework package #
##############################

cd $PKGDIR
mv p4a p4a-$VERSION

tar cf p4a-$VERSION.tar p4a-$VERSION
gzip p4a-$VERSION.tar

rm -r p4a-$VERSION