<?php
//通用
class General{
	//图片裁剪
	public function ImageCrop($old_name=false,$new_name,$input,$output,$x=false,$y=false,$w=false,$h=false){
		if($old_name){
			$targ_w = $targ_h = 150;//头像长宽
			$jpeg_quality = 100;//头像质量
			
			//获取图片
			$src = $input.$old_name;
			$filename_arr=pathinfo($old_name);
			switch($filename_arr['extension']){
				case 'jpg':
				$img_r = imagecreatefromjpeg($src);
				break;
				
				case 'jpeg':
				$img_r = imagecreatefromjpeg($src);
				break;
				
				case 'png':
				$img_r = imagecreatefrompng($src);
				break;
				
				case 'gif':
				$img_r = imagecreatefromgif($src);
				break;
				
				default:
				echo '错误的文件格式';
				exit;
				break;
			}
			$dst_r = imagecreatetruecolor( $targ_w, $targ_h );//建立头像
			imagecopyresampled($dst_r,$img_r,0,0,$x,$y,$targ_w,$targ_h,$w,$h);//头像裁剪
			imagejpeg($dst_r,$output.$new_name.'.jpg',$jpeg_quality);//输出头像
		}
	}
	
	//生成验证码
	public function Validate(){
		srand((double)microtime()*1000000);
		$im=imagecreate(45,18);
		$black=imagecolorallocate($im,0,0,0);
		$white=imagecolorallocate($im,255,255,255);
		$gray=imagecolorallocate($im,200,200,200);
		imagefill($im,0,0,$gray);
		
		//session_register("autonum");
		$_SESSION["autonum"]="";
		for($i=0;$i<4;$i++){
			$str=mt_rand(1,3);
			$size=mt_rand(3,6);
			//$authnum=mt_rand(0,9);
			//$authnum=$this->str_rand($i);
			if($i%2==0){
				$authnum=mt_rand(1,9);
			}else{
				$word='abcdefghijklmnpqrstuvwxyz';
				if(mt_rand(0,1)==0){
					$authnum=str_split($word)[mt_rand(0,24)];
				}else{
					$authnum=str_split(strtoupper($word))[mt_rand(0,24)];
				}
			}
			$_SESSION["autonum"].=$authnum;
			imagestring($im,$size,(5+$i*10),$str,$authnum,imagecolorallocate($im,rand(0,130),rand(0,130),rand(0,130)));
		} 
		for($i=0;$i<200;$i++){
			$randcolor=imagecolorallocate($im,rand(0,255),rand(0,255),rand(0,255));
			imagesetpixel($im,rand()%70,rand()%30,$randcolor); 
		}
		imagepng($im);
		imagedestroy($im);
	}
	/*public function str_rand($i){
		//if(mt_rand(0,1)==0){
		if($i%2==0){
			return mt_rand(0,9);
		}else{
			$word='abcdefghijklmnopqrstuvwxyz';
			if(mt_rand(0,1)==0){
				return str_split($word)[mt_rand(0,25)];
			}else{
				return str_split(strtoupper($word))[mt_rand(0,25)];
			}
		}
	}*/
	
	//SMTP发送邮件
	public function Mail_smtp($to, $subject = 'No subject', $body){ 
		//PS_MAIL_SERVER	smtp.qq.com
		//PS_MAIL_USER		lipeixin2319@qq.com
		//PS_MAIL_PASSWD	qlglpx860119
		//PS_MAIL_DOMAIN	mail.qq.com
		$loc_host = "longcredit"; //发信计算机名，可随意 
		$smtp_acc = "lipeixin2319@qq.com"; //Smtp认证的用户名，类似fish1240@fishcat.com.cn，或者fish1240 
		$smtp_pass = "qlglpx860119"; //Smtp认证的密码，一般等同pop3密码 
		$smtp_host = "172.16.248.96"; //SMTP服务器地址，类似 smtp.tom.com 
		$from ="lipeixin@safe.longcredit.com"; //发信人Email地址，你的发信信箱地址 
		$headers = "Content-Type: text/html; charset=\"utf-8\"\r\nContent-Transfer-Encoding: base64"; 
		$lb="\r\n"; //linebreak
		
		$hdr = explode($lb,$headers); //解析后的hdr 
		$body= $body; 
		if($body) 
		{ 
		$bdy = preg_replace("/^\./","..",explode($lb,$body));//解析后的Body 
		}
		
		
		$smtp = array( 
		//1、EHLO，期待返回220或者250 
		array("EHLO ".$loc_host.$lb,"220,250","HELO error: "), 
		//2、发送Auth Login，期待返回334 
		array("AUTH LOGIN".$lb,"334","AUTH error:"), 
		//3、发送经过Base64编码的用户名，期待返回334 
		array(base64_encode($smtp_acc).$lb,"334","AUTHENTIFICATION error : "), 
		//4、发送经过Base64编码的密码，期待返回235 
		array(base64_encode($smtp_pass).$lb,"235","AUTHENTIFICATION error : ") 
		);
		
		//5、发送Mail From，期待返回250 
		$smtp[] = array("MAIL FROM: <".$from.">".$lb,"250","MAIL FROM error: "); 
		//6、发送Rcpt To。期待返回250 
		$smtp[] = array("RCPT TO: <".$to.">".$lb,"250","RCPT TO error: "); 
		//7、发送DATA，期待返回354 
		$smtp[] = array("DATA".$lb,"354","DATA error: "); 
		//8.0、发送From 
		$smtp[] = array("From: ".$from.$lb,"",""); 
		//8.2、发送To 
		$smtp[] = array("To: ".$to.$lb,"",""); 
		//8.1、发送标题 
		$smtp[] = array("Subject: ".$subject.$lb,"",""); 
		//8.3、发送其他Header内容 
		foreach($hdr as $h) 
		{ 
		$smtp[] = array($h.$lb,"",""); 
		} 
		//8.4、发送一个空行，结束Header发送 
		$smtp[] = array($lb,"",""); 
		//8.5、发送信件主体 
		if($bdy) 
		{ 
		foreach($bdy as $b) 
		{ 
		$smtp[] = array(base64_encode($b.$lb).$lb,"",""); 
		} 
		} 
		//9、发送“.”表示信件结束，期待返回250 
		$smtp[] = array(".".$lb,"250","DATA(end)error: "); 
		//10、发送Quit，退出，期待返回221 
		$smtp[] = array("QUIT".$lb,"221","QUIT error: ");
		
		//打开smtp服务器端口 
		$fp = @fsockopen($smtp_host, 25); 
		if (!$fp) 
		echo "<b>Error:</b> Cannot conect to ".$smtp_host."<br>"; 
		while($result = @fgets($fp, 1024)) 
		{ 
		if(substr($result,3,1) == " ") 
		{
		
		break; 
		} 
		}
		
		$result_str=""; 
		//发送smtp数组中的命令/数据 
		foreach($smtp as $req) 
		{ 
		//发送信息 
		@fputs($fp, $req[0]); 
		//如果需要接收服务器返回信息，则 
		if($req[1]) 
		{ 
		//接收信息 
		while($result = @fgets($fp, 1024)) 
		{ 
		if(substr($result,3,1) == " ") 
		{ 
		break; 
		} 
		}; 
		if (!strstr($req[1],substr($result,0,3))) 
		{ 
		$result_str.=$req[2].$result."<br>"; 
		} 
		} 
		} 
		//关闭连接 
		@fclose($fp); 
		return $result_str; 
	}
	
}


?>