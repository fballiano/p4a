#!/usr/bin/perl -w

require utf8;
import utf8;
use strict;
use DateTime::Locale;
use POSIX 'strftime';

my $locale = $ARGV[0];
if (!`ls /usr/local/share/perl/5.8.7/DateTime/Locale/$locale.pm`) {
	exit(1);
}

my $l = DateTime::Locale->load($locale);

my $short = $l->short_date_format;
my $medium = $l->medium_date_format;
my $long = $l->long_date_format;
my $full = $l->full_date_format;

my $time_short = $l->short_time_format;
my $time_default = $l->medium_time_format;

#print "$short|" . strftime($l->short_date_format, localtime) . "\n";
#print "$medium|" . strftime($l->medium_date_format, localtime) . "\n";
#print "$long|" . strftime($l->long_date_format, localtime) . "\n";
#print "$full|" . strftime($l->full_date_format, localtime) . "\n";

print "$short\n";
print "$medium\n";
print "$long\n";
print "$full\n";
print "$time_short\n";
print "$time_default\n";
