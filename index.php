<?php
	
	include("parse_functions.php");
	$root_dir    = "C:\\bc\\";
	
	// array_diff() function removes the element searched with its index.
	// Use foreach() to loop through the array properly. 
	 
	// scan the contents of the root directory and store it in $root_contents array.
	$root_contents = array_diff(scanDIR($root_dir), array('..', '.')); 
	
	// Loop through each directory contents.
	foreach($root_contents as $d) {
	
		if(is_DIR($root_dir.$d)) { //check if valid directory
			if(is_numeric((int)$d)){//check if the name is valid
			
				// $d = birth certificate year.
				
				// scan the contents of the current year and store it in $volumes array.
				$volumes = array_diff(scanDIR($root_dir.$d), array('..', '.'));
				$total_volumes = count($volumes);
				$volume_counter = 1;
				
				// Loop through each volumes.
				foreach($volumes as $v) {
				
					// scan the contents of the current volume and store it in $files array.
					$files = array_diff(scanDIR($root_dir.$d."\\".$v), array('..', '.'));
					
						//loop through each files
						foreach($files as $f) { // $f = "jsonfile.json"
							
							//check if the file name is = "jsonfile.json"
							if(!is_DIR($root_dir.$d."\\".$v."\\".$f) && $f == "jsonfile.json") {
								
								$dir_path = $root_dir.$d."\\".$v."\\"; // set the json  path ex. "C:\bc\BC 2005 000001-000200 1_2-3\"
								
								// parse the volume name and extract details
								$vInfo = parse_volume_name($v);
								
								// store volume details to the database and return the insert id
								$volume_id = insert_volume_details($vInfo);
								
								// decode the json file
								$jsonObject = json_decode(file_get_contents($dir_path.$f));
								
								// count the number of records inside the json object.
								$total_records = count($jsonObject);
								
								$record_counter = 1; // used for displaying status.
								
								foreach($jsonObject as $jo){
									// Processing: YEAR 2005 -> BC 2005 000001-000200 1_2-3 >> [ 1 of 500 ]
									// Inserting record: 1 of 400
									// Skipped - duplicate exist. or Done
									echo "Processing: ". $d . "\\" . $v . "->" . "[" . $volume_counter . " of " . $total_volumes."]\n";
									echo "Inserting record: " . $record_counter . " of " . $total_records ."\n";
									
									//insert birth certificates
									$bc_id = insert_bc_details($jo,$volume_id);
									
									if($bc_id){
										//insert all the birth certificate attachments
										foreach($jo->attachment as $file){
											insert_files($bc_id,$dir_path,$f);
										}
										echo "Done.\n";
									}else{
										echo "Skipped - a duplicate exist.\n";
									}
									$record_counter++;
								}
								break;
							}else{
								//log
							}
						}
					$volume_counter++;
				}
			}else{
				//log directories that are not valid year name.
				if(is_DIR($root_dir.$d)&& is_numeric((int)$d)) {
				
				}
			}
		}
	}
?>
