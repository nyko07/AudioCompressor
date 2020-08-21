<?php

require './vendor/autoload.php';
header('Content-type: application/json');
$song = $_FILES['file']['name'];
$extensions = array("mp3","wav"); 
$extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
$songName = $_FILES['file']['name'];
if (($extension == "mp3") || $extension == "wav" && in_array($extension, $extensions)) {
    if ($_FILES["file"]["error"] > 0) {
        echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
    } else {
        if (file_exists("upload/" . $songName)) {
            $originalSong = $songName;
            echo json_encode(array('error' => $originalSong, 'success' => false));
            return;
        } else {
            move_uploaded_file(
                $_FILES["file"]["tmp_name"],
                "upload/" . $songName
            );
            $originalSong = "Cancion original guardada en: " . "upload/" . $songName;
            $compressedSong = 'Cancion comprimida guardada en: compressedSongs/'.$songName;
        }
    }
} else {
    echo "Invalid file";
}


$ffmpeg = FFMpeg\FFMpeg::create(array(
    'ffmpeg.binaries'  => 'C:\ffmpeg\bin\ffmpeg.exe',
    'ffprobe.binaries' => 'C:\ffmpeg\bin\ffprobe.exe' 
));


$audio = $ffmpeg->open('upload/'.$song);
$format = new FFMpeg\Format\Audio\Mp3();

$format->on('progress', function ($audio, $format, $percentage) {
});

$format
    ->setAudioChannels(2)
    ->setAudioKiloBitrate(64);
   
$audio->save($format, 'C:\xampp\htdocs\compressor-project/compressedSongs/'.$song);

echo json_encode(array('originalSong' => $originalSong, 'compressedSong' => $compressedSong,'success' => true));
