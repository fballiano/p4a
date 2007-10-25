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
mkdir code-reference
cd $SRCDIR
phpdoc -q on -d 'p4a/,docs/phpdoc-tutorials/' -ti 'P4A - PHP For Applications - Code Reference' -dn 'p4a' -dc 'PHP For Applications' -pp on -dh off -t $PKGDIR/code-reference -i 'pear/,adodb/,phpthumb/,formats/,messages/,getid3/' -o 'HTML:frames:earthli' -ric 'CHANGELOG,README,COPYING'

##########################
# cleaning master source #
##########################

cd $PKGDIR
rm p4a/.project
rm p4a/.buildpath
rm p4a/build.sh
rm -rf p4a/.cache
rm -rf p4a/.settings
rm -rf p4a/contribs
rm -rf p4a/themes/default/widgets/calendar
rm -rf `find -type d -name '.svn'`
rm -f `find -name '.cvsignore'`

##############################################
# COPYING DEFAULT DOCUMENTATION INTO PACKAGE #
##############################################

cd $PKGDIR
rm -r p4a/docs
cp -r code-reference p4a/docs

##############################
# creating framework package #
##############################

cd $PKGDIR
mv p4a p4a-$VERSION

tar cf p4a-$VERSION.tar p4a-$VERSION
gzip p4a-$VERSION.tar

zip -r p4a-$VERSION.zip p4a-$VERSION

rm -r p4a-$VERSION

###################################
# creating documentation zip file #
###################################

zip -r code-reference.zip code-reference