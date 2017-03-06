<?php
/**************
Daily Derby
order 3 out of 8 horses plus pick 3 numbers between 0-9 for the 'race time'
race time odds: 1/1000
trifecta odds: 1/1320
grand prize odds: 1/1320000

advance play: users can play 1-7 or 14 times with the same card
****************/

//require files
require_once('functions.php');
require_once('config.php');
//global variables
$winprize = mt_rand(1,5);
$exactaprize = mt_rand(15,50);
$trifectaprize = mt_rand(200,300);
$racetimeprize = mt_rand(50,100);
$winracetimeprize = $winprize + $racetimeprize;
$exactaracetimeprize = $exactaprize + $racetimeprize;
$grandprize = 50000;
$hit_win = false;
$hit_exacta = false;
$hit_trifecta = false;
$hit_racetime = false;
$winmsg = '';
$win_amount = 0;
//check for form submit
if(isset($_POST['submitgame'])){
	$userhorse1 = $_POST['horsetowin'];
	$userhorse2 = $_POST['horsetoplace'];
	$userhorse3 = $_POST['horsetoshow'];
	$userracetime = $_POST['rtime1'].$_POST['rtime2'].$_POST['rtime3'];
	$user_race_time_string = '1:4'.$_POST['rtime1'].'.'.$_POST['rtime2'].$_POST['rtime3'];
	$horse_arr = array(1,2,3,4,5,6,7,8);
	for($i = 1; $i <= 3; $i++){
		shuffle($horse_arr);
	}
	$race_order = array_slice($horse_arr,0,3);
	$race_order_string = implode(',',$race_order);
	$race_time = draw_numbers(3,0,9,false);
	$race_time_string = '1:4'.$race_time[0].'.'.$race_time[1].$race_time[2];
	$race_time = implode('',$race_time);
	
	//check win
	if($userracetime == $race_time){
		$hit_racetime = true;
	}
	if($userhorse1 == $race_order[0]){
		if($userhorse2 == $race_order[1]){
			if($userhorse3 == $race_order[2]){
				$hit_trifecta = true;
			} else {
				$hit_exacta = true;
			}
		} else {
			$hit_win = true;
		}
	}
	
	//calculate pay
	if($hit_racetime == true){
		if($hit_trifecta == true){
			$win_amount = $grandprize;
		} elseif($hit_exacta == true){
			$win_amount = $exactaracetimeprize;
		} elseif($hit_win == true){
			$win_amount = $winracetimeprize;
		} else {
			$win_amount = $racetimeprize;
		}
	} else {
		if($hit_trifecta == true){
			$win_amount = $trifectaprize;
		} elseif($hit_exacta == true){
			$win_amount = $exactaprize;
		} elseif($hit_win == true){
			$win_amount = $winprize;
		}
	}
	
	//set up win message
	if($win_amount > 0){
		$won = 'y';
		$wins = 1;
		$winmsg = 'Congratulations! You won $'.$win_amount.'.';
	} else {
		$won = 'n';
		$wins = 0;
		$winmsg = 'Sorry, not this time... Play again soon!';
	}
	
	//user info
	if(isset($_COOKIE['lotto_un'])){
		$user_id = strrev(base64_decode(str_rot13($_COOKIE['lotto_un'])));
	} else {
		$user_id = 0;
	}
	$ip = $_SERVER['REMOTE_ADDR'];
	$longip = ip2long($ip);
	
	//real escape
	$picks = $mysqli->real_escape_string($userhorse1.','.$userhorse2.','.$userhorse3);
	$results = $mysqli->real_escape_string($race_order_string);
	$race_time = $mysqli->real_escape_string($race_time);
	$userracetime = $mysqli->real_escape_string($userracetime);
	
	if($_POST['test'] == 0){
		//enter into db
		$sql = "insert ignore into dailyderby_plays values(NULL,'$user_id','$longip','$picks','$results','$userracetime','$race_time','$won','$win_amount')";
		//lotto_query($sql);
		if($user_id != 0){
			$sql = "insert ignore into dailyderby_userplays values('$user_id','1','$wins','$win_amount') on duplicate key update plays=plays+1, wins=wins+$wins, totalwon=totalwon+$win_amount";
			//lotto_query($sql);
		}
	}
}
?>

