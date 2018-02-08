<?php

$inputFile = "1080p_with_timecode.mov";
$maxDuration = trim(`ffmpeg -i $inputFile 2>&1 | grep "Duration" | cut -d ' ' -f 4 | sed s/,//`);

$sTotal = 0;

$h = (int) explode(":", $maxDuration)[0];
$m = (int) explode(":", $maxDuration)[1];
$s = (int) explode(".", explode(":", $maxDuration)[2])[0];

$sTotal += $h * 60 * 60;
$sTotal += $m * 60;
$sTotal += $s;

$maxDuration = $sTotal;

echo "Getting a random snip from >>$maxDuration<<\n";

$endMax = intval(($maxDuration - 2) / 2);
echo "Duration $endMax\n";

for($i = 0; $i < 100; $i++) {
	$start = rand(0, $endMax);
	echo "Getting $start\n";
	$outputFile = "out" . $start . "-" . ($start + 2) . ".mp4";

	if (!is_file($outputFile))
		`ffmpeg -ss $start -t 2 -i $inputFile $outputFile`;
}
