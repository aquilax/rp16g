<?php
// aquilax presents: rp16g
session_start();
$p=&$_POST;
$s=&$_SESSION;
$tl = array(
1=>array(1=>'&nbsp;',2=>1,3=>''),
2=>array(1=>'&nbsp;',2=>0,3=>''),
3=>array(1=>'&nbsp;',2=>0,3=>''),
4=>array(1=>'&nbsp;',2=>1,3=>''),
5=>array(1=>'&nbsp;',2=>1,3=>''),
11=>array(1=>'<font color="brown">.</font>',2=>1,3=>'land'),
12=>array(1=>'<font color="green">*</font>',2=>0,3=>'tree'),
13=>array(1=>'<font color="blue">~</font>',2=>0,3=>'water'),
14=>array(1=>'<font color="red">&</font>',2=>1,3=>'monster'),
15=>array(1=>'<font color="red">+</font>',2=>1,3=>'dragon'),
);
function seed($t, $n, &$r){
  for($i=0;$i<$n;$i++){
    $r[rand(0,60)][rand(0,60)] = $t;
  }
}

function genM(){
  global $tls;
  $r = array();
  for($y=0;$y<61;$y++)
    $r[$y] = array_fill(0,60,1);
  seed(2, rand(55,150), $r);
  seed(3, rand(40,150), $r);
  seed(4, rand(150,250), $r);
  seed(5, 1, $r);
  $r[2][2] = 1;
  return $r;
}
function init(){
  global $s;
  $s['st'] = 1;
  $s['x'] = 2;
  $s['y'] = 2;
  $s['h'] = 10;
  $s['a'] = 2;
  $s['d'] = 2;
  $s['i'] = array();
  $s['m'] = genM();
}
function r($t){
printf('<html><head><title>rp16g</title><style>table td{vertical-align:top}</style></head><body><h1><a>rp16g</a></h1><p style="color:silver;margin:-2em 0 0 5em">tiny rpg</p>%s</body>', $t);
}
function st(){
 r('<form method="post" action=""><input type="submit" name="start" value="Start Game"/></form>');
}
function play(){
global $s;
global $tl;
global $p;
for($y=$s['y']-1;$y<$s['y']+2;$y++){
  for($x=$s['x']-1;$x<$s['x']+2;$x++){
    if($s['m'][$y][$x]<10){
      $s['m'][$y][$x] +=10;
    }
  }
}
$t='<pre><table>';
$t.='<tr>';
$t.='<td style="height:18em;width:60em;line-height:0.6em">';
if (isset($p['run'])){
  $t.='<p>You ran away from the battle. Shame on you</p>';
  $s['st']=1;
}
$t.='<div id="map">';
if($s['st']==1){
  foreach($s['m'] as $y=>$r){
    foreach($r as $x=>$otl){
      if ($y==$s['y'] && $x==$s['x']){
        $t.='<font color="blue">@</font>';
      } else {
        $t.=$tl[$otl][1];
      }
    }
    $t.="\n";
  }
$t.='</div>';
} else {
  if(isset($p['att'])){
    $at = rnd($s['a']);
    if ($at== $at+2) $at *=2;
    $hit = $at-$s['mn']['d'];
    $hit = ($hit<1)?0:$hit;
    $t.=sprintf('<p>You attack and take %d HP</p>', $hit);
    $s['mn']['h'] -= $hit;
    if($s['mn']['h'] <1){
      if($s['st']==2){
        r('<p>You attack and kill the monster. You can <a href="">continue</a> your quest.</p>');
        $s['m'][$s['y']][$s['x']]=11;
        $s['h'] += floor($s['mn']['mh']/2);
        $s['a'] +=1;
        $s['d'] +=1;
        unset($s['mn']);
        $s['st']=1;
        die();
      } else {
        r('<center><p>You killed the mighty dragon. Now you can get the girl, jump into your red ferrary and ride to the beautyful sunset.</p><h2>The end!</h2></center>');
        session_destroy();
        die();
      }
    }
    $at = rnd($s['mn']['a']);
    if ($at== $at+2) $at *=2;
    $hit = $at-$s['d'];
    $hit = ($hit<1)?0:$hit;
    $t.=sprintf('<p>Monster attacks and takes %d HP</p>', $hit);
    $s['h'] -= $hit;
    if($s['h']<1){
      r('<h1>You are dead!</h1><p>Better luck next time. <a href="">Start again</a>.<p>');
      session_destroy();
      die();
    }
  }
  $mn = $s['m'][$s['y']][$s['x']];
  $t.='<center>';
  $t.='<h4>Fight</h4>';
  $t.='<table><tr><th></th><th>@</th><th>'.$tl[$mn][1].'</th></tr>';
  $t.='<tr><td>hlt:</td><td>'.$s['h'].'</td><td>'.$s['mn']['h'].'</td></tr>';
  $t.='<tr><td>att:</td><td>'.$s['a'].'</td><td>'.$s['mn']['a'].'</td></tr>';
  $t.='<tr><td>def:</td><td>'.$s['d'].'</td><td>'.$s['mn']['d'].'</td></tr>';
  $t.='<br/><br/><form method="post" action=""><input type="submit" name="att" value="attack"><input type="submit" name="run" value="run"></form>';
  $t.='</table>';
  $t.='</center>';
}
$t.='</td>';

$t.='<td>';
$t.='<h3>Move</h3>';
$t.='<form method="post" action="">&nbsp;&nbsp;<input type="submit" name="n" value="N"><br/><input type="submit" name="w" value="W"><input type="submit" name="e" value="E"><br/>&nbsp;&nbsp;<input type="submit" name="s" value="S"></form>';
$t.='<h3>Hero stats</h3>';
$t.='Health: '.$s['h'];
$t.='<br/>Attack: '.$s['a'];
$t.='<br/>Defense: '.$s['d'];
$t.='<br/>Position: '.$s['x'].'x'.$s['y'];
$t.='<h3>Legend</h3>';
foreach($tl as $k=>$a){
if($k>10) $t.= $a[1].' '.$a[3].'<br/>';
}
$t.='</td>';
$t.='</tr>';
$t.='</table></pre>';
r($t);
}
function vp($n){
  global $p;
  if (isset($p[$n])){
    return $p[$n];
  } else {
    return FALSE;
  }
}
function rnd($v){return mt_rand($v-2, $v+2);}
function inp(){
  global $s;
  global $tl;
  $x = $s['x'];
  $y = $s['y'];
  if ($s['st']==1){
    if(vp('n'))$y-=1;
    if(vp('w'))$x-=1;
    if(vp('e'))$x+=1;
    if(vp('s'))$y+=1;
  }
  $ct = $s['m'][$y][$x];
  if($tl[$ct][2]){
    $s['x']=$x;
    $s['y']=$y;
    if ($s['st']==1 && $ct==14) {
      $s['st'] = 2;
      $h = rnd($s['h']);
      $s['mn'] = array('mh'=>$h, 'h'=>$h, 'a'=>rnd($s['a']), 'd'=>rnd($s['d']));
    }
    if ($s['st']==1 && $ct==15) {
      $s['st'] = 3;
      $s['mn'] = array('mh'=>100, 'h'=>100, 'a'=>20, 'd'=>30);
    }
  }
}
if(isset($s['st'])){
  inp();
  play();
} elseif($p['start']){
  init();
  play();
} else{
  st();
}
