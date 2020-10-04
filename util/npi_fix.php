<?php


/*

This is a utility that serves to covert a character representation of an NPI to a faithful BIGINT representation

Depends on 
CREATE TABLE resolvemap_npi.map AS SELECT npi AS resolve_from_npi, npi AS resolve_to_npi FROM `npi`


	STEPS:
* Create a new column in the database that is BIGINT.
* Copy all good NPIs (do the luhn check) to the new column.
* Load the bad npi map database, and copy over the existing pseudo npis
* Create new psuedo npis for any outstanding cruft.
* bounce.



*/


	chdir( dirname(__FILE__) );

	require_once('util/loader.php');
	require_oncE('util/npi_check.function.php');

//define a global variable mysql
	$mysql = DB::get(	$config["mysql_host"],
				$config["mysql_database"],
				$config["mysql_user"],
				$config["mysql_password"]);

	if(isset($argv[3])){

		$database = $argv[1];
		$table = $argv[2];
		$column = $argv[3];


	}else{
		echo "ERROR: arguments are [database] [table] [column]\n";
		var_export($argv);
		exit();
	}

	$new_column = $column ."_bigint";


/*	//how to do mysql_real_escape_string here?
	$sql = "
ALTER TABLE $database.$table ADD `$new_column` BIGINT(11) NOT NULL AFTER `$column`;
";

	echo "Adding new BIGINT field\n";
	$mysql->query($sql);
*/


	$sql = "
SELECT $column FROM $database.$table
GROUP BY $column
";

	$result = $mysql->query($sql);

	foreach($result as $result_id => $row){
		$npi = $row['npi'];

		$is_good = npi_check($npi);

		if($is_good){
		
			$update_sql = "
UPDATE $database.$table SET
	$new_column = '$npi'
WHERE $column = '$npi'
";

			echo '.';
			$mysql->query($update_sql);
		

		}


	}


/*
	$sql = "
ALTER TABLE $database.$table DROP `$new_column`
";	
	echo "Removing BIGINT field\n";
	$mysql->query($sql);
*/

