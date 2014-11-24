<?php
require("../define_names.php");
/* Генератор изображений со случайным числом
   Copyright (C) 2005 Владимир Сергеев aka Bred Vilchec.
   Modified by :::NiL::: aka z3x
   E-mail: vilchec@mail.ru
   ICQ   : 273717605                                   */

$img_x          = 65;  //Ширина изображения
$img_y          = 30;   //Высота изображения
$num_n          = 4;    //Число цифр
$font_min_size  = 15;   //Минимальный размер шрифта
$font_max_size  = 15;   //Максимальный размер шрифта
$lines_n_max    = 0;    //Максимальное число шумовых линий
$nois_percent   = 1;    //Зашумленность цветами фона и текста, в процентах
$angle_max      = 19;   //Максимальный угол отклонения от горизонтали по часовой стрелке и против

$font_arr=glob(dirname(__FILE__)."/*.ttf");

$im=imagecreate($img_x, $img_y);
//создаем необходимые цвета
//$text_color = imagecolorallocate($im, 0, 0, 0);           //цвет текста
//$nois_color = imagecolorallocate($im, 0, 0, 0);           //цвет зашумляющих точек
$line_color = imagecolorallocate($im, 255, 255, 255);       //цвет зашумляющих линий
$img_color  = imagecolorallocate($im, 255, 255, 255);       //цвет фона

imagefill($im, 0, 0, $img_color);                   //заливаем изображение фоновым цветом
$number='';                                         //В переменной $number будет храниться число, показанное на изображении

for ($n=0; $n<$num_n; $n++){
   srand((double)microtime() * 1000000);
   $nm=substr(md5(rand(0,999999999)),0,$num_n);
   $num = substr($nm,$n,1);
    $number.=$num;
    $font_size=rand($font_min_size, $font_max_size);
    $angle=rand(360-$angle_max,360+$angle_max);

    // Закоментируйте эту строку и раскоментируйте строку № 21 для того чтобы сделать цвет текста постоянным
	$text_color = imagecolorallocate($im, rand(0,200), rand(0,200), rand(0,200));
    
	$font_cur=rand(0,count($font_arr)-1);
    $font_cur=$font_arr[$font_cur];
    
	//вычисление координат для каждой цифры, формулы обеспечивают нормальное расположние
    //при любых значениях размеров цифры и изображения
    //$y=rand(($img_y-$font_size)/4+$font_size, ($img_y-$font_size)/2+$font_size);
    //$x=rand(($img_x/$num_n-$font_size)/2, $img_x/$num_n-$font_size)+$n*$img_x/$num_n;
    
	$y=rand(19,27);
	if (empty($x)) {$x=7;}
    else {$x+=9+rand(5,7);}
    imagettftext($im, $font_size, $angle, $x, $y, $text_color, $font_cur, $num);
};

$nois_n_pix=round($img_x*$img_y*$nois_percent/100);   //Вычисляем число "зашумленных" пикселов
for ($n=0; $n<$nois_n_pix; $n++){                     //зашумляем изображение пикселами цвета текста
    // Закоментируйте эту строку и раскоментируйте строку № 22 для того чтобы сделать цвет зашумляющих точек постоянным
    $nois_color = imagecolorallocate($im, rand(0,200), rand(0,200), rand(0,200));

	$x=rand(0, $img_x);
    $y=rand(0, $img_y);
    imagesetpixel($im, $x, $y, $nois_color);
};

for ($n=0; $n<$nois_n_pix; $n++){                     //зашумляем изображение пикселами фонового цвета
    $x=rand(0, $img_x);
    $y=rand(0, $img_y);
    imagesetpixel($im, $x, $y, $img_color);
};

$lines_n=rand(1,$lines_n_max);
for ($n=0; $n<$lines_n; $n++){                        //проводим "зашумляющие" линии цвета текста
    $x1=rand(3, $img_x);
    $y1=rand(0, $img_y);
    $x2=rand(3, $img_x);
    $y2=rand(0, $img_y);
    imageline($im, $x1, $y1, $x2, $y2, $line_color);
};


Header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
Header("Last-Modified: ".gmdate("D, d M Y H:i:s")."GMT");
Header("Cache-Control: no-cache, must-revalidate");
Header("Pragma: no-cache");

header("Content-type: image/gif");
imagegif($im);
imagedestroy($im);

if(isset($_GET['n'])) { $_SESSION['number'.$_GET['n'].'']=$number; } else {
$_SESSION['number1']=$number; }
$number11=$number;
//В переменной $number хранится число, показанное на изображении

?>