<?php
$pageTitle="sign_in";
require_once('includes/config.inc.php');
//错误信息
$err=false;
if($_SERVER["REQUEST_METHOD"] == "POST"){
	
	require(DB);

	// Trim all the incoming data:
	$trimmed = array_map('trim', $_POST);
	$p=$fn=false;
	//用户名称
	if (preg_match ('/^[A-Z \'.-]{2,20}$/i', $trimmed['name']) || preg_match('/[\x{4e00}-\x{9fa5}]+/u',$trimmed['name']) ) {
		$fn = mysqli_real_escape_string ($link, $trimmed['name']);
	}
	else {
		$err.= "<h3><p> 你输入的网页注册名有误！</p> <p> You forgot to enter your  name.</p></h3>";
	}
	//密码
	if (preg_match ('/^\w{4,20}$/', $trimmed['pass']) ) {
		$p = mysqli_real_escape_string ($link, $trimmed['pass']);
		
	} else {
		$err.= '<h3><p>Please enter a valid password!密码信息有非法字符！</p></h3>';
	}
	
	if($p && $fn){
		
		$q = "SELECT user_id, Activated FROM equipuser WHERE name='$fn' AND pass=SHA1('$p')";		
		$r = @mysqli_query ($link,$q) or trigger_error("Query:$q\n<br />MySQL Error:".mysqli_error($link));// Run the query.
		
		// Check the result:
		if (mysqli_num_rows($r) == 1) {

			// Fetch the record:
			$row = mysqli_fetch_assoc($r);
			$act=$row['Activated'];
			$user_id=$row['user_id'];
			
			if($act == 1){
				//设置cookie
				//setcookie ('user_id', $user_id,time()+3600);
		//setcookie ('name',$fn,time()+3600);
		//需要改路径，不然退出登录将无效
		setcookie ('user_id', $user_id, time()+86400, BASE_URL, '', 0, 0);
		setcookie ('name', $fn, time()+86400, BASE_URL, '', 0, 0);
		
		//$err.="<h3>登录成功！<br />将在1秒后自动跳转到主页！<br /></h3>";
		//跳转主页
			if (  (basename($_SERVER['PHP_SELF']) != 'index.php') ) 
			{
				$url=BASE_URL.'index.php';
				//ob_end_clean();
				header("Location:$url");
				//exit();	
			}	
			//结束判断账户激活状态	
				}else{
					$err.="<h3>你的账户尚未被激活！<br /></h3>";
					}
			
			//$time=time();
//			$formtime=date("Y-m-d H:i:s",$time);                
//			
//			//记录活动信息
//			$rt="UPDATE equipuser SET CreateTime='$formtime' WHERE name='$e'";
//		$rty= @mysqli_query($link,$rt)or trigger_error("Query:$q\n<br />MySQL Error:".mysqli_error($link));
			
			mysqli_free_result($r);
		//结束判断信息存在性	
		}else{
			mysqli_free_result($r);
			$err .='<h3>The name and password entered do not match those on file<br />用户名和密码信息有误，没有匹配上相关记录！</h3>';
			}
		
		//结束判断信息为空
		}else{
			$err.= '<h3><p>你的登录信息不符合规范，请再次尝试<br /></p></h3>';
		$err.= $fn .'<br />'. $p .'<br />';
			}
	mysqli_close($link);
	//结束判断表格处理
	}
include("z-header.php");
if($err){
	echo $err;
}
?>
        
        <div class="dialog">
    <div class="panel panel-default">
        <p class="panel-heading no-collapse">登录！Sign In</p>
        <div class="panel-body">
            <form action="sign-in.php" method="post">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="name" class="form-control span12" value="<?php if (isset($_POST['name'])) echo $_POST['name']; ?>">
                </div>
                <div class="form-group">
                <label>Password</label>
                    <input type="password" name="pass" class="form-controlspan12 form-control" value="<?php if (isset($_POST['pass'])) echo $_POST['pass']; ?>">
                </div>
                <div>
                
               <input type="submit" name="submit" class="btn btn-primary pull-right"  value="Login" />
               </div>
                
                
                <div class="clearfix"></div>
            </form>
        </div>
    </div>
    	<p><a href="register.php">新用户?...请注册——new user?</a></p> 
       <p><a href="forgot_password.php">忘记密码？——Forgot your password?</a></p>  
       
</body>

</html>