<?php

function koneksi() {
    $conn = mysqli_connect('localhost', 'root', 'root', 'tubes') or die('Koneksi ke DB GAGAL!');

    return $conn;
}

function query($query) {
    $conn = koneksi();

    $result = mysqli_query($conn, $query) or die(mysqli_error($conn));

    $rows = [];
    while($row = mysqli_fetch_assoc($result)) {

    $rows[] = $row;
  }

  return $rows;
}


function tambah($data) {
    $conn = koneksi();

     //  cek apakah ada gambar yang diupload
     if($_FILES["gambar"]["error"] == 4){
      // jika user tidak memilih gambar, beri gambar default
      $gambar = 'nophoto.jpeg';
    } else {
      // jika user memilih gambar, jalankan fungsi upload
      $gambar = upload();
      // cek apakah upload berhasil
      if(!$gambar) {
        return false;
      }
    }
    
    $nama = htmlspecialchars($data["nama"]);
    $harga = htmlspecialchars($data["harga"]);
    $jenis = htmlspecialchars($data["jenis"]);
    $corak = htmlspecialchars($data["corak"]);
    $warna = htmlspecialchars($data["warna"]);
    $teknik_pembuatan = htmlspecialchars($data["teknik_pembuatan"]);
    $cocok_untuk = htmlspecialchars($data["cocok_untuk"]);
    $gramasi = htmlspecialchars($data["gramasi"]);
    // $gambar = htmlspecialchars($data["gambar"]);

    $query = "INSERT INTO 
                kain
              VALUES
                (null, '$nama', '$harga', '$jenis', '$corak', '$warna', '$teknik_pembuatan', '$cocok_untuk', '$gramasi', '$gambar')
             ";

    mysqli_query($conn, $query) or die(mysqli_error($conn));

    return mysqli_affected_rows($conn);
}

function hapus($id) {
  $conn = koneksi();

    // ambil data mahasiswa
    $k = query("SELECT * FROM kain WHERE id = $id")[0];

    // hapus data gambar
    if($k["gambar"] !== 'nophoto.jpeg') {
      unlink('../img/' . $k["gambar"]);
    }
  
  mysqli_query($conn, "DELETE FROM kain WHERE id = $id") or die(mysqli_error($conn));
  return mysqli_affected_rows($conn);
}

function ubah($data) {
  $conn = koneksi();

  $id = $data["id"];
  $nama = htmlspecialchars($data["nama"]);
  $harga = htmlspecialchars($data["harga"]);
  $jenis = htmlspecialchars($data["jenis"]);
  $corak = htmlspecialchars($data["corak"]);
  $warna = htmlspecialchars($data["warna"]);
  $teknik_pembuatan = htmlspecialchars($data["teknik_pembuatan"]);
  $cocok_untuk = htmlspecialchars($data["cocok_untuk"]);
  $gramasi = htmlspecialchars($data["gramasi"]);
  $gambar_lama = htmlspecialchars($data["gambar_lama"]);

  $gambar = upload();
  if(!$gambar) {
    return false;
  }

  if($gambar == 'nophoto.jpeg') {
    $gambar = $gambar_lama;
  }

  $query = "UPDATE kain SET
              nama = '$nama',
              harga = '$harga',
              jenis = '$jenis',
              corak = '$corak',
              warna = '$warna',
              teknik_pembuatan = '$teknik_pembuatan',
              cocok_untuk = '$cocok_untuk',
              gramasi = '$gramasi',
              gambar = '$gambar'
              WHERE id = $id
            ";
  mysqli_query($conn, $query) or die(mysqli_error($conn));

  return mysqli_affected_rows($conn);
}

function cari($keyword) {
  $conn = koneksi();

  if(isset($_GET["cari"])) {
    $keyword = $_GET["keyword"];
    $query = "SELECT * FROM kain
                WHERE 
              nama LIKE '%$keyword%' OR
              harga LIKE '%$keyword%' OR
              jenis LIKE '%$keyword%' OR
              corak LIKE '%$keyword%' OR
              warna LIKE '%$keyword%' OR
              teknik_pembuatan LIKE '%$keyword%' OR
              cocok_untuk LIKE '%$keyword%' OR
              gramasi LIKE '%$keyword%'
              ";
    $kain = query($query);

  $result = mysqli_query($conn, $query);

  $rows = [];
  while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
  }

  return $rows;
  }
}

function upload() {
  // siapkan data gambar
  $filename = $_FILES["gambar"]["name"];
  $filesize = $_FILES["gambar"]["size"];
  $filetmpname = $_FILES["gambar"]["tmp_name"];
  $filetype = pathinfo($filename, PATHINFO_EXTENSION);
  $allowedtype = ["jpg", "jpeg", "png"];

  // cek apakah file bukan gambar
  if(!in_array($filetype, $allowedtype)) {
    echo "<script>
          alert('Yang anda upload bukan gambar!');
          </script>";
    return false;
  }

  // cek jika ukuran lebih besar dari 1MB
  if($filesize > 1000000) {
    echo "<script>
    alert('Ukuran gambar terlalu besar!');
    </script>";
return false;
  }

  // lolos pengecekan gambar
  // buat nama file baru
  $newfilename = uniqid() . $filename;
  // upload gambar
  move_uploaded_file($filetmpname, '../img/' . $newfilename);

  return $newfilename;

}

function login($data) {
  $conn = koneksi();

  $username = htmlspecialchars($data['username']);
  $password = htmlspecialchars($data['password']);

  // cek dulu username
  if ($user = query("SELECT * FROM user WHERE username = '$username'")) {
    // cek password
    if(password_verify($password, $user['password'])) {
      // set session
    $_SESSION['login'] = true;
    
    header("Location: index.php");
    exit;
    }
  }
    return [
      'error' => true,
      'pesan' => 'Username / Password Salah!'
    ];
  }

function registrasi($data) {
  $conn = koneksi();

  $username = htmlspecialchars(strtolower($data['username']));
  $password1 = mysqli_real_escape_string($conn, $data['password1']);
  $password2 = mysqli_real_escape_string($conn, $data['password2']);

  // jika username / password kosong
  if(empty($username) || empty($password1) || empty($password2)) {
    echo "<script> 
          alert('username/ password tidak boleh kosong!');
          document.location.href = 'registrasi.php';
        </script>";
        return false;
  }

  // jika username sudah ada
  if(query("SELECT * FROM user WHERE username = '$username'")) {
    echo "<script> 
          alert('username sudah terdaftar!');
          document.location.href = 'registrasi.php';
        </script>";
        return false;
  }

  // jika konfirmasi password tidak sesuai
  if ($password1 !== $password2) {
    echo "<script> 
          alert('konfirmasi password tidak sesuai!');
          document.location.href = 'registrasi.php';
        </script>";
        return false;
  }

  // jika password < 5 digit
  if(strlen($password1) < 5 ) {
    echo "<script> 
          alert('password terlalu pendek!');
          document.location.href = 'registrasi.php';
        </script>";
        return false;
  }

  // jika username & password sudah sesuai
  // enkripsi password
  $password_baru = password_hash($password1, PASSWORD_DEFAULT);
  // insert ke tabel user
  $query = "INSERT INTO user
              VALUES
              (null, '$username', '$password_baru')
              ";
  mysqli_query($conn, $query) or die(mysqli_error($conn));
  return mysqli_affected_rows($conn);
}