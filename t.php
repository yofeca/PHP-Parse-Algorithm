<?php
	$str = "                      d                        2004 ";
	echo "<pre>".$str."</pre>";
	$str = preg_replace('/([\s])\1+/', ' ', $str);
	echo "<pre>".$str."</pre>";
	$str = trim($str);
	echo "<pre>".$str."</pre>";
	//echo trim($str);
	if($str=="d 2004"){
		echo "<pre>".$str."</pre>";
	}else{
		echo "xxx";
	}
	//echo is_numeric(trim($str));
?>