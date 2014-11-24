<?php

            $comm_filename=MAINURL_5."/template/".TEMPLATE."/comments.php";
            $templ_comm=get_include_contents($comm_filename); // ###шаблон_ввод
$str="";
if(count(@$info['4bloks']['comments'])>0&&@@$info['4bloks']['comments']!="") { foreach($info['4bloks']['comments'] as $c=>$c2) { if($c2['txt']=="") { continue; }
                    $templ_comm2 = strtr(@$templ_comm, array(
                        "{COMMENTS_AUTHOR}"   => $c2['avtor'],
                        "{COMMENTS_DAT}" => date("d.m.Y H:i",$c2['dat']),
                        "{COMMENTS_TXT}" => textprocess($c2['txt']),
                        "{IMG_PATH}" => MAINURL."/template/".TEMPLATE."/images",
                ));
// TODO: ссылки на стр клиентов потом как-нибудь customers_id, registered
                    $str.=$templ_comm2;
}
}
$flag_allow=1;
if(isset($info['4bloks']['comm_allow'])) { if($info['4bloks']['comm_allow']=="1") {} else { $flag_allow=0; } }
if($flag_allow=="1") {
$out['{SHOW_COMMENTS}']="<a name=\"comms\"></a><hr class=\"divider\" style='margin-top:30px;'><div class='comments_blok'>".COMMENTS_NAME."</div>".$str; }



?>