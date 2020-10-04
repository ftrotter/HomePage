<?php
/*
	This file is simply a gateway to calling other project files...
	we use a straight php script in case we need to call something other than artisan 
	or python etc etc...
	
	This file should really have the following pattern: 
	* do something > /log/logsomething...
	* do something else > /log/logsomething_else...
	* etc 
	* etc
	* file_get_contents('Your healthchecks.io health URL');


we are presuming this is going to go into crontab -e
use crontab -e to avoid using root-level permissions with cron whenever possible.. as per: 
https://askubuntu.com/questions/609850/what-is-the-correct-way-to-edit-a-crontab-file
*/	
	$cmds = [];

	//put any commands you want to run here... 
	$cmds[] = "/usr/bin/php /var/www/REPLACEME/artisan YOURCOMMAND:YOURSUBCOMAND --YOURARG=YOURARGVALUE --YOURFLAG";

	foreach($cmds as $this_cmd){
		$now = date('l jS \of F Y h:i:s A');
		echo "Running \n\t$this_cmd\n\t$now\n";
		//we use system because that will cause the results to be echoed to the termnal and therefore logged..
		$last_line = system($this_cmd);
		if($last_line != 'all done.'){
			echo "$this_cmd returned $return_var which is an error. Stopping\n";
			exit(); //Note that it is critical to end the process if a part ended in error... 
				//under no circumstance should we fall through to the hc-ping command file_get_contents... 
				//because doing that will make it seem like the code is working... even if there is a crashing error.
		}
	}

	//now we do the checkin... 
	file_get_contents('https://hc-ping.com/REPLACEME'); //make sure this working...
