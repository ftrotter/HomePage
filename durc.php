<?php

	///HARDCODE HERE..
	//something like
	$hardcode_db_list = '--DB=lore --DB=DURC_aaa --DB=DURC_irs --DB=DURC_northwind_model --DB=DURC_northwind_data';


	$script_name = array_shift($argv);
	$database_list = $argv;
	$db_arg = false;

	//first use any hardcoded value
	if(strlen($hardcode_db_list) > 0){
		$db_arg = $hardcode_db_list;
	}


	//but if arguments are present respect them instead..
	if(count($database_list) > 0){
		$db_arg = '';
		foreach($database_list as $this_database){
			$db_arg .= " --DB=$this_database  ";
		}
	}

	//but we have to have something..
	if(!$db_arg){
		echo "Critical Error: you must either hard code the database parameters by changing this file... or enter them as the only arguments to this command\n";
		echo $db_arg;
		exit();
	}

$main_commands = [
	"php artisan DURC:mine --squash $db_arg", //mine the databases
	"php artisan DURC:write ", //generate the sourcecode automagically
];

	//for the main commands, we want to see the output, so we use system()
	foreach($main_commands as $this_command){
		echo "Running: $this_command\n";
		system($this_command,$return_status);
		
		//DURC tries to respect error codes for returned status.. so this works as expected.
		if($return_status > 0){ //then it retueded an error!!
			echo "Error: $this_command failed... returned $return_status stopping\n";
			exit(100);
		}
	}


$clean_up_commands = [
	//TODO this is clearly not the right way to do this...
	"cp routes/starting.web.php routes/web.php", //copy the route prefixes routes over... 
	"cat routes/web.durc.php | tail -n +2 >> routes/web.php", //copy the auto generated routes over
	"cat routes/ending.web.php >> routes/web.php", //copy the route closing contentn
	//these two are just for good measure... 
	"composer clear-cache > /dev/null 2>&1", //make composer see the new files
	"composer dump-autoload > /dev/null 2>&1", //and refresh the composer cache...
	"php artisan cache:clear",
	"php artisan route:clear",
	"php artisan config:clear ",
	"php artisan view:clear",

];

	//for the cleanup commands, we just want them to silently run... so we use exec without an echo
	foreach($clean_up_commands as $this_command){
		//echo "Running: $this_command\n";
		exec($this_command,$output,$return_status);
		
		//DURC tries to respect error codes for returned status.. so this works as expected.
		if($return_status > 0){ //then it retueded an error!!
			echo "Error: $this_command failed... returned $return_status stopping\n";
			exit(100);
		}
	}

echo "clean up tasks finished. all done\n";
