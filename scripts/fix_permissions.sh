#!/bin/bash

echo "changing all group ownership to careset"
chgrp careset * -R

echo "changing the .git directory specifically"
chgrp careset .git/* -R
chgrp careset .git

echo "change the ownership of the storage directory to be writeable by apache"
chown www-data:careset storage -R

echo "ensure that members of the careset group can read, and write to the files. Ensure that new files have the same permission with the sticky bit"
chmod g+rw * -R
chmod g+rw .git/* -R
chmod g+rw .git

echo "add the +s permission, but only to the directories.."
find ./ -type d | xargs chmod g+s

echo "This only works if your user is a member of the careset group. Do you see your user listed below?"
members careset

