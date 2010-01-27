#!/bin/sh

## Tom Roche, Durham Food Co-op, 2007 ##

INSTALLROOT="/pos"
URI="http://www.wedge.coop/is4c/download/pos.tar.gz"
FN=$(basename $URI)
FP="/$FN"


mkdir -p $INSTALLROOT
wget -O $FP $URI
cd /
tar xfz $FP
$INSTALLROOT/installation/ubuntu/install_server
