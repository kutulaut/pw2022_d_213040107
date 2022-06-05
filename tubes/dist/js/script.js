const tombolCari = document.querySelector(".tombol-cari");
const keyword = document.querySelector(".keyword");
const container = document.querySelector(".container");

tombolCari.style.display = "none";

// event ketika kita menuliskan keyword
keyword.addEventListener("keyup", function () {
  // fetch()
  fetch("../ajax/ajax_cari.php?keyword=" + keyword.value)
    .then((response) => response.text())
    .then((response) => (container.innerHTML = response));
});

// Preview Image untuk Tambah dan Ubah
function previewImage() {
  const gambar = document.querySelector(".gambar");
  const imgPreview = document.querySelector(".img-preview");

  const oFReader = new FileReader();
  oFReader.readAsDataURL(gambar.files[0]);

  oFReader.onload = function (oFREvent) {
    imgPreview.src = oFREvent.target.result;
  };
}

// untuk menambahkan fitur gambar
function previewImage() {
  const gambar = document.querySelector("#gambar");
  const imgPreview = document.querySelector("#img-preview");
  imgPreview.style.display = "block";
  var oFReader = new FileReader();
  oFReader.readAsDataURL(gambar.files[0]);

  oFReader.onload = function (oFREvent) {
    imgPreview.src = oFREvent.target.result;
  };
}
