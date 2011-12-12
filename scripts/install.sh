#!/bin/sh

echo "Plugin Installed!"; #NOT! :)

cd $DOCUMENT_ROOT; #this directory
cd ..
cd /usr/local/directadmin/plugins/MultiSite
for dir in user reseller admin; do
{
        chmod 755 $dir/*
        chown diradmin:diradmin $dir/*
}
done;
chown diradmin:diradmin /usr/local/directadmin/plugins/MultiSite -R
exit 0;
