#!/bin/sh

##
# dump the database to file
##
mysql --user=root --password=root ctm < ctm.sql
