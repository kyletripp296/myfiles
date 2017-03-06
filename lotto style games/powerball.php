<?php 
require_once 'config.php';

define('GAME_ID','pb');

if($_POST['submit']){
	$picks_arr = array();
	//check that the user picked 5 numbers 1-69
	for($i=1;$i<=5;$i++){
		$ball = $_POST['pick'.$i];
		if(empty($ball) || !ctype_digit($ball) || $ball<1 || $ball>69){
			$error = true;
		} else {
			$picks_arr[] = $ball;
		}
	}
	//check that the user picked a powerball 1-26
	$pball = trim($_POST['powerball']);
	if(empty($pball) || !ctype_digit($pball) || $pball<1 || $pball>26){
		$error = true;
	} else {
		$picks_arr[] = $pball;
	}
	//if no errors submit pick
	if(!$error){
		$draw_id = get_draw_id(GAME_ID);
		$picks = implode(',',$picks_arr);
		$gameid_sql = $mysqli->real_escape_string(GAME_ID);
		$drawid_sql = $mysqli->real_escape_string($draw_id);
		$userid_sql = $mysqli->real_escape_string($user_id);
		$picks_sql = $mysqli->real_escape_string($picks);
		$sql = "insert into member_entries values (NULL,'$gameid_sql','$drawid_sql','$userid_sql,'pending','$picks_sql',NULL)";
		exit($sql);
		lotto_query($sql);
	}
}

require_once 'lotto-header.php';
?>

<form id="hiddenform">
	<input type="hidden" name="pick1">
	<input type="hidden" name="pick2">
	<input type="hidden" name="pick3">
	<input type="hidden" name="pick4">
	<input type="hidden" name="pick5">
	<input type="hidden" name="powerball">
</form>

<div class="content-wrap">
	<div class="content">
		<div class="step1">
			<p>Pick 5 of these numbers</p>
			<div class="pick-wrap">
				<?php 
				for($i=1;$i<=69;$i++){
					echo '<span class="ballwhite" data-value="'.$i.'">'.$i.'</span>';
				}
				?>
			</div>
		</div>
		<div class="step2">
			<p>Now pick 1 powerball</p>
			<div class="pick-wrap">
				<?php 
				for($i=1;$i<=26;$i++){
					echo '<span class="ballred" data-value="'.$i.'">'.$i.'</span>';
				}
				?>
			</div>
		</div>
		<div class="step3">
			<div class="ticket-wrap">
				<div class="ticket">
					<p>Game: <span>Powerball</span></p>
					<p>Draw Number: <span></span></p>
					<p>Picks:<br>
						<span class="ball1 ballwhite"></span>
						<span class="ball2 ballwhite"></span>
						<span class="ball3 ballwhite"></span>
						<span class="ball4 ballwhite"></span>
						<span class="ball5 ballwhite"></span>
						<span class="ball6 ballred"></span>
					</p>
				</div>
			</div>
			<div class="confirm-wrap">
				<div class="confirm">Yes! Submit these picks</div>
				<div class="repick">No, repick my numbers</div>
			</div>
		</div>
	</div>
</div>
</body>
</html>