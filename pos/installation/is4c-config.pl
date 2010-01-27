#!/usr/bin/perl

#############################################################
# 		      Edit  httpd.conf 		            #
#############################################################

open(FILE, "/usr/local/apache/conf/httpd.conf");
open(NEWFILE, ">/usr/local/apache/conf/httpd.conf.is4c");

$AddType_php = 0;

while ($line = <FILE> ) {

	if (index($line, "DocumentRoot \"/usr/local/apache") >= 0 ) {
		$line = "DocumentRoot \"/pos/is4c\"\n";
	}
	if (index($line, "<Directory \"/usr/local/apache/htdocs\">") >= 0) {
		$line = "<Directory \"/pos/is4c\">\n";
	}
	if (index($line, "ServerName www.example.com") >= 0) {
		$line = "ServerName 127.0.0.1:80\n";
	}
	if (index($line, "DirectoryIndex index.html") >= 0) {
		$line = "    DirectoryIndex login.php\n";
	}
	if (index($line, "AddType application/x-httpd-php .php") >= 0) {
		$AddType_php = 1;
	}
	print NEWFILE $line;
}

if ($AddType_php == 0) {
	print NEWFILE "\n";
	print NEWFILE "AddType application/x-httpd-php .php\n";
	print NEWFILE "AddType application/x-httpd-php-source .phps\n";
	print NEWFILE "AddType application/x-javascript .js\n";
}

close (FILE);
close (NEWFILE);


######################################################################
# 			  Edit rc.httpd 			     #
######################################################################

open (FILE, "/etc/rc.d/rc.httpd");
open (NEWFILE, ">/etc/rc.d/rc.httpd.is4c");

while ($line = <FILE>) {
	if (index($line, "/usr/sbin/apachectl start ;;") >= 0) {
		$line = "/usr/local/apache/bin/apachectl start ;;\n";
	}
	if (index($line, "/usr/sbin/apachectl stop ;;") >= 0) {
		$line = "/usr/local/apache/bin/apachectl stop ;;\n";
	}
	if (index($line, "/usr/sbin/apachectl restart ;;") >= 0) {
		$line = "/usr/local/apache/bin/apachectl restart ;;\n";
	}
	print NEWFILE $line;

}

close (FILE);
close (NEWFILE);
