<?php //Author: Tom64b | Website: github.com/Tom64b | Code based on article by Dr. Neal Krawetz | License: MIT | Date: 2017-05-12
// crop = 0 => default, crop = 1 => skip the outer pixels, effectively cropping the thumb (remove border noise!)
function dhash($fname, $crop = 0, $onlyJPG = true)
{
	// load exif thumbnail if possible; otherwise- load the image
	$thumb = exif_thumbnail($fname, $wid, $hei);
	if (!$thumb) {
		$src = ($onlyJPG) ? imagecreatefromjpeg($fname) : imagecreatefromstring(file_get_contents($fname));
		list($wid, $hei) = array(imagesx($src), imagesy($src));
	} else {
		$src = imagecreatefromstring($thumb);
		unset($thumb);
	}
	// resize to 9x8 = 72 pixels, apply grayscale
	$img = imagecreatetruecolor(9+$crop*2, 8+$crop*2);
	imagecopyresampled($img, $src, 0, 0, 0, 0, 9+$crop*2, 8+$crop*2, $wid, $hei);
	imagedestroy($src);    
	imagefilter($img, IMG_FILTER_GRAYSCALE);
	//calculate dHash (hackerfactor.com/blog/?/archives/529-Kind-of-Like-That.html)
	$hash = 0;
	$bit = 1;
	for ($y=0+$crop; $y<8; $y++) {
		if ($y == 4) { //second half of the image, this whole IF can be removed on 64bit machines
			$res = sprintf("%08x", $hash);
			$hash = 0;
			$bit = 1;
		}
		$previous = imagecolorat($img, 0, $y) & 0xFF;
		for ($x=1+$crop; $x<9; $x++) {
			$current = imagecolorat($img, $x, $y) & 0xFF;
			if ($previous > $current) {
				$hash |= $bit;
			}
			$bit = $bit << 1;
			$previous = $current;
		}
	}
	imagedestroy($img);
	return sprintf("%08x", $hash) . $res; //if you removed the IF above- remove " . $res"
}
function dhash_distance($hash1, $hash2)
{
	$counts = array(0,1,1,2,1,2,2,3,1,2,2,3,2,3,3,4);
	$res = 0;
	for ($i=0; $i<16; $i++) {
		if ($hash1[$i] != $hash2[$i]) {
			$res += $counts[hexdec($hash1[$i]) ^ hexdec($hash2[$i])];
		}
	}
	return $res;
}