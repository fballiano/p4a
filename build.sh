#!/bin/bash

echo -n "Please, enter MerlinWork version: "
read VERSION

SRCDIR=`pwd`

# going to develop root
cd ..

# preparing creation
rm -r MerlinWorkPackages
mkdir MerlinWorkPackages
cp -r MerlinWork MerlinWorkPackages/
cd MerlinWorkPackages

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
xsltproc -o index.html /usr/share/xml/docbook/html/docbook.xsl ../../MerlinWork/docs/overview/index.xml
cp -r ../../MerlinWork/docs/overview/images .
cd ..
cd ..

# generating splitted files for overview
#cd overview-$VERSION
#mkdir html-splittedfiles
#cd html-splittedfiles
#xsltproc -o index.html /usr/share/sgml/docbook/xsl-stylesheets-1.62.0/htmlhelp/htmlhelp.xsl ../../MerlinWork/docs/overview/index.xml
#cp -r ../../MerlinWork/docs/overview/images .

###########################
# BUILDING CODE REFERENCE #
###########################

cd $PKGDIR
mkdir codereference-$VERSION
cd $SRCDIR
phpdoc -q -d 'core/,docs/phpdoc-tutorials/' -ti 'MerlinWork Code Reference' -dn 'MerlinWork' -dc 'Web Application Framework' -pp on -dh off -t $PKGDIR/codereference-$VERSION -i 'pdf/,pear/,smarty/,formats/,messages/' -o 'HTML:frames:earthli'

##################################
# creating CreaLabs CVS packages #
##################################

cd $PKGDIR
cp -r MerlinWork MerlinWork-CREALABS-$VERSION

tar cf MerlinWork-CREALABS-$VERSION.tar MerlinWork-CREALABS-$VERSION
gzip MerlinWork-CREALABS-$VERSION.tar

tar cf MerlinWork-CREALABS-$VERSION.tar MerlinWork-CREALABS-$VERSION
bzip2 MerlinWork-CREALABS-$VERSION.tar

zip -rq MerlinWork-CREALABS-$VERSION.zip MerlinWork-CREALABS-$VERSION

rm -r MerlinWork-CREALABS-$VERSION

##########################
# cleaning master source #
##########################

cd $PKGDIR
rm MerlinWork/.project
rm -r `find -type d -name 'CVS'`
rm -r MerlinWork/projects/.cvsignore
rm -r MerlinWork/projects/testing

##############################################
# COPYING DEFAULT DOCUMENTATION INTO PACKAGE #
##############################################

cd $PKGDIR
rm -r MerlinWork/docs
mkdir MerlinWork/docs
cp -r overview-$VERSION/html-singlefile MerlinWork/docs/overview
cp -r codereference-$VERSION MerlinWork/docs/code-reference

##############################
# creating framework package #
##############################

cd $PKGDIR
cp -r MerlinWork MerlinWork-$VERSION

rm MerlinWork-$VERSION/build.sh
rm -r MerlinWork-$VERSION/groovy-stuff

tar cf MerlinWork-$VERSION.tar MerlinWork-$VERSION
gzip MerlinWork-$VERSION.tar

tar cf MerlinWork-$VERSION.tar MerlinWork-$VERSION
bzip2 MerlinWork-$VERSION.tar

zip -rq MerlinWork-$VERSION.zip MerlinWork-$VERSION

rm -r MerlinWork-$VERSION

################################
# cleaning up and turning back #
################################

cd $PKGDIR
rm -r MerlinWork
cd ..