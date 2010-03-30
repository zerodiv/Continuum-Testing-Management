#!/bin/sh

##
# Truncate the testing tables.
##
mysql --user=root --password=root ctm < dump.sql
