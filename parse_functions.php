<?php
	
	include("database.php");
	
	function parse_volume_name($v){
		$vArray = explode(" ", $v);
		/*	[0] => BC
			[1] => 2012
			[2] => VOL.
			[3] => 1
			[4] => 000001-000200
			[5] => 1_2-3
		*/
		$vInfo = array();
		$vInfo["name"] = $v;
		$vInfo["number"] = $vArray[3];
		$vInfo["year"] = trim($vArray[1]); 
		$vInfo["series_id"] = trim($vArray[1])."-".trim($vArray[4]); //must use "2013-000001-000200" format to be unique
		
		if(strlen($vArray[5])> 0){
			$vArray[5] = preg_replace("/_ok/","",$vArray[5]);

			$date = explode("-",$vArray[5]);
			/*
				[0] => 1_2
				[1] => 3
			*/
			
			$dr = array();
			foreach($date as $d){
				$nd = explode("_",$d);
				
				if(count($nd)>1){
					array_push($dr, $nd[0],$nd[1]);
				}else{
					array_push($dr, $nd[0]);
				}
			}
			
			if(count($dr) > 3){
				$vInfo["received_start"] = $vArray[1]."-".$dr[0]."-".$dr[1];
				$vInfo["received_end"] = $vArray[1]."-".$dr[2]."-".$dr[3];
			}else if(count($dr) > 2){
				$vInfo["received_start"]  = $vArray[1]."-".$dr[0]."-".$dr[1];
				$vInfo["received_end"] = $vArray[1]."-".$dr[0]."-".$dr[2];
			}else{
				$vInfo["received_start"]  = $vArray[1]."-".$dr[0]."-".$dr[1];
				$vInfo["received_end"] = $vArray[1]."-".$dr[0]."-".$dr[1];
			}
			$vInfo["encoding_start"] = "0000-00-00 00:00:00";
			$vInfo["encoding_stop"] = "0000-00-00 00:00:00";
			$vInfo["encoding_status"] = 1;
			$vInfo["dt_verified"] = "0000-00-00 00:00:00";
			$vInfo["verified_status"] = 0;
			
		}
		return $vInfo;
	}
	
	function insert_bc_details($jsonObject,$vid){
		echo $jsonObject->registrationNumber;
		$query = "SELECT registry_no FROM birth_certificate WHERE registry_no='".$jsonObject->registrationNumber."'";
		$bc = dbQuery($query);
		
		if(!$bc){
			$query = "INSERT INTO birth_certificate (
					registry_no,
					volume_id,
					date_of_registration, 
					firstname,
					middlename, 
					lastname, 
					sex,
					date_of_birth, 
					place_of_birth, 
					mother_firstname,
					mother_middlename, 
					mother_lastname, 
					mother_citizenship, 
					father_firstname,
					father_middlename, 
					father_lastname, 
					father_citizenship, 
					parents_marriage_date,
					parents_marriage_place
				) VALUES (
					'".$jsonObject->registrationNumber."',
					'".$vid."',
					'',
					'".mysql_real_escape_string($jsonObject->firstName)."',
					'".mysql_real_escape_string($jsonObject->middleName)."',
					'".mysql_real_escape_string($jsonObject->lastName)."',
					'',
					'".mysql_real_escape_string(date("Y-m-d",strtotime($jsonObject->dateOfBirth)))."',
					'".mysql_real_escape_string($jsonObject->placeOfBirth)."',
					'".mysql_real_escape_string($jsonObject->motherFirstName)."',
					'".mysql_real_escape_string($jsonObject->motherMiddleName)."',
					'".mysql_real_escape_string($jsonObject->motherLastName)."',
					'',
					'".mysql_real_escape_string($jsonObject->fatherFirstName)."',
					'".mysql_real_escape_string($jsonObject->fatherMiddleName)."',
					'".mysql_real_escape_string($jsonObject->fatherLastName)."',
					'',
					'',
					''
				)";
					
			$id = dbQuery($query);
			$id = $id["mysql_insert_id"];
			return $id;
		}
		return 0;
	}
	
	function insert_files($bc_id,$volume_path,$file){
		if($bc_id != null){
			$query = "INSERT INTO attachment(
					file_name,
					file_path,
					birth_certificate_id)
				VALUES(
					'".$file."',
					'".mysql_real_escape_string($volume_path.$file)."',
					'".$bc_id."'
				)";
			dbQuery($query);
		}
	}
	
	function insert_volume_details($vInfo,$uid=1){
		/*	[series_id] => 000001-000200
			[user_id]
			[name] => BC 2013 VOL. 1 000001-000200 1_31-2_1
			[number] => 1
			[year] => 2013
			[date_received_start] => 2013-1-31
			[date_received_end] => 2013-2-1
			[encoding_start] => 2014-04-28 10:30:00
			[encoding_end] => 2014-04-28 11:30:00
			[encoding_status] => done
			[date_verified] => 2014-05-28 11:30:00
			[verification_status] => done
		*/
		
		$query = "SELECT series_id FROM volume WHERE series_id='".$vInfo["series_id"]."'";
		$series = dbQuery($query);

		if(!$series){
			$query = 
				"INSERT INTO volume(
					series_id,
					user_id, 
					name, 
					number, 
					year, 
					date_received_start,
					date_received_end,
					encoding_start,
					encoding_stop,
					encoding_status,
					date_time_verified,
					verification_status
				) VALUES(
					'" . $vInfo["series_id"] . "',
					'" . $uid . "',
					'" . mysql_real_escape_string($vInfo["name"]) . "',
					'" . $vInfo["number"] . "',
					'" . mysql_real_escape_string($vInfo["year"]) . "',
					'" . mysql_real_escape_string(date("Y-m-d",strtotime($vInfo["received_start"]))) . "',
					'" . mysql_real_escape_string(date("Y-m-d",strtotime($vInfo["received_end"]))) . "',
					'" . mysql_real_escape_string(date("Y-m-d",strtotime($vInfo["encoding_start"]))) . "',
					'" . mysql_real_escape_string(date("Y-m-d",strtotime($vInfo["encoding_stop"]))) . "',
					'" . mysql_real_escape_string($vInfo["encoding_status"]) . "',
					'" . mysql_real_escape_string(date("Y-m-d H:i:s",strtotime($vInfo["dt_verified"]))) . "',
					'" . mysql_real_escape_string($vInfo["verified_status"]) . "'
				)";
			$id = dbQuery($query);
			$id = $id["mysql_insert_id"];
			
			return $id;
		}else{
			$query = "SELECT id FROM volume WHERE series_id='".$vInfo["series_id"]."'";
			$id = dbQuery($query);

			$id = $id[0]["id"];
			
			return $id;
		}
	}
?>