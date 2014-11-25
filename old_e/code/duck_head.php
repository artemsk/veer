<?php

$bsk_filename=MAINURL_5."/template/".TEMPLATE."/head.php";

if(@$info['4bloks']['BREAK_OUTPUT_FLAG']=="1") { $bsk_filename=@$info['4bloks']['BREAK_OUTPUT_FILE_HEAD']; } // BREAK_OUTPUT!

if($bsk_filename!="") { $templ_head=get_include_contents($bsk_filename); } // ###������_����

  if(isset($head_filename)) { $templ_head_container=get_include_contents($head_filename); // ###������_����
  if(@$templ_head_container!="") { $templ_head = strtr($templ_head, array("{HEAD_CONTAINER}"=>@$templ_head_container)); }
  } // ������ � ����������� �� ����������


  if(@$title_head=="") { $title_head=""; } 
