[Mapy bez bariér](https://mapybezbarier.cz)
==========================================
Aplikace map přístupnosti Konta Bariéry Nadace Charty 77.

Požadavky
---------
* PHP 5.6
* Apache 2.4
* PostgresSQL 9.4

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

