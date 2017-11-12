<?php


echo 
	 " ### #### ### VIRTUAL DISK SPACE #### ### ### ", PHP_EOL, 
	 " ### # E X A G O N A L <> P R O J E C T # ### ", PHP_EOL, 
	 " ############################################ ", PHP_EOL,
	 " ### MANSINCAL PATRICK STANISLAS 2016-2050 ## ", PHP_EOL,
	 " ### synnus@gmail.com Tous droits reserves ## ", PHP_EOL,
	 " ############################################ ", PHP_EOL,
	 " #### ISO EXAGONAL PROJECT COMPACT BIN   #### ", PHP_EOL,
	 " ####         V 0.1.00.10A 2016          #### ", PHP_EOL,	
	 " ############################################ ", PHP_EOL,
	 PHP_EOL, PHP_EOL; 


	
	switch($argv[1]) {
		case '-extract': extract_file($argv[2],$argv[3]); break;
		case '-compact': compact_file($argv[2],$argv[3],$argv[4],$argv[5]); break;
		case '-addcmpt': addcmpt_file($argv[2],$argv[3],$argv[4]); break;
		case '/?':
		case '-help':
		default:
		echo 'commande line : ' , PHP_EOL, PHP_EOL;
		echo '-extract <diskname> <filename or -all or -list>' , PHP_EOL;
		echo '-addcmpt <diskname> <filename> <compress_level[-0-9] or -nocmp>' , PHP_EOL;
		echo '-compact <diskname> <folder_path> <compress_level[-0-9] or -nocmp> <size_disk_byte or -auto or -max 4go> <block_rand_max[4-8] or -autorand>' , PHP_EOL, PHP_EOL, PHP_EOL;
	}
	

	function compact_file($diskname,$afolder,$cmp=4,$size_disk=4294967296,$bande=2) {
		
		if($cmp == '') {$cmp=4;}
		if($bande == '' or $bande == '-autorand') {$bande=2;}
		if($size_disk == '-max') { $size_disk=4294967296; }
		
		if($size_disk == '' or $size_disk == '-auto') {
			$x_size = 32768 * 8;
			$dx = dir($afolder);
			echo "Pointeur : " . $dx->handle . "\n";
			echo "Chemin : " . $dx->path . "\n";
			while (false !== ($ent = $dx->read())) {
				if($ent[0] != '.') {					
					$x_size += filesize_cmd($dx->path . '\\' . $ent);
				}
			}
			$size_disk = $x_size;
		}

		
		echo 'COMPACT files : ... ',PHP_EOL;
		echo $diskname, ' -- ' ,$afolder, ' -- ' ,$zip, ' -- ' ,$size_disk, ' --' , $bande , PHP_EOL,PHP_EOL;
	
	$diskname = str_ireplace(array('.exa','.exh'),'',$diskname);
	
	 $yb = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
	 $yc = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);	 
	 $xzero = array($yb,$yb,$yb,$yb);	 	 
	 $z = array($yc,$yc,$yc,$yc);
	 
	 $zsdd = round($size_disk / 32768);
	 
	
	$disk['exclu'] = array_fill(0,$zsdd,array(0=>3,1=>4,2=>64));
	$disk['block'] = array_fill(0,$zsdd,$z);
	
	// préparation du disk et retour du nombre de block disponible
	$dtf = prep_disk_space($disk);
	
		echo "\0Temps de l\'analise : " . $dtf[3] .' s',PHP_EOL;		
		echo "\0 Nombre de block 512 octets libre : " , $dtf[2] , PHP_EOL;
		echo "\0 Nombre de block 8192 octets libre : " , $dtf[1] , PHP_EOL;
		echo "\0 Nombre de block 32768 octets libre : " , $dtf[0] , PHP_EOL, PHP_EOL;
		echo "\0Espace libre : " , ($dtf[2] * 512) , ' octets', PHP_EOL;	
	
		
		$erreurs = ' erreurs : ';

		CreatFileDummy($diskname . '.exa',$size_disk);
		
		$newhead = '';
		$e = fopen($diskname . '.exh', "w");
		$f = fopen($diskname . '.exa', "r+b");


		 //$erreurs .= copy_files($disk,$e,$f,$file_name);	
		@mkdir('tmp\\', 0777, true);
	
		//$param = array('blocks' => 9, 'work' => 0);
		$d = dir($afolder);
		echo "Pointeur : " . $d->handle . "\n";
		echo "Chemin : " . $d->path . "\n";
		while (false !== ($entry = $d->read())) {
		   if($entry[0] != '.') {
			   
			   echo $d->path,'\\',$entry, '#' , base64_encode($d->path . '\\' . $entry) , ' -- ', $md5file ,PHP_EOL;
			
					if($cmp == '-nocmp') { copy($d->path . '\\' . $entry, 'tmp\\' . $entry . '.pgz'); }
					else {
						pgz_file($d->path . '\\' . $entry,$cmp);
					}
				


				$erreurs .= copy_files($disk,$e,$f,'tmp\\' . $entry . '.pgz',$xzero,$yb,$d->path . '\\' . $entry,$bande,$newhead,$cmp);
				//unlink('tmp\\' . $entry . '.pgz');
				
				
		   }
		}
		$d->close();
		
		
		fclose($f);
		fclose($e);
		
		$q = fopen($diskname . '.hxa', "w");
		fwrite($q,'[HEAD]' . base64_encode(bzcompress($newhead)) . '[/HEAD]');
		
		$ddd = ''; $sp=0;
		foreach($disk['block'] as $kb => $block) {
			foreach($block as $kp => $portion) {
				$ddd .= dechex(bindec(implode('', $portion)));
				$sp++;
			}
		}
		
		echo PHP_EOL,PHP_EOL;
		echo '>>>', strlen($ddd), ' -- ' , strlen(base64_encode(bzcompress($ddd))), ' -- ', $sp , '--' , $sp * 8192,PHP_EOL;
		echo PHP_EOL,PHP_EOL;
		
		fwrite($q,"\n");
		fwrite($q,'[DISK]' . base64_encode(bzcompress($ddd)) . '[/DISK]');
		
		fclose($q);
		
		echo PHP_EOL, $erreurs, PHP_EOL, PHP_EOL, " === END ===", PHP_EOL, PHP_EOL;
		
		$dtf = prep_disk_space($disk);
	
		echo "\0Temps de l\'analise : " . $dtf[3] .' s',PHP_EOL;		
		echo "\0 Nombre de block 512 octets libre : " , $dtf[2] , PHP_EOL;
		echo "\0 Nombre de block 8192 octets libre : " , $dtf[1] , PHP_EOL;
		echo "\0 Nombre de block 32768 octets libre : " , $dtf[0] , PHP_EOL, PHP_EOL;
		echo "\0Espace libre : " , ($dtf[2] * 512) , ' octets', PHP_EOL;
		echo "\0Espace libre conseiller : " , $size_disk - ($dtf[2] * 512) , ' octets', PHP_EOL;		
		
	}
	
	function extract_file($diskname,$filexname='-all') {
		
		
		
		$diskname = str_ireplace(array('.exa','.exh'),'',$diskname);
		
		$f = fopen($diskname . '.exa', "r");
		
		$lines = file($diskname . '.exh');

			// Affiche toutes les lignes du tableau comme code HTML, avec les numéros de ligne
			foreach ($lines as $line_num => $line) { // : ,- ;
			
			
				$l = trim($line);
				list($cmpz,$path,$md51,$md52,$taille,$offset) = explode(':',$l);
				$taille = hexdec($taille);
				$xof = explode(',',$offset);
					
					$ypath = base64_decode($path);
					$xpath = pathinfo($ypath);
				
				if( $filexname == '' or $filexname == '-all' or $filexname == $xpath['basename'])  {
				
						@mkdir($xpath['dirname'], 0777, true);
						@mkdir('tmp\\', 0777, true);	
					
					$g = fopen('tmp\\' . $xpath['basename'] . '.pgz', "wb");
					$ctxof = count($xof);
					foreach($xof as $k => $y) {
						echo ' -> extract : ' , $k , ' / ' , $ctxof, "\r"; 
						if($y != '') {
							if($y[strlen($y)-1] == ';') { 
								$y = str_ireplace(';','',$y);
								
								list($ofz,$lght) = explode('-',$y);
								
								$bin = read_offset($f,hexdec($ofz),hexdec($lght));
								
							}
							else {
								
								$bin = read_offset($f,hexdec($y),$taille);
								
							}
						}
						
						fwrite($g, $bin);

						
					}
					
					fclose($g);
					echo $ypath, ' -- ' , $path ,PHP_EOL;
					pgz_extract($cmpz,'tmp\\' . $xpath['basename'] . '.pgz',$ypath, $md51 , $md52 );
					echo PHP_EOL;
				}
				else if($filexname == '-list') {
					
					echo $line_num,' - ',$ypath, PHP_EOL;
					
				}
			}
		
		
		fclose($f);
		
		echo PHP_EOL, PHP_EOL, PHP_EOL, " === END ===", PHP_EOL, PHP_EOL;
	}

	function pgz_extract($c,$srcName, $dstName, $md51 , $md52 ) { 
    
	$md5file = md5_file($srcName);
	
	if($md5file == $md51) { echo 'MD51 : ok', PHP_EOL; }
	else{ echo 'MD51 : no', PHP_EOL; }
	
	if($c == 1) {
		$sfp = gzopen($srcName, "rb");
		$fp = fopen($dstName, "w");

		while (($bin = gzread($sfp,4096))) {
			fwrite($fp, $bin);		
		}	

		gzclose($sfp);
		fclose($fp);
	}
	else {
		copy($srcName, $dstName);
	}
	
	
	$md5file2 = md5_file($dstName);

	if($md5file2 == $md52) { echo 'MD52 : ok', PHP_EOL; }
	else{ echo 'MD52 : no', PHP_EOL; }
	
	}
	
	
	
	function pgz_file($path,$cmp=4,$where='tmp\\') {
		
	
		$ph = pathinfo($path);
		$filname = $ph['basename'];
	
		$f_size = filesize_cmd($path);
		
		
		$fd = fopen($where . $filname . '.pgz', "wb");
		$handle = fopen($path, "rb");
		
		$bin='';
		$lt = 0; $u = 0;
		$o=0;
		while ($o < $f_size) { 
			$bin = fread($handle, 32768);	
			if(($lt = @round((ftell($handle) * 100 )/ $f_size)) != $u) { $u = $lt;  echo ' --> compress: ' , $u ,"%\r"; }		
			fwrite($fd, gzencode($bin,$cmp));
			$o += strlen($bin);
		}
		
		fclose($handle);
		
		
		fclose($fd);
		

	}


	function copy_files(&$disk,$e,$f,$file_name,$xzero,$yb,$pathx,$bande,&$newhead,$cmp) {
	
	$erreurs = '';
	$f_size = filesize_cmd($file_name);
	
	if($f_size > 0 ) {
	
			$md5file = md5_file($file_name);
			$md5file2 = md5_file($pathx);
			
			
			if($cmp == '-nocmp') { $zcmp = 0; } else { $zcmp = 1; }
			
			
			$newhead .= $zcmp . "\0" . $pathx . "\0" . $md5file2 . "\0";
		    fwrite($e, $zcmp . ':' . base64_encode($pathx) . ":" . $md5file . ':'  . $md5file2 . ':');
	
	
	echo ' size : ' , $f_size , ' : ';
	$type = 0;
	
	if($f_size >= 32768) { // copy et recherche à 32768 octet
		
		
		$nombre_portion = ceil($f_size / 32768);
		echo '-> block de 32 : ' , $nombre_portion, PHP_EOL;
		$block_list = block_plage($disk,$nombre_portion,32,$bande);
		$type = 32768;
	
		if($block_list === false) { 
			$nombre_portion = ceil($f_size / 8192);
			echo ' <- 0 :: block de 8 : ' , $nombre_portion, PHP_EOL;
			$block_list = block_plage($disk,$nombre_portion,8,$bande);
			$type = 8192;
			
			if($block_list === false ) { 
				$nombre_portion = ceil($f_size / 512);
				echo ' <- 0 :: block de 512 : ' , $nombre_portion, PHP_EOL;
				$block_list = block_plage($disk,$nombre_portion,512,$bande);
				$type = 512;
			}
		}
	
	}
	else if($f_size >= 8192 and $f_size < 32768) { // copy et recherche à 8192 octet
		
		$nombre_portion = ceil($f_size / 8192);
		echo '-> block de 8 : ' , $nombre_portion, PHP_EOL;
		$block_list = block_plage($disk,$nombre_portion,8,$bande);
		$type = 8192;
		
		if($block_list === false ) {
			$nombre_portion = ceil($f_size / 512);
			echo ' <- 0 :: block de 512 : ' , $nombre_portion, PHP_EOL;
			$block_list = block_plage($disk,$nombre_portion,512,$bande);
			$type = 512;
		}
		
	
	}
	else if($f_size < 8192) { // copy et recherche à 512 octet
		
		$nombre_portion = ceil($f_size / 512);
		echo '-> block de 512 : ' , $nombre_portion, PHP_EOL;
		$block_list = block_plage($disk,$nombre_portion,512,$bande);
		$type = 512;
	}
	
	

		
		$d = fopen($file_name, "r");
		$progress=0;
		fwrite($e, dechex($type) . ':');
		$bq = count($block_list); $bq0=0;
		foreach($block_list as $k => $of) {	$bq0++;
			

			
			if($of[1] == 0) { 
				$disk['block'][$of[2]] = array(
											array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1),
											array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1),
											array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1),
											array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1)
										);
			
			}
			else if($of[1] == 1) { 
				$disk['block'][$of[2]][$of[3]] = array(1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1);
	
			}
			else if($of[1] == 2) { 
				$disk['block'][$of[2]][$of[3]][$of[4]] = 1;
			}

				
				$disk['exclu'][$of[2]][0]=0;
				$disk['exclu'][$of[2]][1]=0;
				$disk['exclu'][$of[2]][2]=0;
				
				foreach($disk['block'][$of[2]] as $kb8 => $portion) {
					
					$nb_parties = 0;
					
					foreach($portion as $kp512 => $parties) {
						
						if($parties == 0) { 
							$nb_parties++; 
							$disk['exclu'][$of[2]][2]++;
							$t512b++;
						}
						
						
					}
					
					if($nb_parties == 16) { $disk['exclu'][$of[2]][1]++; $t8b++; }
					
				}
				
				if($disk['exclu'][$of[2]][2] > 0) { $disk['exclu'][$of[2]][0] = 1; }
				if($disk['exclu'][$of[2]][1] > 0) { $disk['exclu'][$of[2]][0] = 2; }
				if($disk['exclu'][$of[2]][1] == 4) { $disk['exclu'][$of[2]][0] = 3; $t32b++; }
				
				
			
			
			$bin = read_offset($d,$progress,$type);
			$t_bin = strlen($bin);
			
			$ofset = intval($of[0]);
			
			if($t_bin != $type or $bq == $bq0) { $enx = '-' . dechex($t_bin) . ';'; $eny = '-' . dechex($t_bin); } else { $eny = $enx = ','; }
			
			$tentative = 0;		

			echo ' Copy -> ' , @round(($bq0* 100) / $bq) , "% \t offset: ",$ofset , " len: ", $t_bin , " \t | ";

			while(true) {
				$ofxxx = write_offset($f,$ofset,$bin);		
				if($bin == read_offset($f,$ofset,$t_bin)) { echo "yes \r"; fwrite($e,  dechex($ofxxx) . $enx); $newhead .= dechex($ofxxx) . $eny; break; } else { $tentative++; echo ";\t\tt:" . $tentative . "\n";  }
				if($tentative >= 100) { $erreurs .= $file_name . $ofset . ':' . $tentative . PHP_EOL; break;}
			}
			
			
			$progress += $type;
			
		}
		fwrite($e, "\n");
		$newhead .= "\n";
		fclose($d);
	
	}
	else {
		$erreurs .= 'ERR-SIZE: 0 -> ' . $file_name . ':' . $f_size . PHP_EOL;
		
	}
	
	return $erreurs;
	}	

	

	function CreatFileDummy($file_name,$size=4294967296) {   
		// 32bits 4 294 967 296 bytes MAX Size
		// div by 512 = 8388608
			echo '-> Creat File : ';
			$f = fopen($file_name, 'wb');
			if($size >= 1000000000)  {
				
				$z = ($size / 1000000000);       
				if (is_float($z))  {
					$z = round($z,0);
					fseek($f, ($size - ($z * 1000000000) -1), SEEK_END);
					fwrite($f, "\0");
				}       
				while(--$z > -1) {
					fseek($f, 999999999, SEEK_END);
					fwrite($f, "\0");
				}
			}
			else {
				fseek($f, $size - 1, SEEK_END);
				fwrite($f, "\0");
			}
			rewind($f);
			fclose($f);	
			echo ' FINISH',PHP_EOL;

		Return true;
		}	 
	
	
	function block_plage(&$disk,$portion=1,$type=512,$bande=4) {

		$block_list = array();
		foreach($disk['exclu'] as $k => $l) {	
			
			if($l[0] != 0) {
					
				if($type == 32 and $l[0] == 3) { 
					
					$block_list[]= array(($k * 32768),0,$k);
					
					if(count($block_list) > $portion * $bande) { break; }
				}
				else if($type == 8 and $l[1] > 0) {

						foreach($disk['block'][$k] as $j => $m) {
							
							if($m == array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0)) { // ($k * 32768) + ($m * 8192)
								
								$block_list[]= array((($k * 32768) + ($j * 8192)),1, $k , $j);
								
								if(count($block_list) > $portion * $bande) { break 2; }
							}
						}


					
				}
				else if($type == 512 and $l[2] > 0) {

					foreach($disk['block'][$k] as $g => $h) {
						
							foreach($h as $p => $i) {
								if($i == 0) {
									$block_list[]= array( (($k * 32768) + ($g * 8192) + ($p * 512)),2, $k , $g , $p);
									
									if(count($block_list) > $portion * $bande) { break 3; }
								}
							}
						
						
					}
					
				}
			}
		}

		
		if(empty($block_list) or count($block_list) < $portion ) { return false; }
		else {
			// mélange des bloques 
			shuffle_assoc($block_list);
			$block_list = array_slice($block_list, 0,$portion);
		
		return $block_list;
		}
	}	
	
	
	function prep_disk_space(&$disk) {
	
		
		$time_start = microtime(true);
	
		$t32b = 0;
		$t8b = 0;
		$t512b = 0;
		
		foreach($disk['block'] as $kb32 => $block) {
			
			$disk['exclu'][$kb32][0]=0;
			$disk['exclu'][$kb32][1]=0;
			$disk['exclu'][$kb32][2]=0;
			
			foreach($block as $kb8 => $portion) {
				
				$nb_parties = 0;
				
				foreach($portion as $kp512 => $parties) {
					
					if($parties == 0) { 
						$nb_parties++; 
						$disk['exclu'][$kb32][2]++;
						$t512b++;
					}
					
					
				}
				
				if($nb_parties == 16) { $disk['exclu'][$kb32][1]++; $t8b++; }
				
			}
			
			if($disk['exclu'][$kb32][2] > 0) { $disk['exclu'][$kb32][0] = 1; }
			if($disk['exclu'][$kb32][1] > 0) { $disk['exclu'][$kb32][0] = 2; }
			if($disk['exclu'][$kb32][1] == 4) { $disk['exclu'][$kb32][0] = 3; $t32b++; }
			
		}

	
		$time_end = microtime(true);
		$time = round($time_end - $time_start,2);

	
	return array($t32b,$t8b,$t512b,$time);
	}	
	

	function filesize_cmd($file) {
		$pth = pathinfo($file);		
		$fz = filesize($file);
		$fx = exec('forfiles /p ' . $pth['dirname'] . ' /m "' . $pth['basename'] . '" /c "cmd /c echo @fsize"');	
		if($fz != $fx) { return $fx; }
		return $fz;
	}	


		function write_offset($handel,$offset,$bin,$t_f_max=4294967296) {
				($offset > PHP_INT_MAX) ? fseek($handel,-($t_f_max-$offset),SEEK_END) : fseek($handel,$offset,SEEK_SET);
				fwrite($handel,$bin);
				return $offset;
		}
		
		function read_offset($handel,$offset,$nb,$t_f_max=4294967296) {
			($offset > PHP_INT_MAX) ? fseek($handel,-($t_f_max-$offset),SEEK_END) : fseek($handel,$offset,SEEK_SET);
			return fread($handel, $nb);
		}
		

		function shuffle_assoc(&$array) {
				$keys = array_keys($array);

				shuffle($keys);

				foreach($keys as $key) {
					$new[$key] = $array[$key];
				}

				$array = $new;

				return true;
		}

?>