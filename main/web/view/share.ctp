
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>口袋派对－发现更多可能...</title>
<style type="text/css">
<!--
body {margin: 0px;}
#Change {
width:100%;
height:1600px;
position:absolute;
z-index:-2012;
}
.ipbg {	background-image: url(http://f.gangker.com/image/ipbg.png);
	background-repeat: no-repeat;
}
.tgtitle {
	font-size: 16px;
	color: #FFF;
	font-weight: bold;
}
.plname {
	color: #FFF;
	font-size: 14px;
	text-indent: 5px;
}
img {
	border-top-style: none;
	border-right-style: none;
	border-bottom-style: none;
	border-left-style: none;
}
.zftxt {
	color: #666;
	font-size: 14px;
}
.zfname {
	font-size: 14px;
	font-weight: bold;
	color: #900;
}
.listname {
	font-size: 12px;
	color: #666;
}
-->
.ipbg {
	position:relative;
	z-index:10;
}
.voice_center, .voice_right{ 
	margin-left:-8px;
}
.voice, .voice_td, #bg_table {
	position:relative;
}
.voice_time {
	position:absolute;
	display:inline;
	top:15px;
	z-index:99;
}
.voice_paly {
	position:absolute;
	z-index:100;
}
#background {
	position:absolute;
	height:64px;
	z-index:-2;
	width:450;
	top:-392px;
}
#background_header{
	position:absolute;
	top:0px;
}
#tgtitle_tr {
	position:relative;
}
#tgtitle_bg {
	height:64px;
	width:100%;
	left:0;
	background-color: gray;
	opacity: 0.4;filter: alpha(opacity=40);-moz-opacity: 0.4;
	position:absolute;
	z-index:-1;
}
.voice_left_play {
	display:none;
}
.no_margin, .no_margin tr, .no_margin td{
	border:0px;
	margin:0px;
	padding:0px;;
}
.talk_avatar {
	border:2px solid gray;
}
.voice_num {
	width : 14px;
	margin:-1px;
}
</style>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
</head>

<body>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>

<!--这里开始做成动态的DIV头-->

