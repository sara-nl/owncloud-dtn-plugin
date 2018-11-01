# DTN ownCloud plugin application

## Getting Started
Use this application to add large file transfer capabilities to your ownCloud instance.

### Installing
Create a new 'dtn' directory in the ownCloud instance 'apps' dir and add the 'apps/dtn' files to it.

Add the following setting to the server config/config.php file: 
> 'filesystem_check_changes' => 1

This makes sure that transfered files will be discovered by ownCloud.