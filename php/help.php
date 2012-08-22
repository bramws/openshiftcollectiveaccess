<?php
$basedir = $_SERVER['DOCUMENT_ROOT'];
$hostname = $_SERVER['HTTP_HOST'];
$importdir = substr($basedir, 0, -9);
$datadir = $OPENSHIFT_DATA_DIR
print ("DATA_DIR : ");
print $datadir;
print ("Document ROOT : ");
print $basedir;
print ("<br>");
print ("\n\nHostname : ");
print $hostname;
print ("<br><br>");
print ("Example of a working setup.php configuration:\n");
print ("<br>");
print ("define(\"__CA_BASE_DIR__\", \"$basedir\");");
print ("<br>");
print ("define(\"__CA_SITE_HOSTNAME__\", \"$hostname\");");
print ("<br>");
print ("define(\"__CA_DB_HOST__\", '127.9.185.1');");
print ("<br>");
print ("define(\"__CA_DB_PASSWORD__\", 'VVPpFL8ZnsA9');");
print ("<br>");
print ("date_default_timezone_set('Europe/Brussels');");
print ("<br>");
print ("define(\"__CA_ADMIN_EMAIL__\", 'your-e-mail@domain.tld');");
print ("<br><br><br>");
print ("To use the import media plugin change the importRootDirectory in app/plugins/mediaImport/conf/mediaImport.conf to");
print ("<br>");
print ("importRootDirectory = $importdir/data/uploads");
?>
