<?php 
header('Content-Type: application/json');

// Lokasi file penyimpanan data
$data_file = 'data.json';

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
        // Mengirimkan data dari file JSON
        echo json_encode($persons);
        break;

    case 'POST':
        // Membaca input dari POST request
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validasi input
        if (!isset($input['nama_buku']) || !isset($input['harga']) || !isset($input['profil_pembuat']) || !isset($input['hobi'])) {
            echo json_encode(["message" => "Input tidak valid"]);
            exit;
        }
        

        // Menambahkan ID baru ke input, jika array kosong, set ID pertama ke 1
        $input['id'] = count($persons) > 0 ? end($persons)['id'] + 1 : 1;
        // Menambahkan data baru ke array persons
        $persons[] = [
            'id' => $input['id'],
            'nama_buku' => $input['nama_buku'],
            'harga' => $input['harga'],
            'profil_pembuat' => $input['profil_pembuat'],
            'hobi' => $input['hobi']
        ];

        // Menyimpan data kembali ke file JSON
        file_put_contents($data_file, json_encode($persons, JSON_PRETTY_PRINT));

        // Mengirimkan respon data yang ditambahkan
        echo json_encode($input);
        break;

    default:
        http_response_code(405);
        echo json_encode(["message" => "Metode HTTP tidak didukung"]);
        break;
}
