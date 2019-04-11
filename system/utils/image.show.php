<?php
class IMG
{
	public function show($row,$type = 1)
	{
	    return 'https://congly.vn/data/news/'.date('Y/n/j/',(int)$row['time_created']).$row['img'.$type];
	}
	function thumb($row,$dir="")
	{
            if($dir=='cnn_150x100')
                    return 'https://congly.vn/resize.150x100/data/news/'.date('Y/n/j' , $row['time_created']).'/'.$row['img1'];
            if($dir=='cnn_275x185')
                    return 'http://congly.vn/resize.276x184/data/news/'.date('Y/n/j' , $row['time_created']).'/'.$row['img1'];
            if($dir=='cnn_185x140')
                    return 'https://congly.vn/resize.185x140/data/news/'.date('Y/n/j' , $row['time_created']).'/'.$row['img1'];
            if($dir=='cnn_210x140')
                    return 'https://congly.vn/resize.210x140/data/news/'.date('Y/n/j' , $row['time_created']).'/'.$row['img1'];
            if($dir=='cnn_320x215')
                    return 'https://congly.vn/resize.320x214/data/news/'.date('Y/n/j' , $row['time_created']).'/'.$row['img1'];
            if($dir=='cnd_220x315')
                    return 'https://congly.vn/data/news/'.date('Y/n/j/',$row['time_created']).$row['img3'];
            if($dir=='cnn_135x90')
                    return 'https://congly.vn/resize.150x100/data/news/'.date('Y/n/j' , $row['time_created']).'/'.$row['img1'];
            if($dir=='cnn_225x150')
                    return 'https://congly.vn/resize.210x140/data/news/'.date('Y/n/j' , $row['time_created']).'/'.$row['img1'];
            return 'https://congly.vn/data/news/'.date('Y/n/j' , $row['time_created']).'/'.$row['img1'];
		
	}
}