<div style='background:url("<?php echo $url;?>/retina_wood2.jpg")  0px 0px;' id=Change></div>
<!--这里放背景上面的内容-->
<table width="1000" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="568" valign="top"><table width="568" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td height="800" align="center" valign="top" class="ipbg"><table width="450" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td height="35">&nbsp;</td>
          </tr>
          </table>
		  <table width="460" border="0" cellspacing="0" cellpadding="0" id="" >
			<tr id="background_header">
				<td>
					<img src="<?php echo $url;?>/header.jpg" width="100%" />
				</td>
			</tr>
		  </table>
          <table width="450" border="0" cellspacing="0" cellpadding="0" id="bg_table" >
			<tr id="background">
				<td>
					<img src="<?php echo $bg_image;?>" width="100%" />
				</td>
			</tr>
            <tr id="tgtitle_tr">
              <td height="64" align="center" class="tgtitle"><?php echo $title;?></td>
			  <td id="tgtitle_bg">&nbsp;</td>
            </tr>
            <tr>
              <td height="116">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td width="100%" align="center">
					<table width="90%" border="0" cellspacing="0" cellpadding="0">
						<tr>
						  <td align="left" width="85px">
							  <table width="83" cellspacing="0" cellpadding="0">
								<tr>
								  <td height="83" align="center" valign="middle" bgcolor="#FFFFFF"><img src="<?php echo $avatar;?>" width="75" height="75" /></td>
								  </tr>
							  </table>
						  </td>
						  <td align="left">
							<table width="85%" cellspacing="0" cellpadding="0">
							<tr>
								<td height="20" class="plname" style="padding-left:20px" ><?php echo $nickname;?></td>
							  </tr>
							  <tr>
								<td class="voice_td" >
									<?php $bg_width = 116+$voice_time;$bg_width_num = 64+$voice_time;?>
									<img class="voice_left" tag="1" src="<?php echo $url;?>/left_dial_n_left.png" width="62" height="53" />
									<img class="voice_left_play" tag="1" src="<?php echo $url;?>/left_dial_p_left.png" width="62" height="53" />
									<img class="voice_center" tag="1" src="<?php echo $url;?>/left_dial_n_center.png" width="<?php echo $voice_time;?>" height="53" />
									<img class="voice_right" tag="1" src="<?php echo $url;?>/left_dial_n_right.png" width="53" height="53" />
									<div class="voice_time" style="left:<?php echo $bg_width_num;?>px;">
										<?php
											$voice_time_str = $voice_time.'';
											$voice_time_len = strlen($voice_time_str);
											$voice_time_img = '';
											for ( $i=0; $i<$voice_time_len; $i++) {
												$voice_time_img .= '<img class="voice_num" tag="1" src="'.$url.'/blue_num/'.$voice_time_str{$i}.'.png" />';
											}
											$voice_time_img .= '<img class="voice_num" tag="1" src="'.$url.'/blue_num/\'.png" />';
											echo $voice_time_img;
										?>
									</div>
									<a href="javascript:void(0)" class="voice_paly" style="left:0px;">
										<img id="imgask" name="talk_<?php echo $talkid;?>" class="voice" tag="1" src="<?php echo $url;?>/play.png" width="<?php echo $bg_width;?>" height="53" playtype="html5" voiceurl = "<?php echo $voice['mp3']?>"/>
										<input type="hidden" class="caf" value="<?php echo $voice['caf'];?>"/>
									</a>
								</td>
							 </tr>
							</table>
						</td>
						</tr>
					</table>
				  </td>
                </tr>
              </table></td>
            </tr>
            <tr>
              <td height="98" background="<?php echo $url;?>/gkmain_bg2.jpg"><table width="100%" cellspacing="0" cellpadding="0">
                <tr>
                  <td width="19%" align="center"><img src="<?php echo $avatar2;?>" width="60" height="60" /></td>
                  <td width="5%">&nbsp;</td>
                  <td width="56%"><table width="90%" cellspacing="0" cellpadding="0">
                    <tr>
                        <td height="25" align="left" class="zfname"><?php echo $nickname2;?></td>
                      </tr>
                      <tr>
                        <td height="25" align="left" class="zftxt">我在关注此话题，大家快来围观吧。 </td>
                      </tr>
                  </table></td>
                  <td width="20%" align="left"><img src="<?php echo $url;?>/zf_play.jpg" width="50" height="50" /></td>
                </tr>
              </table></td>
            </tr>
			</table>
			<?php foreach ( $comment as $_comment ) {?>
				<table width="450" cellspacing="0" cellpadding="0" class="no_margin">
					<tbody>
						<tr>
							<td height="100" background="<?php echo $url;?>/gkmain_listbg.jpg" align="center">
								<table width="95%" cellspacing="0" cellpadding="0">
									<tbody>
										<tr>
											<td>
												<table width="85%" cellspacing="0" cellpadding="0">
													<tbody>
														<tr>
															<td height="20" class="listname"><span>第<?php echo $_comment->voice_fid;?>位</span><span style="float:right;margin-right:20px" ><?php echo $_comment->nickname;?></span></td>
														</tr>
														<tr>
															<td class="voice_td" align="right" >
																<?php $bg_width = 116+$_comment->voice_time;$bg_width_num = 64+$_comment->voice_time;?>
																<img class="voice_left" tag="1" src="<?php echo $url;?>/right_dial_n_left.png" width="62" height="53" />
																<img class="voice_left_play" tag="1" src="<?php echo $url;?>/right_dial_p_left.png" width="62" height="53" />
																<img class="voice_center" tag="1" src="<?php echo $url;?>/right_dial_n_center.png" width="<?php echo $_comment->voice_time;?>" height="53" />
																<img class="voice_right" tag="1" src="<?php echo $url;?>/right_dial_n_right.png" width="53" height="53" />
																<div class="voice_time" style="right:22px;">
																	<?php
																		$voice_time_str = $_comment->voice_time.'';
																		$voice_time_len = strlen($voice_time_str);
																		$voice_time_img = '';
																		for ( $i=0; $i<$voice_time_len; $i++) {
																			$voice_time_img .= '<img class="voice_num" tag="1" src="'.$url.'/gray_num/'.$voice_time_str{$i}.'.png" />';
																		}
																		$voice_time_img .= '<img class="voice_num" tag="1" src="'.$url.'/gray_num/\'.png" />';
																		echo $voice_time_img;
																	?>
																</div>
																<a href="javascript:void(0)" class="voice_paly" style="right:0px;">
																	<img id="imgask" name="comment_<?php echo $_comment->id;?>" class="voice" tag="1" src="<?php echo $url;?>/play.png" width="<?php echo $bg_width;?>" height="53" playtype="html5" voiceurl = "<?php echo $_comment->voice['mp3'];?>"/>
																	<input type="hidden" class="caf" value="<?php echo $_comment->voice['caf'];?>"/>
																</a>
															</td>
														</tr>
													</tbody>
												</table>
											</td>
											<td width="70" valign="top">
												<img width="60" height="60" class="talk_avatar" src="<?php echo $_comment->avatar;?>">
											</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
					</tbody>
				</table>
			<?php }?>
			<table width="450" cellspacing="0" cellpadding="0">
				<tr>
				  <td height="50" align="center" bgcolor="#FFFFFF" class="listname">想了解更多.请下载口袋派对</td>
				</tr>
			</table>
		</td>
      </tr>
    </table></td>
    <td valign="top"><table width="90%" cellspacing="0" cellpadding="0">
      <tr>
        <td width="96%" height="40" align="left">&nbsp;</td>
      </tr>
      <tr>
        <td align="left"><img src="<?php echo $url;?>/tglogo.png" width="164" height="164" /></td>
      </tr>
      <tr>
        <td height="160" align="left"><img src="<?php echo $url;?>/tgslogn.png" width="272" height="120" /></td>
      </tr>
      <tr>
        <td height="220" align="left"><img src="<?php echo $url;?>/tgtext.png" width="416" height="178" /></td>
      </tr>
      <tr>
        <td align="left"><a href="https://itunes.apple.com/cn/app/tai-gang/id553114436?mt=8">
		        <img src="<?php echo $url;?>/tgdownload_n.png" width="281" height="91" />
		        </a></td>
      </tr>
    </table></td>
  </tr>
