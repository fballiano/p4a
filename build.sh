#!/bin/bash

echo -n "Please, enter P4A version: "
read VERSION

SRCDIR=`pwd`
TMPDIR='/tmp'



# preparing creation
cd $TMPDIR
rm -r p4apackages
mkdir p4apackages
cp -r p4a p4apackages/
cd p4apackages
PKGDIR=`pwd`

###########################
# BUILDING CODE REFERENCE #
###########################

cd $PKGDIR
mkdir codereference-$VERSION
cd $SRCDIR
phpdoc -q -d 'core/,docs/phpdoc-tutorials/' -ti 'P4A - PHP For Applications - Code Reference' -dn 'P4A' -dc 'PHP For Applications' -pp on -dh off -t $PKGDIR/codereference-$VERSION -i 'pdf/,pear/,smarty/,formats/,messages/' -o 'HTML:frames:earthli'

##########################
# cleaning master source #
##########################

cd $PKGDIR
rm p4a/.project
rm -r `find -type d -name 'CVS'`
rm `find -name '.cvsignore'`
rm p4a/.project
rm p4a/p4a.kdevelop
rm p4a/p4a.kdevses
rm p4a-$VERSION/build.sh

##############################################
# COPYING DEFAULT DOCUMENTATION INTO PACKAGE #
##############################################

cd $PKGDIR
rm -r p4a/docs
mkdir p4a/docs
cp -r codereference-$VERSION p4a/docs/code-reference

##############################
# creating framework package #
##############################

cd $PKGDIR
cp -r p4a pra-$VERSION

tar cf p4a-$VERSION.tar p4a-$VERSION
gzip p4a-$VERSION.tar

rm -r p4a-$VERSION

################################
# cleaning up and turning back #
################################

cd $PKGDIR
rm -r p4a
cd ..