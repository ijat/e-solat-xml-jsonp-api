e-solat-xml-jsonp-api
=====================

E-Solat XML/JSONP API adalah API third-party yang direka untuk mendapatkan waktu solat dari laman E-solat Malaysia.
Hanya waktu solat dalam negara sahaja.

Cara guna:
Upload di server anda dimana server tersebut mampu membuka file PHP. Kemudian access seperti biasa.
Cth http://example.com/esolat.php?ver=__X__&type=__Y__&kod=__Z__&callback=__V__

__X__ = adalah versi API. Hanya gantikan dengan 1 atau 2. Disyorkan menggunakan versi 2. (required)

__Y__ = type adalah format untuk diterima. Gantikan dengan xml atau json atau jsonp. (required)

__Z__ = kod zon E-Solat. Lihat di bawah. (required)

__V__ = hanya untuk type xml. Digunakan untuk AJAX. Biarkan kosong jika tiada.

Contoh XML:
http://example.com/esolat.php?ver=2&type=xml&kod=sgr03&callback=

Contoh JSON:
http://example.com/esolat.php?ver=2&type=json&kod=sgr03&callback=

Contoh JSONP:
http://example.com/esolat.php?ver=2&type=jsonp&kod=sgr03&callback=jsonpfunc

**
Sebarang pertanyaan? Email ke contact@ijat.my
