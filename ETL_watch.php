<?php
/*
	This file understands how to download new copies of the data... 
	and to initiate futher processing
*/

require_once('vendor/autoload.php');
require_once('EbbHelper.class.php');

use Sunra\PhpSimple\HtmlDomParser;
use CedricZiel\FlysystemGcs\GoogleCloudStorageAdapter;
use League\Flysystem\Filesystem;

	//always change..
    	$bucket    = 'REPLACE_ME'; // needs to be changed with every script, should be something like ebb_yourthing. NOTE for now you need to create this bucket using the google console
		//Remember to use NEARLINE storage when you do this. The price point on this is the reason we use Google and not AWS
		//You MUST use "fine-grained" access control rather than "Uniform" because our library sets the access control during upload.. and that can break...

	//sometimes change..
	//this should NOT end in a '/'
	//
	$cloud_path = 'raw_file_mirror'; //this is an ok starting point NOTE for now you need to create this directory using the Google console!!

	//These should not change.
    	$projectId = 'savvy-summit-133807'; //this should likely not change
	$keyFilePath = __DIR__ . '/google_cloud.auth.json'; //should likely not change

	//Create the download helper.. this lets you translate between things on the Internet, and our file backup system easily.
	$DH = new EbbHelper(
			$projectId,
			$bucket,
			$keyFilePath
		);


	$urls_to_get = [];

	//TODO you need to figure out what urls you want to download here...
	//like urls_to_get[] = ['url' = "http://dommain.com/something/something/something/thefile.zip",
	//			'is_current' => false]; //this variable will control if a url is recorded as the current version...

	$is_new_data = false;

	foreach($urls_to_get as $url_data){
		$this_url = $url_data['url'];
		$is_current = $url_data['is_current'];

		$mirror_to_dir = "REPLACE_ME/"; //you can organize your data however you want, note the trailing slash!!

		$result = $DH->mirror_that($cloud_path,$this_url);
		//$result = $DH->mirror_that($cloud_path,$this_url,$file_name_override,$if_false_will_not_use_md5); //this is the full potential call...
		//note we actually use the full path below...
		if($result > 0){
			$is_new_data = true;
		}

		if($is_current){ //this should only be true once for each type of file..
			//so we use the extra arguments on mirror_that to force a file name and prevent the auto-add of an md5 to that name...
			$is_use_cloud_name = false; //we do not want to have the md5 and date for the current version..
			$file_name = 'current.zip'; //may need to adjust this!!!
			$DH->mirror_that($mirror_to_dir,$this_url,$file_name,$is_use_cloud_name);
		}
	}

//at this point you should confirm that this was run from cron using healthchecks.io
$hping_url = ''; // go get one from https://healthchecks.io/
file_get_contents($hping_url); // this will mean that assume this ran successfully... 
/*
	returns the standard...

 	< 0 - FAILED. An error occured and this script failed to properly run. 
 	= 0 - NO CHANGE. The current cloud version of this file is correct. 
 	> 0 - CHANGE. There is a new file in teh cloud and the next step needs to be processed

*/

//we would have exited with -1 before now if we had an error
if($is_new_data){
	exit(1); //there has neen a change..
}else{
	exit(0);
}		












