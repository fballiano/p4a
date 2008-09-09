DESTDIR =

TARGETDIR = $(DESTDIR)/usr/share/php/p4a
CONFPATH  = $(DESTDIR)/etc/p4a

install:
	install -d -o www-data -m 755 $(TARGETDIR)/
	install -d ${CONFPATH}/ 

	cp -a p4a $(TARGETDIR)/
	cp -a themes $(TARGETDIR)/
	cp -a icons $(TARGETDIR)/
	cp p4a.php $(TARGETDIR)/
	cp debian/apache.conf $(CONFPATH)/apache.conf
 
	chown -Rf www-data:www-data $(TARGETDIR)/*
	find $(TARGETDIR)/ -type f -exec chmod 644 {} \;
