<?php
require ./class-game.php;
require ./class-kicker.php;
require ./class-deck.php;
require ./class-offense-deck.php;
require ./class-defense-deck.php;
require ./class-special-deck.php;
$game = new Game();
$game->play();
?>
