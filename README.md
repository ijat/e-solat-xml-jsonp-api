e-Solat XML/JSONP API
=====================

__UPDATE__
Official link https://ijat.my/e-solat-xmljsonp-api

E-Solat XML/JSONP API adalah API third-party yang direka untuk mendapatkan waktu solat dari laman E-solat Malaysia.
Hanya waktu solat dalam negara sahaja.

Cara guna:
Upload di server anda dimana server tersebut mampu membuka file PHP. Kemudian access seperti biasa.
Cth http://example.com/esolat.php?ver=_X_&type=_Y_&kod=_Z_&callback=_V_&format=_C_

_X_ = adalah versi API. Hanya gantikan dengan 1 atau 2. Disyorkan menggunakan versi 2. (required)

_Y_ = type adalah format untuk diterima. Gantikan dengan xml atau json atau jsonp. (required)

_Z_ = kod zon E-Solat. Lihat di laman e-solat Malaysia atau di page E-Solat API di Ijat.my. (required)

_V_ = hanya untuk type jsonp. Digunakan untuk AJAX. Biarkan kosong jika tiada.

_C_ = format masa, '12' atau '24' jam. Default 24 jam.

Contoh XML:
http://example.com/esolat.php?ver=2&type=xml&kod=sgr03&callback=

Contoh JSON:
http://example.com/esolat.php?ver=2&type=json&kod=sgr03&callback=

Contoh JSONP:
http://example.com/esolat.php?ver=2&type=jsonp&kod=sgr03&callback=jsonpfunc

Contoh format masa 12 jam:
http://example.com/esolat.php?ver=2&type=json&kod=sgr03&format=12

**
Dibuat oleh Ijat (ijat.my)
**
Sebarang pertanyaan? Email ke contact@ijat.my
