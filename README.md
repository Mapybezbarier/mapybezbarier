[Mapy bez bariér](https://mapybezbarier.cz)
==========================================
Aplikace mapa přístupnosti Konta Bariéry Nadace Charty 77.

Požadavky
---------
* PHP 7.1
* Apache 2.4
* PostgresSQL 9.6

Instalace
---------
Instalace závislostí.
```
    composer install
```
Příprava prostředí a nezbytné adresářové struktury aplikace
```
      bash -c "mkdir -m 777 -p {images/{draft,object},storage/images/{draft,object},temp/{cache,session},log/tracy,asset/temp}"
      bash -c "chmod 777 {images/{draft,object},storage/images/{draft,object},temp/{cache,session},log/tracy,asset/temp}"
```
Konfigurace prostředí
* app/constant.php - IP adresa vývojového/produkčního serveru; adresář se zálohami DB; e-mailové adresy správce/supportu
* app/config/credentials.neon - přístupy k databázovému serveru; klíče Google služeb; SMTP server
 
Dokumentace
-----------
Dokumentace vygenerovaná ze zdrojových kódů je součástí repozitáře (adresář /docs).
Bližší informace k databázové struktuře a veřejnému API k čerpání dat o mapových objektech je součástí [Wiki](https://github.com/Mapybezbarier/mapybezbarier/wiki).
