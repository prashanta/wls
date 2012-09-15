<?php
	$query = $_SERVER['QUERY_STRING']; 		    
	$rootFolder = substr(dirname($_SERVER["PHP_SELF"]),1);	
	$commentFile = ".comment";
	$statFile = ".statFile";
	$query = str_replace("%20", " ", $query);
?>
<html>
<head>
<title><?php echo $rootFolder."/".$query;?></title>
<link rel="stylesheet" type="text/css" href=".wls/style.css">
<link rel="stylesheet" type="text/css" href=".wls/wls-style.css">
<script type="text/javascript" src=".wls/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src=".wls/jquery.easing.1.3.js"></script>
<script type="text/javascript" src=".wls/jquery.tablesorter-2.0.5.min.js"></script>
<script type="text/javascript" src=".wls/jquery.metadata.js"></script>

<script>
/* STUFFS TO DO AFTER EVERYTHING LOADS */
$(document).ready(function() {
	/* Activate table sorter */
	$("table").tablesorter({cancelSelection: true})	
	var e = parseInt($(".title").css("width"));	
    var ef = parseInt($(".title").css("height"));	
	while (e > document.width) {
		var fs = parseInt($(".title").css("font-size")) - 1;
		$(".title").css("font-size",fs+"px");
		e= parseInt($(".title").css("width"));	
	}	
	while (ef > 40) {
		var fs = parseInt($(".title").css("font-size")) - 1;
		$(".title").css("font-size",fs+"px");
		ef= parseInt($(".title").css("height"));	
	}
	/* Link the linkables */
	$(".a1").click(function() {
  		//alert("going to : " + $(this).attr("href"));
	});
});
</script>
</head>
<body>

<?php
	if($query!=null && !is_dir($query)){
		echo "<div class='error'><span style='font-size: 30px'>404 occured!<br><br></span>";
		echo "<span>{ <b>".$rootFolder."/".$query."</b> } could not be found!<br>Goto <a href='".dirname($_SERVER["PHP_SELF"])."'>root folder</a></span></div>" ;
		return;
	}	
	
	$path = $rootFolder."/"; 	
	$fullPath = getcwd()."/"; 	
	if($query != ""){
		$path.=$query."/";
		$fullPath.=$query."/"; 
		$query.="/";
	}		
	// segment path and create chain link
	$bce = explode('/', $path);        		
	$ha = "";
	$hb = "";	
	for($i=0; $i<sizeof($bce); $i++)
	{
		if($bce[$i])
		{
			if($i>1 && $i < (sizeof($bce)-1))
				$hb .= "/";					
			if($i==0)
				$hb .= "";			
			else	
				$hb .= $bce[$i];
			// add path		
			$ha .= "&raquo;<a class='a2' href='?".$hb."'>".$bce[$i]."</a>";
		}
	}
?>	
	<div class="page-header">
		<span class='title'><?php echo $ha;?></span>	
	</div>		
	<br><br><br>
<?php
	// read files
	$comments = readPropFile($fullPath.$commentFile);			
	$stats = readPropFile($fullPath.$statFile);			
	
	// add page description
	if($comments != null)	
		echo "<div class='page-desc'>".$comments['pageDescription']."</div>";
		
	// add page description
	if($stats == null){
		//echo "<div>No statfile!</div>";	
    	// if in options, create_statfile_enabled create sat file
    }
	
	$filecount = 0;
	$dircount = 0;
	$tfs = 0;
	
	$dir_handle = @opendir($fullPath) or die("Unable to open $path"); 	
	$h = "";
	$files1 = scandir($fullPath);	
	if(count($files1) < 3){
		echo "<span class='error'>Nothing to see here yet!</span>";
		return;
	}
?>	
	<table id="table" class='tablesorter file-list' cellpadding=0 cellspacing=1>
		<thead> 
			<tr style="height: 30px;"> 
				<th class="col1 list-header colCommon">Name</th>
				<th class="col2 list-header colCommon">Kind</th>
				<th class="col3 list-header colCommon">Size</th>
				<th class="col4 list-header colCommon {sorter: 'date'}">Last Modified Date</th>
				<th class="col5 list-header colCommon">Comment</th>
			</tr>
		</thead>
		<tbody>
