<?php header('Content-Type: text/html; charset=utf-8'); ?>
<style>
.inline-block{
	display: inline-table;
	overflow: hidden;
	float: left;
	border: 1px solid #eee;
	margin: 4px;
	padding: 5px;
	background-color:#EEE;
	height:200px;
}
.inline-block center{
	overflow:hidden;
	height:150px;
}
.red,.green,.blue
	{
	display:block;
	overflow:hidden;
	height:10px;
	}
.red{
	background-color:red;
}
.green{
	background-color:green;
}
.blue{
	background-color:blue;
}
</style>
<?php
/*spl_autoload_register(function($class_name){include dirname($_SERVER['SCRIPT_FILENAME']).'/object/'.$class_name.'.php';});*/
set_time_limit(0);
$max_count=10;
//$url = "http://yariadom.ru";
//$div = "/foto-galereya/";
$url="";
$div="http://www.photosight.ru/search/search_word/?query=".urlencode($_POST['request'])."&look_at=all&sort_type=weight";
showForm();
$images = getImages($url,$div);
$x=0;
foreach($images[1] as $img)
	{
	$mid_color=array('r'=>0,'g'=>0,'b'=>0);
	$Image = new Imagick($url.$img);
	$img_geo=$Image->getImageGeometry();
	for($i=0;$i<=$img_geo['height'];$i++)
		{
		for($j=0;$j<=$img_geo['width'];$j++)
			{
			$pixel_color=$Image->getImagePixelColor($i,$j)->getColor();
			$mid_color['r']=$mid_color['r']+$pixel_color['r'];
			$mid_color['g']=$mid_color['g']+$pixel_color['g'];
			$mid_color['b']=$mid_color['b']+$pixel_color['b'];
			}
		}
	$pixels_count=$img_geo['width']*$img_geo['height'];
	showImagePreview($mid_color,$pixels_count,$url,$img,$img_geo);
	$x++;
	if($x==$max_count){exit(0);}
	}


function getImages($url,$div)
	{
	$ch = curl_init($url.$div);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.1 Safari/537.11');
	$output = curl_exec($ch);
	curl_close($ch); 
	preg_match_all('/src="([^"]{0,}\.jpg)"/', $output, $images);	
	return $images;
	}
function showImagePreview($mid_color,$pixels_count,$url,$img,$img_geo)
	{
	echo '<span class="inline-block">';
	$count_red=$mid_color['r']/$pixels_count;
	echo '<div class="red" style="width:'.(100/256*$count_red).'%" title="red: '.round($count_red,2).'"></div>';
	$count_green=$mid_color['g']/$pixels_count;
	echo '<div class="green" style="width:'.(100/256*$count_green).'%" title="green: '.round($count_green,2).'"></div>';
	$count_blue=$mid_color['b']/$pixels_count;
	echo '<div></div><div class="blue" style="width:'.(100/256*$count_blue).'%" title="blue: '.round($count_blue,2).'"></div>';
	$result='#'.dechex($mid_color['r']/$pixels_count).dechex($mid_color['g']/$pixels_count).dechex($mid_color['b']/$pixels_count);
	echo $result;
	echo'<br/>';
	echo $color=$_POST['color'];
	echo'<br/>';
	echo calculateRelevancePercent($result,$color);
//	echo '('.convertColor()['r'].', '.convertColor()['g'].', '.convertColor['b'].')';
	echo 'color relevance: ';
	echo'<br/>';
	echo '<center><img width="'.$img_geo['width'].'" height="'.$img_geo['height'].'" src="'.$url.$img.'"></center>';
	echo'<br/>';
	echo '</span>';
	}
function showForm()
	{
	echo	'<form method="POST">
			<input type="text" name="request" placeholder="request" value="'.$_POST['request'].'"/>
			<input type="text" name="color" placeholder="color" value="'.$_POST['color'].'" maxlength="7"/>
			<button>Search</button>
		</form>
	';
	print_r(convertColor());
	}

function fromHex($color)
	{
	
	$color=substr($color,1);
	if(strlen($color)<6)
		{
		$ret['r']=hexdec($color[0]);
		$ret['g']=hexdec($color[1]);
		$ret['b']=hexdec($color[2]);
		}
	else
		{
		$ret['r']=hexdec($color[0].$color[1]);
		$ret['g']=hexdec($color[2].$color[3]);
		$ret['b']=hexdec($color[4].$color[5]);
		}
	return $ret;
	}
function convertColor()
	{
	$color=$_GET['color'];
	if(strpos($color,'#')!==false)
		{
		$ret=fromHex($color);
		}
	return $ret;
	}
function calculateRelevancePercent($result,$color)
	{
	$c=substr($color,1);
	$r=substr($result,1);
	$j=0;
	// #1 in hex mode 100/6*count(color[$i]>0
	for($i=0;$i<=5;$i=$i+2)
		{
		echo $c[$i].$c[$i+1];
		if(($c[$i].$c[$i+1])>0 && ($r[$i].$r[$i+1])>0)
			{
			$j++;
			}
		if(($c[$i].$c[$i+1])==($r[$i].$r[$i+1])>0)
			{
			$j++;
			}
		}
		echo '<div>j='.$j.'</div>';
	return 100/6*$j;
	}
/*function calculateImageColo*/
?>
