<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class GenericTripleLinker extends Controller
{

	public function linkSaver(Request $request, $durc_type_left,$durc_type_right,$durc_type_tag){


		$link_table = $durc_type_left."_$durc_type_right"."_$durc_type_tag";
	
		$error_msg = "Error: ";
		$has_error = false;

		$left_id = $durc_type_left."_id";
		$right_id = $durc_type_right."_id";
		$tag_id = $durc_type_tag."_id";

		if($left_id == $right_id){
			//then we rename the same way we did when we setup the table..
			$right_id = 'second_' . $durc_type_right . '_id';
		}
	
	
		$left_ids = $request->input($left_id);
		if(is_null($left_ids)){
			$has_error = true;
			$error_msg .= "Nothing on the left to link...\n";
		}
		$right_ids = $request->input($right_id);
		if(is_null($right_ids)){
			$has_error = true;
			$error_msg .= "Nothing on the right to link...\n";
		}
		$tag_ids = $request->input($tag_id);
		if(is_null($tag_ids)){
			$has_error = true;
			$error_msg .= "No tags to link...\n";
		}
		$link_note = $request->input('link_note');
		//nothing in the link_note is fine and typical.

		if($has_error){
			echo $error_msg;
			exit();
		}	


		$total_links_created = 0;
	
	
		$all_linkers = [];	
	
		foreach($left_ids as $this_left_id){
			foreach($right_ids as $this_right_id){
				foreach($tag_ids as $this_tag_id){
	
					
					$find_array = [
						$left_id => $this_left_id,
						$right_id => $this_right_id,
						$tag_id => $this_tag_id,
					];
					$data_array = [
						'is_bulk_linker' => 1,
						'link_note' => $link_note,							
					];

					$class_name = "\App\\$link_table";

					$linker_object = $class_name::updateOrCreate($find_array,$data_array);
					$all_linkers[] = $linker_object;
					$total_links_created++;

				}
			}
		}

		$go_back_url  = "/genericLinkerForm/$durc_type_left/$durc_type_right/$durc_type_tag";
		$view_all_url = "/Zermelo/DURC_$durc_type_left"."_$durc_type_right"."_$durc_type_tag";
			
		return view('linker.triple_link_created',[
			'total_links_created' => $total_links_created,
			'go_back_url' => $go_back_url,
			'view_all_url' => $view_all_url,
		]);	

	}



	public function linkForm($durc_type_left,$durc_type_right,$durc_type_tag){


		$left_class = "\App\\$durc_type_left";
		$right_class = "\App\\$durc_type_right";
		$tag_class = "\App\\$durc_type_tag";
		
		if(!class_exists($left_class)){
			return("Error $durc_type_left does not exist as DURC");
		}
		if(!class_exists($right_class)){
			return("Error $durc_type_right does not exist as DURC");
		}
		if(!class_exists($tag_class)){
			return("Error $durc_type_tag does not exist as DURC");
		}

		$pdo = \DB::connection()->getPdo();

		$db = \Config::get('database.connections.'.\Config::get('database.default').'.database');
		//this returns null for some reason? But this is where this come from..
		
		//Allow for a custom linker database...
		$db = env('LINKER_DATABASE',false);
		if(!$db){
			$db = env('DB_DATABASE');
		}
		$pdo->query("USE $db");

		$link_table = $durc_type_left."_$durc_type_right"."_$durc_type_tag";

		$durc_tag_id = $durc_type_tag . '_id';
		$durc_left_id = $durc_type_left . '_id';
		$durc_right_id = $durc_type_right . '_id';

		if($durc_type_right == $durc_type_left){
			//this is OK, but the field names need to be changed.
			$durc_right_id = 'second_' . $durc_type_right . '_id';
		}


		$link_class = "\App\\$link_table";

		if(!class_exists($link_class)){
			//so the class does not exist yet. Thats fine.
			//we support autolinking as long as the left, right and tag tables exist...
			//so see if we have a database table.
			return $this->showSQLView($durc_type_left,$durc_type_right,$durc_type_tag);
		}
		//here we know that we have DURC classes for all 4 of the relevant data contructs...
		//the list of tags, the object that sits to the right and the left of the tag relation...
		//we are ready to show the Select2 heavy interface that will allow for really fast tagging...

		
		
		$view_data = [
			'durc_type_left' => $durc_type_left,
			'durc_type_right' => $durc_type_right,
			'durc_type_tag' => $durc_type_tag,
			'durc_left_id' => $durc_left_id,
			'durc_right_id' => $durc_right_id,
			'durc_tag_id' => $durc_tag_id,
			'durc_linker' => $link_table,
			'durc_linker' => $link_table,
		];

		return view('linker.triple_main',$view_data);

	}

	//we have this as a second function so that we can see this anytime we want
	//but in production this code will get called by the linkForm function... 
	public function showSQLView($durc_type_left,$durc_type_right,$durc_type_tag){


		$left_class = "\App\\$durc_type_left";
		$right_class = "\App\\$durc_type_right";
		$tag_class = "\App\\$durc_type_tag";
		
		if(!class_exists($left_class)){
			return("Error $durc_type_left does not exist as DURC");
		}
		if(!class_exists($right_class)){
			return("Error $durc_type_right does not exist as DURC");
		}
		if(!class_exists($tag_class)){
			return("Error $durc_type_tag does not exist as DURC");
		}

		$pdo = \DB::connection()->getPdo();

		$db = \Config::get('database.connections.'.\Config::get('database.default').'.database');
		//this returns null for some reason? But this is where this come from..
		
		//Allow for a custom linker database...
		$db = env('LINKER_DATABASE',false);
		if(!$db){
			$db = env('DB_DATABASE');
		}
		$pdo->query("USE $db");

		$link_table = $durc_type_left."_$durc_type_right"."_$durc_type_tag";

		$durc_tag_id = $durc_type_tag . '_id';
		$durc_left_id = $durc_type_left . '_id';
		$durc_right_id = $durc_type_right . '_id';

		if($durc_type_right == $durc_type_left){
			//this is OK, but the field names need to be changed.
			$durc_right_id = 'second_' . $durc_type_right . '_id';
		}


			//so it is possible that the table could exist  at this point..
			$test_sql  =  "
SELECT COUNT(*) as table_count
FROM information_schema.tables
WHERE table_schema = '$db' 
    AND table_name = '$link_table'
";

			$stmt = $pdo->query($test_sql);
	
			$is_db_exists = false;	
			while($this_row = $stmt->fetch()){
				if($this_row['table_count'] > 0){
					$is_db_exists = true;
				}
			}


			$is_tag_distinct = true;
			if($durc_type_left  == $durc_type_tag){
				$is_tag_distinct = false;
			}
			if($durc_type_right  == $durc_type_tag){
				$is_tag_distinct = false;
			}

			if(!$is_tag_distinct){
				echo "Error: While it is possible to have the same table on the left and right of the linker, the tag column must not be the same as either the left or the right";
				exit();
			}

		$durc_tag_name_field = $tag_class::getNameField();
		$durc_left_name_field = $left_class::getNameField();
		$durc_right_name_field = $right_class::getNameField();


				$message = "
## Here is the SQL to create the table

CREATE TABLE IF NOT EXISTS $db.$link_table  ( 
	`id` INT(11) NOT NULL AUTO_INCREMENT ,  
	`$durc_left_id` INT(11) NOT NULL ,  
	`$durc_right_id` INT(11) NOT NULL ,  
	`$durc_tag_id` INT(11) NOT NULL ,  
	`is_bulk_linker` TINYINT(1) NOT NULL DEFAULT '0' ,  
	`link_note` VARCHAR(255) DEFAULT NULL ,  
	`created_at` DATETIME NOT NULL DEFAULT current_timestamp() ,  
	`updated_at` DATETIME NOT NULL DEFAULT current_timestamp() ,    
	PRIMARY KEY  (`id`),
	UNIQUE KEY( $durc_left_id, $durc_right_id, $durc_tag_id )
	) ENGINE = MyISAM 
;

## Here is the SQL to create the graph browser

SELECT 
	CONCAT('$durc_type_right','_',$durc_right_id) AS source_id,
	rightside.$durc_right_name_field AS source_name,
	500 AS source_size,
	'$durc_type_right                             ' AS source_type,
	'$durc_type_right                             ' AS source_group,
	0 AS source_longitude,
	0 AS source_latitude,
	CONCAT('/DURC/json/$durc_type_right/',$durc_right_id) AS source_json_url,
	'' AS source_img,

	CONCAT('$durc_type_left','_',$durc_left_id) AS target_id,
	leftside.$durc_left_name_field AS target_name,
	500 AS target_size,
	'$durc_type_left                              ' AS target_type,
	'$durc_type_left                              ' AS target_group,
	0 AS target_longitude,
	0 AS target_latitude,
	CONCAT('/DURC/json/$durc_type_left/',$durc_left_id) AS target_json_url,
	'' AS target_img,
		
	100 AS weight,
	$durc_tag_name_field AS link_type,
	1 AS query_num

FROM $db.$link_table
JOIN $db.$durc_type_tag AS tag ON 
	tag.id =
	$durc_tag_id
JOIN $db.$durc_type_left AS leftside ON
	leftside.id =
	$durc_left_id 
JOIN $db.$durc_type_right AS rightside ON 
	rightside.id =
	$durc_right_id 

";
			// we could just run this SQL... but.. 
			// this is horribly risky from a security standpoint... leading to obvious DOS
			//instead we throw this back to the user...
			//$pdo->query($messageg);
			return view('linker.triple_create_table',['message' => $message]);	
	}


}