<?php
	while (false !== ($file = readdir($dir_handle))) 
	{ 	
    	if($file == "" || $file == "." || $file == ".." || $file == "index.php" || strpos($file,".") === 0 ) 
			continue; 
		if(is_dir($query.$file))
			$dircount += 1;	
		else
			$filecount += 1;
		$tfs = $tfs + filesize($query.$file);		
		$comment = "";
		if($comments){
			if(array_key_exists($file, $comments))
				$comment = $comments[$file];
		}
		$h .= addFileToList($query, $file, $comment, is_dir($fullPath.$file));        		
    }
    $h .= "</tbody></table>";    
	echo $h;
	echo "<br><br>".
		 "<div class='footer'>".		 	
		 	"<span id='left' class='aa'><span class='label_common label_files'></span><span class='val'>".$filecount."</span></span></span>".
	  	 	"<span id='center' class='aa'><span class='label_common label_dir'></span><span class='val'>".$dircount."</span></span>".
	  	 	"<span id='right' class='aa'><span class='label_common label_size'></span><span class='val'>".format_bytes($tfs)."</span></span>".
		 "</div>";
	
	closedir($dir_handle); 	
?> 
<div class='endnote'></div>
</body>
</html>

<?php

/*  ============================================================
    SOME PHP HELPERS    
    ============================================================ */ 

/* GENERIC READ PROPERTY FILE*/ 
function readPropFile($file)
{
	$ret = null;
	$lines;
	if (file_exists($file)){
		$lines = file($file);  		
		foreach ($lines as $line){
			if(!(strpos($line, '>>') === false)){
				list($k, $v) = explode('>>', $line);        	        	
				$ret[trim($k)] = trim($v);		
			}
		}		
	}
	/* Create one if it does not exist */
	/*
	else{
			$ourFileHandle = fopen($file, 'w') or die("can't open file");
			fwrite($ourFileHandle, "pageDescription >> A <i>.comment</i> file has been created. Please edit <i>.comment</i> file on this directory to add comments for each folder item.");
			fclose($ourFileHandle);
			$ret["pageDescription"] = "A <i>.comment</i> file has been created. Please edit <i>.comment</i> file on this directory to add comments for each folder item.";
		}
	*/
	return $ret;
}

/* ADD A ROW TO SORTABLE */
function addFileToList($path, $file, $comment, $dir){			
	$href = "";			
	$label = $file;
	if($dir==1){					
		$href = "?".$path.$file;	
		$label = "<b>".$file."</b>";
	}
	else{
		$href = $path.$file;
		$label = "<i>".$file."</i>";
	}
	$ext = ($dir? "" : substr($file,strrpos($file, ".")+1));
	$d  = "<tr style='height: 25px; padding: 0px 5px 0px 5px;'>";
	$d .= 	"<td class='col1 colCommon'><a class='a1' href='".$href."'>".$label."</a></td>";	
	$d .= 	"<td class='col2 colCommon'>".$ext."</td>";
	$d .= 	"<td class='col3 colCommon'>".($dir? "" : format_bytes(filesize($path.$file)))."</td>";
	$d .=	"<td class='col4 colCommon'>".($dir? "" : (date("M d, Y h:i A", filemtime($path.$file))))."</td>";	
	if($comment)			
		$d .=	"<td class='col5 colCommon'><div><center>".$comment."</center></div></td>";
	else
		$d .=	"<td class='col5 colCommon'></td>";	
	$d .= "</tr>";				
	return $d;
}

function format_bytes($size){
	$units = array(' Bytes', ' KB', ' MB', ' GB', ' TB');
    for ($i = 0; $size >= 1024 && $i < 4; $i++) 
    	$size /= 1024;
	return round($size, 2).$units[$i];
}
?>
