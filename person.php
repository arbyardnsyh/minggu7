<?php 
// Mengatur header konten untuk format XML
header('Content-Type: application/xml');

// Lokasi file penyimpanan data untuk person
$data_file = 'data_person.json';

// Jika file tidak ada, buat file baru dengan array kosong
if (!file_exists($data_file)) {
    file_put_contents($data_file, json_encode([]));
}

// Membaca data dari file JSON
$persons = json_decode(file_get_contents($data_file), true);

// Jika decoding JSON gagal atau menghasilkan null, inisialisasi sebagai array kosong
if (!is_array($persons)) {
    $persons = [];
}

// Mendapatkan metode HTTP yang digunakan (GET, POST)
$method = $_SERVER['REQUEST_METHOD'];

// Mengatur respon berdasarkan metode HTTP
switch ($method) {
    case 'GET':
        // Mengirimkan data dalam format XML
        $xml = new SimpleXMLElement('<persons/>');

        foreach ($persons as $person) {
            $person_node = $xml->addChild('person');
            $person_node->addChild('id', $person['id']);
            $person_node->addChild('name', $person['nama']);
            $person_node->addChild('age', $person['umur']);
            
            $address = $person_node->addChild('address');
            $address->addChild('street', $person['alamat']['jalan']);
            $address->addChild('city', $person['alamat']['kota']);
            
            $hobbies = $person_node->addChild('hobbies');
            foreach ($person['hobi'] as $hobi) {
                $hobbies->addChild('hobby', $hobi);
            }
        }

        echo $xml->asXML();
        break;

    case 'POST':
        // Membaca input dari POST request dalam format XML
        $input = simplexml_load_string(file_get_contents('php://input'));
        
        // Validasi input
        if (!isset($input->name) || !isset($input->age) || !isset($input->address) || !isset($input->hobbies)) {
            header('Content-Type: application/json');
            echo json_encode(["message" => "Input tidak valid"]);
            exit;
        }

        // Menambahkan ID baru ke input, jika array kosong, set ID pertama ke 1
        $new_person = [
            'id' => count($persons) > 0 ? end($persons)['id'] + 1 : 1,
            'nama' => (string) $input->name,
            'umur' => (int) $input->age,
            'alamat' => [
                'jalan' => (string) $input->address->street,
                'kota' => (string) $input->address->city
            ],
            'hobi' => []
        ];

        foreach ($input->hobbies->hobby as $hobi) {
            $new_person['hobi'][] = (string) $hobi;
        }

        // Menambahkan data baru ke array persons
        $persons[] = $new_person;

        // Menyimpan data kembali ke file JSON
        file_put_contents($data_file, json_encode($persons, JSON_PRETTY_PRINT));

        // Mengirimkan respon data yang ditambahkan dalam format XML
        $response_xml = new SimpleXMLElement('<person/>');
        $response_xml->addChild('id', $new_person['id']);
        $response_xml->addChild('name', $new_person['nama']);
        $response_xml->addChild('age', $new_person['umur']);

        $address = $response_xml->addChild('address');
        $address->addChild('street', $new_person['alamat']['jalan']);
        $address->addChild('city', $new_person['alamat']['kota']);

        $hobbies = $response_xml->addChild('hobbies');
        foreach ($new_person['hobi'] as $hobi) {
            $hobbies->addChild('hobby', $hobi);
        }

        echo $response_xml->asXML();
        break;

    default:
        header('Content-Type: application/json');
        http_response_code(405);
        echo json_encode(["message" => "Metode HTTP tidak didukung"]);
        break;
}
