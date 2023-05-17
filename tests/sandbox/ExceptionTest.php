<?php

function checkNum($number) {
	if ($number > 1) {
		throw new Exception('Value must be 1 or below');
	}
	return true;
}

//trigger exception in a "try" block
try {
	checkNum(2);
	//If the exception is thrown, this text will not be shown
	echo 'If you see this, the number is 1 or below';
} catch (Exception $e) {
	//catch exception
	echo 'Message: ' . $e->getMessage();
}

