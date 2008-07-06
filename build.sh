#!/bin/bash


VERSION=`cat p4a/constants.php | grep P4A_VERSION | cut -d \' -f4`
SRCDIR=`pwd`
TMPDIR='/tmp'

# preparing creation
cd $TMPDIR
rm -r p4apackages
mkdir p4apackages
cd p4apackages
PKGDIR=`pwd`
cp -r $SRCDIR p4a

###########################
# BUILDING CODE REFERENCE #
###########################

cd $PKGDIR
mkdir code-reference
cd $SRCDIR
phpdoc -q on -d 'p4a/' -ti 'P4A - PHP For Applications' -dn 'p4a' -dc 'PHP For Applications' -pp on -dh off -t $PKGDIR/code-reference -i 'Zend/,pear_net_useragent_detect.php,p4a_db_table.php' -o 'HTML:frames:earthli' -ric 'CHANGELOG,README,COPYING' -f 'CHANGELOG,README,COPYING'

##########################
# cleaning master source #
##########################

cd $PKGDIR
rm p4a/.project
rm p4a/build.sh
rm -rf p4a/.cache
rm -rf p4a/.settings
rm -rf p4a/themes/default/widgets/calendar
rm -rf `find -type d -name '.svn'`
rm -f `find -name '.cvsignore'`

##############################################
# COPYING DEFAULT DOCUMENTATION INTO PACKAGE #
##############################################

cd $PKGDIR
sed 's/blank.html/ric_README.html/' code-reference/index.html > index.html
mv index.html code-reference/
cp -r code-reference p4a/docs

##############################
# creating framework package #
##############################

cd $PKGDIR
mv p4a p4a-$VERSION
zip -qr9 p4a-$VERSION.zip p4a-$VERSION
tar zcf  p4a-$VERSION.tgz p4a-$VERSION
rm -r p4a-$VERSION