</table>
<br />

<bgsound src="" loop="1" id="bgsound" />
<audio id="audio" preload="none" width="292">
    <source src="" type="audio/mpeg" />
</audio>
</body>
</html>
<script language="javascript">
var browser={ 
    versions:function(){   
           var u = navigator.userAgent;
                app = navigator.appVersion;
 return {                
                    trident: u.indexOf('Trident') > -1, //IE内核        
                    presto: u.indexOf('Presto') > -1, //opera内核           
                    webKit: u.indexOf('AppleWebKit') > -1, //苹果、谷歌内核    
                    gecko: u.indexOf('Gecko') > -1 && u.indexOf('KHTML') == -1, //火狐内核                    mobile:!!u.match(/AppleWebKit.*Mobile.*/)||!!u.match(/AppleWebKit/),                     //是否为移动终端 
                    ios: !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/), 
                       //ios终端      
                    android: u.indexOf('Android') > -1 || u.indexOf('Linux') > -1,                                 //android终端或者uc浏览器      
                    iPhone: u.indexOf('iPhone') > -1 || u.indexOf('Mac') > -1,
                 //是否为iPhone或者QQHD浏览器            
                    iPad: u.indexOf('iPad') > -1, //是否iPad              
                    webApp: u.indexOf('Safari') == -1 //是否web应该程序，没有头部与底部            
     };
  }()
 }

$(document).ready(function(){
	if ( true == browser.versions.iPhone) {
		$('.caf').each(function(){
			$(this).prev().attr('voiceurl', ($(this).val()));
		})
	}
})

