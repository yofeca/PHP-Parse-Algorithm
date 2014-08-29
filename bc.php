<?php
	
	include("parse_functions.php");
	
	//$rdr = "c:\\bc\\"; 
	$rdr = "/home/admin/Desktop/data/ccro/bc/";
	
	if(isset($argv[1])){
		$path = $rdr.$argv[1];
	}else{
		$path = $rdr."bc";
	}
	
	if(is_DIR($path)){
		
		$v = array_diff(scanDIR($path), array('..', '.'));
		
		$tv = count($v);
		$tctr = 1;
		
		foreach($v as $v){
		
			$files = array_diff(scanDIR($path."/".$v), array('..', '.'));
			
			foreach($files as $f){
			
				//if(!is_DIR($path."\\".$v."\\".$f) && $f == "jsonfile.json") {
				if(!is_DIR($path."/".$v."/".$f) && $f == "jsonfile.json") {
										
					$vInfo = parse_volume_name($v);
					$vid = insert_volume_details($vInfo);
					
					//$jsonObject = json_decode(file_get_contents($path."\\".$v."\\".$f));
					$jsonObject = json_decode(file_get_contents($path."/".$v."/".$f));
					
					$trec = count($jsonObject);
					$rc = 1;
					
					foreach($jsonObject as $jo){
						//echo "Processing: ". $path . "\\" . $v . "->" . "[" . $tctr . " of " . $tv."]\n";
						echo "Processing: ". $path . "/" . $v . "->" . "[" . $tctr . " of " . $tv."]\n";
						echo "Inserting record: " . $rc . " of " . $trec ."\n";
						
						$bid = insert_bc_details($jo,$vid);
						$echo $bid;
						if($bid){
							foreach($jo->attachment as $file){
								//insert_files($bid,$path."\\".$v."\\",$f);
								insert_files($bid,$path."/".$v."/",$f);
							}
							echo "Done.\n";
						}else{
							echo "Skipped - a duplicate exist.\n";
						}
						$rc++;
					}
					break;
				}
			}
			$tctr++;
		}
	}else{
		echo $path." is not a valid directory or directory does not exist.";
	}
	
?>
