#!/bin/sh

## Tom Roche, Durham Food Co-op, 2007 ##

INSTALLROOT="/pos"
URI="http://cloud.github.com/downloads/maxolasersquad/FLOSS-POS/floss-pos.tar.gz"
FN=$(basename $URI)
FP="/$FN"


mkdir -p $INSTALLROOT
wget -O $FP $URI
cd /
tar xfz $FP
$INSTALLROOT/installation/ubuntu/install_lane
