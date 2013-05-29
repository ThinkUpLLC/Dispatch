#!/bin/bash

if [[ "$GEARMAND" != *gearmand ]]; then
	echo "GEARMAND enviormental var not defined"
else
	echo "Starting Gearman Server"
	$GEARMAND --verbose=DEBUG --listen=127.0.0.1
fi