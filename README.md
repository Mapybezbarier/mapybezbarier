# [Mapy bez bariér](https://mapybezbarier.cz)

Aplikace mapa přístupnosti Konta Bariéry Nadace Charty 77.

## Požadavky

* PHP 7.1
* Apache 2.4
* PostgresSQL 9.6

## Instalace

### Instalace závislostí.

```bash
composer install
```

### Příprava prostředí a nezbytné adresářové struktury aplikace

```bash
mkdir -m 777 -p {images/{draft,object},storage/images/{draft,object},temp/{cache,session},log/tracy,asset/temp}
chmod 777 {images/{draft,object},storage/images/{draft,object},temp/{cache,session},log/tracy,asset/temp}
```

### Konfigurace prostředí

* app/constant.php - IP adresa vývojového/produkčního serveru; adresář se zálohami DB; e-mailové adresy správce/supportu
* app/config/credentials.neon - přístupy k databázovému serveru; klíče Google služeb; SMTP server

### Spuštění vývojového prostředí prostředí

```bash
cp .env.dist .env
docker-compose up
```

### Obnova produkční databáze na vývojové prostředí

Nejprve vytvoříme databázi, uživatele a poté spustíme obnovu pomocí `pg_restore`.

```bash
echo "CREATE DATABASE mapy_pristupnosti_db_01" | psql -h 127.0.0.1 -p 5432 -U postgres
echo "CREATE USER mapy_pristupnosti_db_01 WITH SUPERUSER PASSWORD 'secret';" | psql -h 127.0.0.1 -p 5432 -U postgres
pg_restore -h 127.0.0.1 -U mapy_pristupnosti_db_01 -d mapy_pristupnosti_db_01 -1 production_20180101.dump
```


## Dokumentace
Dokumentace vygenerovaná ze zdrojových kódů je součástí repozitáře (adresář /docs).
Bližší informace k databázové struktuře a veřejnému API k čerpání dat o mapových objektech je součástí [Wiki](https://github.com/Mapybezbarier/mapybezbarier/wiki).
