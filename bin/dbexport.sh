#!/usr/bin/env bash
sudo mysqldump upnextfm > /tmp/upnextfm.sql
tar -czvf /tmp/upnextfm.sql.tar.gz /tmp/upnextfm.sql
scp /tmp/upnextfm.sql.tar.gz ubuntu@upnext.fm:/tmp
ssh ubuntu@upnext.fm 'cd /tmp && tar -xzvf upnextfm.sql.tar.gz && sudo mysql upnextfm < tmp/upnextfm.sql && rm -rf /tmp/tmp && rm upnextfm.sql.tar.gz'
