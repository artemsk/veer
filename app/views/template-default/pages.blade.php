<? foreach($p as $k => $v) {
    
    echo $v->comments()->count()." ".$v->title." <a href=".$mainurl."/public/page/".$v->id."#disqus_thread>Link</a><br>";
    
    
} 

echo @$disqus_foot;
// TODO: продумать, как убрать из шаблона сбор данных
// $v->comments()->count() vs #disqus_thread
?>