<!DOCTYPE html>
<head>
<title>Daily Derby - Lotto - KyleTripp.com</title>
<style type="text/css">
	*{font-family:Verdana, Geneva, sans-serif;text-align:center;}
	body{background-color:#0ef;}
	thead{height:0;margin:0;padding:0;}
	.hidden{display:none!important;}
	.clearfix{clear:both;}
	.site-wrapper{width:900px;min-height:95vh;margin:0 auto;background-color:#F5F5F5;padding:8px;display:block;box-shadow:0px 0px 5px #333;-webkit-box-shadow:0px 0px 5px #333;-moz-box-shadow:0px 0px 5px #333;}
	.back{float:left;padding:7px 14px;margin:5px;background-color:#FF1C27;color:#fff;font-size:24px;cursor:pointer;}
	.intronav{width:60%;padding:10px 0;margin:20px auto 0;background-color:#FF1C27;color:#fff;font-size:24px;cursor:pointer;}
	.leftside, .rightside{width:49%;display:inline-block;vertical-align:top;}
	.leftside table{border:1px solid black;display:block;}
	.leftside tbody{display:block;}
	.hr{border:1px solid red;padding:5% 2%;width:95%;margin:0 auto;display:block;}
	.horder{border:1px solid black;}
	.horder-item{border:1px solid red;}
	.horder-item p{border:1px solid blue;margin:6px 2%;width:75%;padding:5% 2%;display:inline-block;}
	.remove{border:1px solid green;margin:6px 1%;width:10%;padding:5% 2%;display:inline-block;vertical-align:top;}
	.rtime{border:1px solid black;margin-top:25px;}
	.rtime-item{border:1px solid red;}
	.rtime-selector{border:1px solid blue;padding:8px 0;}
	.rs{border:1px solid green;width:30%;padding:10px 0;display:inline-block;margin-bottom:5px;}
	.rulesdiv{width:80%;margin:0 auto;}
	.rulesdiv *{text-align:left!important;}
	.select, .rs, .info, .remove, .fsubmit{cursor:pointer;}
	.fsubmit{width: 100%;margin: 10px 0;padding: 10px 0;-webkit-appearance: none;border: none;background-color: #0f0;font-size: 24px;}
	.racearea{height:300px;position:relative;border:1px solid black;}
	.dirt{background-color:#C2A73E;}
	.turf{background-color:#4DBD33;}
	.finishline{position:absolute;top:0;right:10px;z-index:1;height:100%;width:24px;background:url('images/checkerboard.jpg') 0 0; background-repeat:repeat-y;}
	.horse{height:60px;width:100px;z-index:10;background:url('images/horse-sample.gif') 0px 0px; background-repeat:no-repeat;background-size:contain;position:absolute;}
	.yournumbers{display:inline-block;width:59%;}
	.windiv{display:block;position:relative;vertical-align:top;width:100%;margin:10px 0;}
	.winamount{font-family:arial;font-weight:600;font-size:60px;text-shadow:#666 2px 3px;}
	.endmessage{font-family:arial;font-weight:500;font-size:46px;text-shadow:#666 2px 3px;}
	.win{color:#181;}
	.lose{color:#811;}
	.playagain{text-decoration:none;font-size:30px;padding:15px 0;width:60%;background-color:blue;color:white;margin:15px auto;display:block;cursor:pointer;}
	<?php
		for($i = 1; $i <= 8; $i++){
			$t = (34*($i-1));
			echo '.horse'.$i.'{top:'.$t.'px;left:0;}';
		}
	?>
</style>
<script type="text/javascript" src="http://www.kyletripp.com/js/jquery1.11.min.js"></script>
<script type="text/javascript">
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-51898739-1', 'kyletripp.com');
  ga('send', 'pageview');

</script>
</head>
<body>
	<div class="site-wrapper">
	
	
	
	<?php if(!isset($_POST['submitgame'])){ ?>
	<form action="http://www.kyletripp.com/lotto/dailyderby.php" method="post" name="dailyderbyform">
	
	
<?php
	 /*******************
	  view 1
	  intro screen 
	  *******************/
?> 
		<div id="introscreen">
			<h1>Welcome to Kyle Tripp's Daily Derby Simulator</h1>
			<h3>Based on the California Lottery game of the same name: <a href="http://www.calottery.com/play/draw-games/daily-3" target="_blank">here</a></h3>
			<h3>Press the start button below to begin</h3>
			<div class="intronav begin">Start</div>
			<div class="intronav rules">Rules</div>
			<div class="intronav topwins">Top Wins</div>
			<div class="intronav mostplays">Most Plays</div>
			<div class="leftside">
				<div class="lastwin">
					<h2>Last Winner</h2>
					<?php
					$sql = "select user_id,amount from dailyderby_plays where won='y' order by id desc limit 1";
					$result = lotto_query($sql);
					if($result->num_rows == 0){
						echo '<p>No winners yet.</p>';
					} else{
						$row = $result->fetch_assoc();
						$this_id = $row['user_id'];
						$this_amount = $row['amount'];
						if($this_id == '0'){
							$this_un = 'Guest';
						} else {
							$sql = "select username from lotto_user where id='$this_id'";
							$result = lotto_query($sql);
							$row = $result->fetch_assoc();
							$this_un = $row['username'];
						}
						echo '<p>'.$this_un.' won $'.$this_amount.'</p>';
					}
					?>
				</div>
			</div>
			<div class="rightside">
				<div class="member">
				<?php if(isset($_COOKIE['lotto_un']) && isset($_COOKIE['lotto_name'])){
					echo '<p style="width:100%;text-align:center;">Welcome '.$_COOKIE['lotto_name'].'! <a href="http://www.kyletripp.com/lotto/logout.php">Logout</a></p>';
				} else {
					echo '<p style="width:100%;text-align:center;"><a href="http://www.kyletripp.com/lotto/register.php">Register</a> to track your stats</p><p style="width:100%;text-align:center;"><a href="http://www.kyletripp.com/lotto/login.php">Login</a> to continue playing.</p>';
				} ?>
				</div>
			</div>
		</div><?php//endsection?>
		
		
		
		
<?php
	/**************
	view 2
	pick horses
	uses a text scroller at the top (later)
	left side:
	lists the 8 horses that will race, click each horse for info
	right side:
	display picked horses, pick time appears when all three are picked
	display pick time, go appears when three numbers are picked
	****************/
?>	
		<div id="pickhorses" class="hidden">
			<div class="back">Back</div>
			<div class="clearfix"></div>
			<div class="leftside">
				<table>
					<tbody>
				<?php 
				$horse_arr = pick8horses();
				for($i = 1; $i <= 8; $i++){
					echo '<tr class="hr hr'.$i.'">
							<td width="10%" class="jersey"></td>
							<td width="10%" class="bibnum">'.$i.'</td>
							<td width="50%" class="horsename">'.$horse_arr[$i-1]['name'].'</td>
							<td width="15%" class="info">info</td>
							<td width="15%" class="select">select</td>
						</tr>';
				}
				?>
					</tbody>
				</table>
			</div>
			<div class="rightside">
				<div class="horder">
					<h2 class="horder-prompt">Pick a horse to Win</h2>
					<div class="horder-item horder-item1 hidden"><p></p><div class="remove"></div></div>
					<div class="horder-item horder-item2 hidden"><p></p><div class="remove"></div></div>
					<div class="horder-item horder-item3 hidden"><p></p><div class="remove"></div></div>
					<input type="hidden" name="horsetowin" id="horsetowin" value="">
					<input type="hidden" name="horsetoplace" id="horsetoplace" value="">
					<input type="hidden" name="horsetoshow" id="horsetoshow" value="">
				</div>
				<div class="rtime hidden">
					<h2 class="rtime-prompt">Pick a race time</h2>
					<div class="rtime-item">1:4_:__</div>
					<input type="hidden" name="rtime1" id="rtime1" value="">
					<input type="hidden" name="rtime2" id="rtime2" value="">
					<input type="hidden" name="rtime3" id="rtime3" value="">
					<div class="rtime-selector">
						<div class="rs rs1">1</div>
						<div class="rs rs2">2</div>
						<div class="rs rs3">3</div>
						<div class="rs rs4">4</div>
						<div class="rs rs5">5</div>
						<div class="rs rs6">6</div>
						<div class="rs rs7">7</div>
						<div class="rs rs8">8</div>
						<div class="rs rs9">9</div>
						<div class="rs rsx">&laquo;</div>
						<div class="rs rs0">0</div>
						<div class="rs"></div>
					</div>
				</div>
				<div class="submitdiv hidden">
					<input type="submit" class="fsubmit" name="submitgame" value="GO!">
					<input type="hidden" name="test" value="1">
				</div>
			</div>
		</div><?php//endsection?>
		
		
		
		
		
		
<?php
	/**************
	view 3
	view rules
	get copy from calotto website
	explain my rules
	****************/
?>			
		<div id="viewrules" class="hidden">
			<div class="back">Back</div>
			<div class="clearfix"></div>
			<h2>Daily Derby Rules</h2>
			<div class="rulesdiv">
				<p>Pick your three favorite horses: one to finish first, one to finish second and one to finish third.</p>
				<p>Next, pick your Race Time, which is the time it takes for the first place horse to finish.</p>
				<p>The Race Time will range from 1:4<u>0:00</u> to 1:4<u>9:99</u>. That's one minute and forty seconds to one minute and forty-nine point nine, nine seconds.</p>
				<p><b>All you have to do is select the last three numbers.</b></p>
				<p>If you choose 1:4<b>2:31</b> as your Race Time, you only need to select the last three digits <b>231</b> on the play page.</p>
				<p>Once you have selected all three horses and a race time, hit go and watch them race.  Good luck</p>
			</div>
		</div><?php//endsection?>
		
		
		
		
		
		
		
<?php
	/**************
	view 4
	top winners
	right now will show top 10 highest paid registered members
	****************/
?>			
		<div id="topwinners" class="hidden">
			<div class="back">Back</div>
			<div class="clearfix"></div>
			<table class="leaderboard">
				<thead>
					<tr>
						<td style="width:50%;"></td>
						<td style="width:50%;"></td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="2" class="h2">Top Winners</td>
					</tr>
					<?php
					$sql = "select up.totalwon,lu.username from dailyderby_userplays up, lotto_user lu where up.user_id=lu.id and lu.banned='n' order by totalwon desc limit 10";
					$result = lotto_query($sql);
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$this_amount = $row['totalwon'];
							$this_un = $row['username'];
							echo '<tr><td>'.$this_un.'</td><td>$'.$this_amount.'</td></tr>';
						}
					}?>
				</tbody>
			</table>
		</div><?php//endsection?>
		
		
				
<?php
	/**************
	view 5
	most plays
	right now will show the ten most playing registered members
	****************/
?>	
		<div id="mostplays" class="hidden">
			<div class="back">Back</div>
			<div class="clearfix"></div>
			<table class="leaderboard">
				<thead>
					<tr>
						<td style="width:50%;"></td>
						<td style="width:50%;"></td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="2" class="h2">Most Plays</td>
					</tr>
					<?php
					$sql = "select up.plays,lu.username from dailyderby_userplays up, lotto_user lu where up.user_id=lu.id and lu.banned='n' order by plays desc limit 10";
					$result = lotto_query($sql);
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$this_amount = $row['plays'];
							$this_un = $row['username'];
							echo '<tr><td>'.$this_un.'</td><td>'.$this_amount.'</td></tr>';
						}
					}?>
				</tbody>
			</table>
		</div>
	</form><?php//endsection?>
	
	
			
<?php
	/**************
	end of non form submit view
	****************/

} else {
	
	/**************
	begin form submit view
	****************/
?>	
	
	<div id="results">
		<div class="racearea turf">
			<div class="horse horse1"></div>
			<div class="horse horse2"></div>
			<div class="horse horse3"></div>
			<div class="horse horse4"></div>
			<div class="horse horse5"></div>
			<div class="horse horse6"></div>
			<div class="horse horse7"></div>
			<div class="horse horse8"></div>
			<div class="finishline"></div>
		</div>
		<div class="winvalues hidden">
			<table>
				<thead>
					<tr><td style="width:50%"></td><td style="width:50%"></td></tr>
				</thead>
				<tbody>
					<tr><td class="grandprize">Grand Prize</td><td class="grandprizevalue"><?php echo $grandprize;?></td></tr>
					<tr><td>Exacta + RT</td><td><?php echo $exactaracetimeprize;?></td></tr>
					<tr><td>Win + RT</td><td><?php echo $winracetimeprize;?></td></tr>
					<tr><td>Race Time Only</td><td><?php echo $racetimeprize;?></td></tr>
					<tr><td>Trifecta</td><td><?php echo $trifectaprize;?></td></tr>
					<tr><td>Exacta</td><td><?php echo $exactaprize;?></td></tr>
					<tr><td>Win</td><td><?php echo $winprize;?></td></tr>
				</tbody>
			</table>
		</div>
		<div class="displayarea">
			<div class="yournumbers">
				<table width="100%;">
					<tbody>
						<tr>
							<td><h3>Your Numbers</h3></td>
							<td><?php echo $userhorse1;?></td>
							<td><?php echo $userhorse2;?></td>
							<td><?php echo $userhorse3;?></td>
							<td><?php echo $user_race_time_string;?></td>
						</tr>
						<tr>
							<td><h3>Results</h3></td>
							<td class="hidden"><?php echo $race_order[0];?></td>
							<td class="hidden"><?php echo $race_order[1];?></td>
							<td class="hidden"><?php echo $race_order[2];?></td>
							<td class="hidden"><?php echo $race_time_string;?></td>
						</tr>
						<tr>
							<td></td>
							<td class="hidden"><?php echo($userhorse1 == $race_order[0])?'&#x2713;':'&#x2717;';?></td>
							<td class="hidden"><?php echo($userhorse2 == $race_order[1])?'&#x2713;':'&#x2717;';?></td>
							<td class="hidden"><?php echo($userhorse3 == $race_order[2])?'&#x2713;':'&#x2717;';?></td>
							<td class="hidden"><?php echo($user_race_time_string == $race_time_string)?'&#x2713;':'&#x2717;';?></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="clearfix"></div>
			<div class="windiv">
				<div class="winamount hidden <?php echo ($win_amount!=0)?'win':'lose';?>"><?php echo $win_amount;?></div>
				<div class="endmessage hidden <?php echo ($win_amount!=0)?'win':'lose';?>"><?php echo ($win_amount!=0)?'You Win!':'Not this time...';?></div>
			</div>
			<div class="clearfix"></div>
			<div><a href="http://www.kyletripp.com/lotto/dailyderby.php" class="playagain hidden">Play Again</a></div>
		</div>
		<div class="hidden">
			<?php var_dump($_POST);?>
			<?php echo '<br>';var_dump($horse_arr);?>
			<?php echo '<br>'.$race_order_string.'  '.$race_time_string;?>
		</div>
	</div>
	<?php } ?>
	
</div>




<?php
	/******************
	jquery functions
	******************/
?>
<script type="text/javascript">
$(document).ready(function(){
	
});
/*intro screen/general*/
$('.begin').click(function(){
	$('#introscreen').toggleClass('hidden');
	$('#pickhorses').toggleClass('hidden');
});
$('.rules').click(function(){
	$('#introscreen').toggleClass('hidden');
	$('#viewrules').toggleClass('hidden');
});
$('.topwins').click(function(){
	$('#introscreen').toggleClass('hidden');
	$('#topwinners').toggleClass('hidden');
});
$('.mostplays').click(function(){
	$('#introscreen').toggleClass('hidden');
	$('#mostplays').toggleClass('hidden');
});
$('.back').click(function(){
	$('#'+(this.parentNode.id)).toggleClass('hidden');
	$('#introscreen').toggleClass('hidden');
});
/*play section*/
$('.select').click(function(){
	//make sure horse isnt already picked
	if($(this).hasClass('picked')){
		return;
	}
	//get the name and number of the horse selected
	var hname = $(this).siblings('.horsename').html();
	var hnum = $(this).siblings('.bibnum').html();
	//check for open slot
	var o = 0;
	var p = '';
	for(var i = 1; i <= 3; i++){
		if($('.horder-item'+i+' p').html() == ''){
			o = i;
			if(i == 1){
				p = 'win';
			} else if(i == 2){
				p = 'place';
			} else {
				p = 'show';
			}
			break;
		}
	}
	//do update
	if(o != 0){
		$('.horder-item'+o).toggleClass('hidden');
		$('.horder-item'+o+' p').html(hname);
		$('#horseto'+p).val(hnum);
		$('.hr'+hnum+' .select').addClass('picked');
		if(o == 1){
			$('.horder-prompt').html('Pick a horse to Place');
		} else if(o == 2){
			$('.horder-prompt').html('Pick a horse to Show');
		} else {
			$('.horder-prompt').html('All horses selected');
			$('.rtime').removeClass('hidden');
		}	
	}
});
$('.rs').click(function(){
	if($(this).hasClass('rsx')){
		//undo last number if possible
		for(var i = 3; i > 0; i--){
			if($('#rtime'+i).val() != ''){
				$('#rtime'+i).val('');
				$('.submitdiv').addClass('hidden');
				break;
			}
		}
	} else {
		//add number if possible
		for(var i = 0; i <= 9; i++){
			if($(this).hasClass('rs'+i)){
				var n = i;
				break;
			}
		}
		var r = false;
		for(var i = 1; i <= 3; i++){
			if($('#rtime'+i).val() == ''){
				$('#rtime'+i).val(n);
				if(i == 3){
					$('.submitdiv').removeClass('hidden');
				}
				break;
			}
		}
	}
	//update display
	var a = '_';
	var b = '_';
	var c = '_';
	if($('#rtime1').val() != ''){
		a = $('#rtime1').val();
	}
	if($('#rtime2').val() != ''){
		b = $('#rtime2').val();
	}
	if($('#rtime3').val() != ''){
		c = $('#rtime3').val();
	}
	console.log('a:'+a+' b:'+b+' c:'+c);
	$('.rtime-item').html('1:4'+a+':'+b+c);
});
$('.remove').on('click',function(){
	var item = $(this).parent();
	if(item.hasClass('horder-item1')){
		$('.hr'+$('#horsetowin').val()+' .picked').removeClass('picked');
		$('#horsetowin').val($('#horsetoplace').val());
		$('.horder-item1 p').html($('.horder-item2 p').html());
		$('#horsetoplace').val($('#horsetoshow').val());
		$('.horder-item2 p').html($('.horder-item3 p').html());
		$('#horsetoshow').val('');
		$('.horder-item3 p').html('');
		if($('#horsetowin').val() != ''){
			if($('#horsetoplace').val() != ''){
				$('.horder-prompt').html('Pick a horse to Show');		
				$('.horder-item3').addClass('hidden');
			} else {
				$('.horder-prompt').html('Pick a horse to Place');		
				$('.horder-item2, .horder-item3').addClass('hidden');
			}
		} else {
			$('.horder-prompt').html('Pick a horse to Win');
			$('.horder-item1, .horder-item2, .horder-item3').addClass('hidden');
		}
	} else if(item.hasClass('horder-item2')){
		$('.hr'+$('#horsetoplace').val()+' .picked').removeClass('picked');
		$('#horsetoplace').val($('#horsetoshow').val());
		$('.horder-item2 p').html($('.horder-item3 p').html());
		$('#horsetoshow').val('');
		$('.horder-item3 p').html('');
		console.log($('#horsetoplace').val());
		if($('#horsetoplace').val() != ''){
			console.log("$('.horder-prompt').html('Pick a horse to Show')");
			$('.horder-prompt').html('Pick a horse to Show');	
			$('.horder-item3').addClass('hidden');
		} else {
			console.log("$('.horder-prompt').html('Pick a horse to Place');");
			$('.horder-prompt').html('Pick a horse to Place');
			$('.horder-item2, .horder-item3').addClass('hidden');
		}
	} else if(item.hasClass('horder-item3')){
		$('.hr'+$('#horsetoshow').val()+' .picked').removeClass('picked');
		$('#horsetoshow').val('');
		$('.horder-item3 p').html('');
		$('.horder-prompt').html('Pick a horse to Show');	
		$('.horder-item3').addClass('hidden');
	}
	$('.rtime').addClass('hidden');
});
/* race section */
<?php if(isset($_POST['submitgame'])){ ?>
	setTimeout(function(){do_horserace();},2000);
<?php } ?>
function do_horserace(){
	<?php 
	$ms = 7000;
	//$easing_arr = array('linear','swing');
	for($i = 0; $i < 8; $i++){ 
		//$rand = mt_rand(0,1);
		echo '$(".horse'.($horse_arr[$i]).'").animate({left: "800px"},'.$ms.',"linear",function(){});';
		$rand = mt_rand(400,900);
		$ms += $rand;
	}
	?>
}
</script>
</body>
</html>

<?php
	/***********
	php functions
	***********/
	
function pick8horses(){
	$horse_arr = array();
	$horsenames_arr = array('ed','benny','charles','champion','suzy','blue bomber','finish line','racecar','xbox','sweetheart','babygirl','las vegas');
	$num_horsenames = count($horsenames_arr);
	$names_arr = draw_numbers(8,0,$num_horsenames-1,true);
	$i = 0;
	foreach($names_arr as $key=>$value){
		$horse_arr[$i]['name'] = $horsenames_arr[$value];
		$i++;
	}
	return $horse_arr;
}
?>
