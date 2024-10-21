<?php
// Mengambil data dari API JSON
$jsonData = file_get_contents('http://localhost/Interoperability/minggu7/person.php');
$books = json_decode($jsonData, true);

// Mengonversi ke format XML
$xml = new SimpleXMLElement('<books/>');
foreach ($books as $book) {
    $item = $xml->addChild('book');
    $item->addChild('id', $book['id']);
    $item->addChild('title', $book['title']);
    $item->addChild('author', $book['author']);
}

// Menyimpan atau menampilkan XML
Header('Content-Type: application/xml');
echo $xml->asXML();
?>