var Beep = undefined;
var playing = 0;
var currentVoiceId = undefined;
var tag = undefined;
var currentTag = undefined;
var play_id = '';
function callback_html5() {
	//if (tag == 1)
	//	img = "http://f.gangker.com/image/left_dial_n.png";
	//else
	//	img = "http://f.gangker.com/image/right_dial_n.png";	
	if (currentVoiceId !== undefined) {
		var id = '#'+ currentVoiceId;
		//$(id).attr("src", img);
	}
	playing = 0;//play end
}
$(".voice").click(function(){
	var playtype = $(this).attr("playtype");
	var voiceUrl = $(this).attr("voiceurl");
	newVoiceId = $(this).attr("id");
	tag = $(this).attr("tag");
	var img_dom = $(this).parent().prev().prev().prev().prev();
    if(playing && $(this).attr('name') == play_id){
		playing = !playing;
        if (newVoiceId != currentVoiceId) { //playing not finished  and click another one
        	if (playtype == 'html5') {
        		//if (currentTag == 1)
        		//	img = "http://f.gangker.com/image/left_dial_n.png";
        		//else
        		//	img = "http://f.gangker.com/image/right_dial_n.png";
        		//var id = '#'+ currentVoiceId;	
        		//$(id).attr("src", img);
        		//if (tag == 1)
        		//	img = "http://f.gangker.com/image/left_dial_p.png";
        		//else
        		//	img = "http://f.gangker.com/image/right_dial_p.png";
        		//var id = '#'+ newVoiceId;	
        		//$(id).attr("src", img);
        		Beep.currentTime = 0;
        		Beep.pause();
                currentVoiceId =  $(this).attr("id");
            	audio.addEventListener( "ended", callback_html5, false);
                Beep = document.getElementById("audio");
                //Beep.stop();
                Beep.setAttribute("src", voiceUrl);
                Beep.load();
                Beep.play();
        	} else {
        		//if (currentTag == 1) {
        		//	img = "http://f.gangker.com/image/left_dial_n.png";
        		//} else {
        		//	img = "http://f.gangker.com/image/right_dial_n.png";
				//}
        		//var id = '#'+ currentVoiceId;	
        		//$(id).attr("src", img);
        		//if (tag == 1) {
        			//img = "http://f.gangker.com/image/left_dial_p.png";
        		//} else {
        			//img = "http://f.gangker.com/image/right_dial_p.png";
				//}
        		//var id = '#'+ newVoiceId;	
        		//$(id).attr("src", img);
        		document.getElementById("bgsound").src='';	
        		currentVoiceId =  $(this).attr("id");
        		document.getElementById("bgsound").src=voiceUrl;
        	}
            currentTag =  $(this).attr("tag");
        	return;
        }
		img_dom.hide();
		img_dom.prev().show();
    	if (playtype == 'html5') {
    		Beep.currentTime = 0;
    		Beep.pause();
    	}
    	else {
        	//var xx=document.getElementById("bgsound").src;
        	document.getElementById("bgsound").src='';	
    	}
    	if (tag == 1)
    		img = "http://f.gangker.com/image/left_dial_n.png";
    	else
    		img = "http://f.gangker.com/image/right_dial_n.png";
    	//$(this).attr("src", img);
    } else {
		play_id = $(this).attr('name');
		playing = !playing;
		$('.voice_left').show();
		$('.voice_left_play').hide();
		img_dom.prev().hide();
		img_dom.show();
        currentVoiceId =  $(this).attr("id");
        currentTag =  $(this).attr("tag");
        if (playtype == 'html5') {
        	audio.addEventListener( "ended", callback_html5, false);
            Beep = document.getElementById("audio");
            //Beep.stop();
            Beep.setAttribute("src", voiceUrl);
            Beep.load();
            Beep.play();
        }
        else {
        	//var xx=document.getElementById("bgsound").src;
        	document.getElementById("bgsound").attachEvent("onreadystatechange", callback_html5);
        	document.getElementById("bgsound").src=voiceUrl;	
        }
        if (tag == 1)
    		img = "http://f.gangker.com/image/left_dial_p.png";
    	else 
    		img = "http://f.gangker.com/image/right_dial_p.png";
    	//$(this).attr("src", img);
    }
 });
</script>