#!/bin/bash

echo -n "Please, enter P4A version: "
read VERSION

SRCDIR=`pwd`

# going to develop root
cd ..

# preparing creation
rm -r p4aPackages
mkdir p4aPackages
cp -r p4a p4aPackages/
cd p4aPackages

PKGDIR=`pwd`

#########################
# DOCBOOK DOCUMENTATION #
#########################

# generating sigle file for overview
cd $PKGDIR
mkdir overview-$VERSION
cd overview-$VERSION
mkdir html-singlefile
cd html-singlefile
xsltproc -o index.html /usr/share/xml/docbook/html/docbook.xsl ../../p4a/docs/overview/index.xml
cp -r ../../p4a/docs/overview/images .
cd ..
cd ..

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
rm -r p4a/applications/.cvsignore
rm -r p4a/applications/testing

##############################################
# COPYING DEFAULT DOCUMENTATION INTO PACKAGE #
##############################################

cd $PKGDIR
rm -r p4a/docs
mkdir p4a/docs
cp -r overview-$VERSION/html-singlefile p4a/docs/overview
cp -r codereference-$VERSION p4a/docs/code-reference

##############################
# creating framework package #
##############################

cd $PKGDIR
cp -r p4a pra-$VERSION

rm p4a-$VERSION/build.sh
rm -r p4a-$VERSION/groovy-stuff

tar cf p4a-$VERSION.tar p4a-$VERSION
gzip p4a-$VERSION.tar

tar cf p4a-$VERSION.tar p4a-$VERSION
bzip2 p4a-$VERSION.tar

zip -rq p4a-$VERSION.zip p4a-$VERSION

rm -r p4a-$VERSION

################################
# cleaning up and turning back #
################################

cd $PKGDIR
rm -r p4a
cd ..