<?php

$disallowed = array(".DS_Store","..",".",".gitkeep");
$sourceDir = getcwd() . DIRECTORY_SEPARATOR . "source" . DIRECTORY_SEPARATOR;
$outputDir = getcwd() . DIRECTORY_SEPARATOR . "output" . DIRECTORY_SEPARATOR;

if (!is_writable($outputDir))
	throw new \Exception("Can't write to $outputDir");

$token = date("YMDhis");
mkdir($outputDir . $token);

$outputDir = $outputDir . $token . DIRECTORY_SEPARATOR;

$dir = scandir($sourceDir);
$sources = array();

foreach($dir as &$file) {
	if(stristr($file,'.php') || in_array($file,$disallowed) || is_dir($file)) {
		continue;
	}
	$sources[] = $file;
}

$inputFile = $sourceDir . $sources[0];
$inputFileEscaped = escapeshellarg($inputFile);

if (!is_file($inputFile))
	throw new Exception("I don't know WTF to do with {$inputFile}");

$cmd = "ffmpeg -i $inputFileEscaped 2>&1 | grep \"Duration\" | cut -d ' ' -f 4 | sed s/,//";

echo "I want to run:\n{$cmd}\n";

$maxDuration = trim(`$cmd`);

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
	$outputFile = $outputDir . "out" . $start . "-" . ($start + 2) . ".mp4";

	if (!is_file($outputFile))
		`ffmpeg -ss $start -t 2 -i $inputFileEscaped $outputFile`;
}
