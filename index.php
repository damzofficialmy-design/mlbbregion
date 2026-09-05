<?php
$hasil = null;
$error = null;

if(isset($_POST['cek'])){
    $uid = $_POST['uid'];
    $zone = $_POST['zone'];

    if(empty($uid) || empty($zone)){
        $error = "Isi User ID dan Zone ID dulu!";
    } else {
        // Senarai API backup
        $apis = [
            "https://api.dazelpro.com/mobile-legends/player/$uid/$zone",
            "https://api.jios.store/ml/info/$uid/$zone",
            "https://api.tokovip.co.id/v1/ml/cek/$uid/$zone"
        ];

        foreach($apis as $url){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);

            $data = json_decode($response, true);
            
            // Check format dazelpro
            if(isset($data['status']) && $data['status'] == true && isset($data['data'])){
                $hasil = [
                    'nick' => $data['data']['name'],
                    'region' => $data['data']['region'] ?? 'Tidak diketahui',
                    'id' => "$uid($zone)"
                ];
                break;
            }
            // Check format jios
            if(isset($data['data']) && isset($data['data']['username'])){
                $hasil = [
                    'nick' => $data['data']['username'],
                    'region' => $data['data']['region'] ?? 'Tidak diketahui',
                    'id' => "$uid($zone)"
                ];
                break;
            }
        }

        if($hasil == null){
            $error = "ID tidak dijumpai atau semua API sedang down. Cuba lagi 5 minit.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cek Region MLBB PHP</title>
<style>
  body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #0f172a, #1e293b);
    color: #fff;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    margin: 0;
  }
  .box {
    background: #1e293b;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 0 20px rgba(59, 130, 246, 0.4);
    width: 90%;
    max-width: 420px;
    text-align: center;
  }
  h1 { color: #3b82f6; margin-bottom: 10px; }
  p { font-size: 14px; color: #94a3b8; margin-bottom: 20px; }
  input {
    width: 100%;
    padding: 12px;
    margin: 8px 0;
    border-radius: 8px;
    border: none;
    background: #334155;
    color: #fff;
    font-size: 16px;
    box-sizing: border-box;
  }
  button {
    width: 100%;
    padding: 12px;
    margin-top: 10px;
    background: #3b82f6;
    border: none;
    border-radius: 8px;
    color: #fff;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.2s;
  }
  button:hover { background: #2563eb; }
  .hasil {
    margin-top: 20px;
    padding: 15px;
    background: #0f172a;
    border-radius: 10px;
    text-align: left;
  }
  .success { border-left: 4px solid #22c55e; }
  .error { border-left: 4px solid #ef4444; color: #ef4444; }
  .label { color: #94a3b8; font-size: 13px; }
</style>
</head>
<body>

<div class="box">
  <h1>🎮 Cek Region MLBB</h1>
  <p>Masukkan User ID + Zone ID untuk tahu region server</p>
  
  <form method="POST">
    <input type="number" name="uid" placeholder="Contoh User ID: 123456789" required>
    <input type="number" name="zone" placeholder="Contoh Zone ID: 1234" required>
    <button type="submit" name="cek">CEK SEKARANG</button>
  </form>

  <?php if($hasil): ?>
  <div class="hasil success">
    <p>✅ <b>Berjaya!</b></p>
    <p><span class="label">Nickname:</span> <?= $hasil['nick'] ?></p>
    <p><span class="label">Region:</span> <?= $hasil['region'] ?></p>
    <p><span class="label">ID:</span> <?= $hasil['id'] ?></p>
  </div>
  <?php endif; ?>

  <?php if($error): ?>
  <div class="hasil error">
    <p>❌ <?= $error ?></p>
  </div>
  <?php endif; ?>

</div>

</body>
</html>