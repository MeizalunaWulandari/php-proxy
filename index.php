<?php
// Menambahkan header CORS sebelum output lainnya
header('Access-Control-Allow-Origin: *'); // Mengizinkan semua origin. Ubah sesuai kebutuhan.
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Requested-With');

// Menangani permintaan OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit; // Jika hanya perlu menangani permintaan OPTIONS, hentikan eksekusi
}

// Proxy PHP untuk bypass CORS
$target_url = 'https://etslive-v3-vidio-com-tokenized.akamaized.net';

// Ambil URL dari request
$request_uri = $_SERVER['REQUEST_URI'];

// Hapus '/proxy' dan '/index.php' dari URI jika ada
$request_uri = preg_replace('#^/proxy#', '', $request_uri);
$request_uri = preg_replace('#^/index\.php#', '', $request_uri);


$full_url = $target_url . $request_uri;


// Inisialisasi curl
$ch = curl_init($full_url);

// Header yang ingin dikirim ke target
$curl_headers = [
    'authority: etslive-v3-vidio-com-tokenized.akamaized.net',
    'accept: */*',
    'accept-language: en-US,en;q=0.9',
    'cache-control: no-cache',
    'origin: https://www.vidio.com',
    'pragma: no-cache',
    'referer: https://www.vidio.com/',
    'sec-ch-ua: " Not A;Brand";v="99", "Chromium";v="102", "Google Chrome";v="102"',
    'sec-ch-ua-mobile: ?0',
    'sec-ch-ua-platform: "Linux"',
    'sec-fetch-dest: empty',
    'sec-fetch-mode: cors',
    'sec-fetch-site: cross-site',
    'user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/102.0.0.0 Safari/537.36'
];

// Set options curl
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_headers);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

// Eksekusi request
$response = curl_exec($ch);

// Pisahkan headers dan body
$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$header = substr($response, 0, $header_size);
$body = substr($response, $header_size);

// Tutup curl
curl_close($ch);

// Kirimkan headers ke client, menghapus header yang tidak diinginkan
$response_headers = explode("\r\n", $header);
foreach ($response_headers as $header_line) {
    if (stripos($header_line, 'Access-Control-Allow-Origin') === false && 
        stripos($header_line, 'Access-Control-Allow-Methods') === false &&
        stripos($header_line, 'Access-Control-Allow-Headers') === false) {
        header($header_line);
    }
}

// Kirimkan body ke client
echo $body;
?>
