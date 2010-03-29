#!/bin/sh

##
# Truncate the testing tables.
##
mysql --user=root --password=root ctm < dump.sql

##
# dump the database to file
##
mysqldump --user=root --password=root ctm > ctm.sql
