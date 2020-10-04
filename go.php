#!/usr/bin/env php
<?php

	chdir(__DIR__); //there are bugs that prevent the scripts 
				//from being run "from anywhere" 
				//these have to do with processing the location of the data file
				//and need to be fixed eventually
	$commands = [];

	//list your commands here... use the tee to create a log and show the results to the screen!!
	//remember, all of your commands should return 'all done' as the last line when the work correctly. Anything else will be considered an error!!
	$commands[" Step10.. look in ./log/step10.log"] = "php Step10_something.php |& tee log/step10.log";

foreach($commands as $echo_me => $this_command){
	echo "Running $echo_me\n";
	$last_line = system($this_command);
	if($last_line == 'all done'){
		//then we have the magic phrase back..
		//nothing to do here..
	}else{
		//
		echo "Error: failed to return the magic phrase.. something went wrong\n";
		exit();
	}
}


//healthchecks.io url goes here
$hping_url = '';
file_get_contents($hping_url);

//even this script returns the magic 'all done' when it is working correctly...
echo "all steps completed correctly. \nall done.\n";
