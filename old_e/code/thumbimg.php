<?
//ini_set('session.gc_maxlifetime', 7200);
//ini_set('session.cookie_lifetime', 7200);
ini_set("max_execution_time", "120");
session_start();
function fastimagecopyresampled (&$dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h, $quality = 3) {
  // Plug-and-Play fastimagecopyresampled function replaces much slower imagecopyresampled.
  // Just include this function and change all "imagecopyresampled" references to "fastimagecopyresampled".
  // Typically from 30 to 60 times faster when reducing high resolution images down to thumbnail size using the default quality setting.
  // Author: Tim Eckel - Date: 09/07/07 - Version: 1.1 - Project: FreeRingers.net - Freely distributable - These comments must remain.
  //
  // Optional "quality" parameter (defaults is 3). Fractional values are allowed, for example 1.5. Must be greater than zero.
  // Between 0 and 1 = Fast, but mosaic results, closer to 0 increases the mosaic effect.
  // 1 = Up to 350 times faster. Poor results, looks very similar to imagecopyresized.
  // 2 = Up to 95 times faster.  Images appear a little sharp, some prefer this over a quality of 3.
  // 3 = Up to 60 times faster.  Will give high quality smooth results very close to imagecopyresampled, just faster.
  // 4 = Up to 25 times faster.  Almost identical to imagecopyresampled for most images.
  // 5 = No speedup. Just uses imagecopyresampled, no advantage over imagecopyresampled.

  if (empty($src_image) || empty($dst_image) || $quality <= 0) { return false; }
  if ($quality < 5 && (($dst_w * $quality) < $src_w || ($dst_h * $quality) < $src_h)) {
    $temp = imagecreatetruecolor ($dst_w * $quality + 1, $dst_h * $quality + 1);
    imagecopyresized ($temp, $src_image, 0, 0, $src_x, $src_y, $dst_w * $quality + 1, $dst_h * $quality + 1, $src_w, $src_h);
    imagecopyresampled ($dst_image, $temp, $dst_x, $dst_y, 0, 0, $dst_w, $dst_h, $dst_w * $quality, $dst_h * $quality);
    imagedestroy ($temp);
  } else imagecopyresampled ($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
  return true;
}


function imageRoundedCopyResampled(&$dstimg, &$srcimg, $dstx, $dsty, $srcx,
                                   $srcy, $dstw, $dsth, $srcw, $srch, $radius) {
    # Resize the Source Image
    $srcResized = imagecreatetruecolor($dstw, $dsth);
    fastimagecopyresampled($srcResized, $srcimg, 0, 0, $srcx, $srcy,
                       $dstw, $dsth, $srcw, $srch);
    # Copy the Body without corners
    imagecopy($dstimg, $srcResized, $dstx+$radius, $dsty,
              $radius, 0, $dstw-($radius*2), $dsth);
    imagecopy($dstimg, $srcResized, $dstx, $dsty+$radius,
              0, $radius, $dstw, $dsth-($radius*2));
    # Create a list of iterations; array(array(X1, X2, CenterX, CenterY), ...)
    # Iterations in order are: Top-Left, Top-Right, Bottom-Left, Bottom-Right
    $iterations = array(
        array(0, 0, $radius, $radius),
        array($dstw-$radius, 0, $dstw-$radius, $radius),
        array(0, $dsth-$radius, $radius, $dsth-$radius),
        array($dstw-$radius, $dsth-$radius, $dstw-$radius, $dsth-$radius)
    );
    # Loop through each corner 'iteration'
    foreach($iterations as $iteration) {
        list($x1,$y1,$cx,$cy) = $iteration;
        for ($y=$y1; $y<=$y1+$radius; $y++) {
            for ($x=$x1; $x<=$x1+$radius; $x++) {
                # If length (X,Y)->(CX,CY) is less then radius draw the point
                $length = sqrt(pow(($cx - $x), 2) + pow(($cy - $y), 2));
                if ($length < $radius) {
                    imagecopy($dstimg, $srcResized, $x+$dstx, $y+$dsty,
                              $x, $y, 1, 1);
                }
            }
        }
    }
}
?>
<?php
// The file
$filename = $_GET['img'];
$filename2=explode(".jpg",$filename); $filename3=explode(".png",$filename); $filename4=explode(".gif",$filename); $filename5=explode(".JPG",$filename);
if(count($filename2)>1||count($filename3)>1||count($filename4)>1||count($filename5)>1) {
$w = @$_GET['w'];
$h = @$_GET['h'];
$r = @$_GET['r'];
$t = @$_GET['t'];
// Content type
if(count($filename2)>1||count($filename5)>1) { header('Content-type: image/jpeg'); } else {
if(count($filename4)>1) { header('Content-type: image/gif'); }}
// Get new dimensions
list($width, $height) = getimagesize($filename);

//$new_width = $width * 0.5;
//$new_height = $height * 0.5;
$new_width=$width;
$new_height=$height;
$flag_not=0;
if(@$w!=""&&@$h!="") { $new_width=$w; $new_height=$h; } 
if(@$w!=""&&@$h=="") { if($width<$w) { $w=$width; $flag_not=1; } $new_width=$w; $new_height=$height * $w / $width; } 
if(@$h!=""&&@$w=="") { if($height<$h) { $h=$height; $flag_not=1; } $new_height=$h; $new_width=$width * $h / $height; } 

// Resample

$image_p = imagecreatetruecolor($new_width, $new_height);

if(count($filename2)>1||count($filename5)>1) { $image = imagecreatefromjpeg($filename);} else {
if(count($filename4)>1) { $image = imagecreatefromgif($filename); 

$colorTransparent = imagecolortransparent($image);
imagepalettecopy($image, $image_p);
imagefill($image_p, 0, 0, $colorTransparent);
imagecolortransparent($image_p, $colorTransparent);
imagetruecolortopalette($image_p, true, 256);

}}

if($flag_not=='1') { $image_p=$image; } else { 
fastimagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height); 
}

// Output

if(count($filename2)>1||count($filename5)>1) { imagejpeg($image_p, null, 100); } else {
if(count($filename4)>1) { imagegif($image_p, null,100); }}

}
?>
