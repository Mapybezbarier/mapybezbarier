--
-- PostgreSQL database dump
--

-- Dumped from database version 9.4.5
-- Dumped by pg_dump version 9.4.5
-- Started on 2016-02-16 09:30:13 CET

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- TOC entry 3161 (class 1262 OID 988633)
-- Name: mapy_pristupnosti_db_01; Type: DATABASE; Schema: -; Owner: mapy_pristupnosti_db_01
--

CREATE DATABASE mapy_pristupnosti_db_01 WITH TEMPLATE = template0 ENCODING = 'UTF8' LC_COLLATE = 'cs_CZ.UTF-8' LC_CTYPE = 'cs_CZ.UTF-8';


ALTER DATABASE mapy_pristupnosti_db_01 OWNER TO mapy_pristupnosti_db_01;

\connect mapy_pristupnosti_db_01

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- TOC entry 6 (class 2615 OID 2200)
-- Name: public; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA public;


ALTER SCHEMA public OWNER TO postgres;

--
-- TOC entry 3162 (class 0 OID 0)
-- Dependencies: 6
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: postgres
--

COMMENT ON SCHEMA public IS 'standard public schema';


--
-- TOC entry 8 (class 2615 OID 757487)
-- Name: service; Type: SCHEMA; Schema: -; Owner: mapy_pristupnosti_db_01
--

CREATE SCHEMA service;


ALTER SCHEMA service OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 7 (class 2615 OID 736672)
-- Name: versions; Type: SCHEMA; Schema: -; Owner: mapy_pristupnosti_db_01
--

CREATE SCHEMA versions;


ALTER SCHEMA versions OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 354 (class 3079 OID 11863)
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- TOC entry 3164 (class 0 OID 0)
-- Dependencies: 354
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner:
--

CREATE EXTENSION postgis;

SET search_path = public, pg_catalog;

--
-- TOC entry 378 (class 1255 OID 1003735)
-- Name: delete_after_constraints(); Type: FUNCTION; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE FUNCTION delete_after_constraints() RETURNS boolean
    LANGUAGE sql
    AS $$

ALTER TABLE "versions".map_object DROP CONSTRAINT map_object_object_id_fk;
ALTER TABLE "versions".map_object ADD CONSTRAINT map_object_object_id_fk FOREIGN KEY (object_id)
      REFERENCES map_object (object_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE;

ALTER TABLE "versions".wc_lang DROP CONSTRAINT wc_lang_wc_id_fk;
ALTER TABLE "versions".wc_lang ADD CONSTRAINT wc_lang_wc_id_fk FOREIGN KEY (wc_id)
      REFERENCES "versions".wc (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE "versions".platform_lang DROP CONSTRAINT platform_lang_platform_id_fk;
ALTER TABLE "versions".platform_lang ADD CONSTRAINT platform_lang_platform_id_fk FOREIGN KEY (platform_id)
      REFERENCES "versions".platform (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE "versions".rampskids_lang DROP CONSTRAINT rampskids_lang_rampskids_id_fk;
ALTER TABLE "versions".rampskids_lang ADD CONSTRAINT rampskids_lang_rampskids_id_fk FOREIGN KEY (rampskids_id)
      REFERENCES "versions".rampskids (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE "versions".elevator_lang DROP CONSTRAINT elevator_lang_elevator_id_fk;
ALTER TABLE "versions".elevator_lang ADD CONSTRAINT elevator_lang_elevator_id_fk FOREIGN KEY (elevator_id)
      REFERENCES "versions".elevator (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE "versions".rampskids DROP CONSTRAINT rampskids_map_object_id_fk;
ALTER TABLE "versions".rampskids ADD CONSTRAINT rampskids_map_object_id_fk FOREIGN KEY (map_object_id)
      REFERENCES "versions".map_object (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE "versions".wc DROP CONSTRAINT wc_map_object_id_fk;
ALTER TABLE "versions".wc ADD CONSTRAINT wc_map_object_id_fk FOREIGN KEY (map_object_id)
      REFERENCES "versions".map_object (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE "versions".platform DROP CONSTRAINT platform_map_object_id_fk;
ALTER TABLE "versions".platform ADD CONSTRAINT platform_map_object_id_fk FOREIGN KEY (map_object_id)
      REFERENCES "versions".map_object (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE RESTRICT;
      
ALTER TABLE "versions".elevator DROP CONSTRAINT elevator_map_object_id_fk;
ALTER TABLE "versions".elevator ADD CONSTRAINT elevator_map_object_id_fk FOREIGN KEY (map_object_id)
      REFERENCES "versions".map_object (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE RESTRICT;
      
ALTER TABLE "versions".map_object_lang DROP CONSTRAINT map_object_lang_map_object_id_fk;
ALTER TABLE "versions".map_object_lang ADD CONSTRAINT map_object_lang_map_object_id_fk FOREIGN KEY (map_object_id)
      REFERENCES "versions".map_object (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE RESTRICT;

--- END versions scheme

ALTER TABLE map_object_draft DROP CONSTRAINT map_object_draft_map_object_object_id_fk;
ALTER TABLE map_object_draft ADD CONSTRAINT map_object_draft_map_object_object_id_fk FOREIGN KEY (map_object_object_id)
      REFERENCES map_object (object_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE NO ACTION DEFERRABLE INITIALLY IMMEDIATE;
      
ALTER TABLE wc_lang DROP CONSTRAINT wc_lang_wc_id_fk;
ALTER TABLE wc_lang ADD CONSTRAINT wc_lang_wc_id_fk FOREIGN KEY (wc_id)
      REFERENCES wc (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE platform_lang DROP CONSTRAINT platform_lang_platform_id_fk;
ALTER TABLE platform_lang ADD CONSTRAINT platform_lang_platform_id_fk FOREIGN KEY (platform_id)
      REFERENCES platform (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE rampskids_lang DROP CONSTRAINT rampskids_lang_rampskids_id_fk;
ALTER TABLE rampskids_lang ADD CONSTRAINT rampskids_lang_rampskids_id_fk FOREIGN KEY (rampskids_id)
      REFERENCES rampskids (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE elevator_lang DROP CONSTRAINT elevator_lang_elevator_id_fk;
ALTER TABLE elevator_lang ADD CONSTRAINT elevator_lang_elevator_id_fk FOREIGN KEY (elevator_id)
      REFERENCES elevator (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE rampskids DROP CONSTRAINT rampskids_map_object_id_fk;
ALTER TABLE rampskids ADD CONSTRAINT rampskids_map_object_id_fk FOREIGN KEY (map_object_id)
      REFERENCES map_object (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE wc DROP CONSTRAINT wc_map_object_id_fk;
ALTER TABLE wc ADD CONSTRAINT wc_map_object_id_fk FOREIGN KEY (map_object_id)
      REFERENCES map_object (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE platform DROP CONSTRAINT platform_map_object_id_fk;
ALTER TABLE platform ADD CONSTRAINT platform_map_object_id_fk FOREIGN KEY (map_object_id)
      REFERENCES map_object (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE RESTRICT;
      
ALTER TABLE elevator DROP CONSTRAINT elevator_map_object_id_fk;
ALTER TABLE elevator ADD CONSTRAINT elevator_map_object_id_fk FOREIGN KEY (map_object_id)
      REFERENCES map_object (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE RESTRICT;
      
ALTER TABLE map_object_lang DROP CONSTRAINT map_object_lang_map_object_id_fk;
ALTER TABLE map_object_lang ADD CONSTRAINT map_object_lang_map_object_id_fk FOREIGN KEY (map_object_id)
      REFERENCES map_object (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE RESTRICT;

SELECT TRUE;

$$;


ALTER FUNCTION public.delete_after_constraints() OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 371 (class 1255 OID 892260)
-- Name: delete_all_objects(); Type: FUNCTION; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE FUNCTION delete_all_objects() RETURNS boolean
    LANGUAGE sql
    AS $$

SELECT delete_before_constraints();

--- START DELETE SQL

UPDATE map_object SET parent_object_id = NULL;
DELETE FROM map_object;

--- END DELETE SQL

SELECT delete_after_constraints();

SELECT setval('public.object_id_seq', 1, true);
SELECT TRUE;

$$;


ALTER FUNCTION public.delete_all_objects() OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 377 (class 1255 OID 1003734)
-- Name: delete_before_constraints(); Type: FUNCTION; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE FUNCTION delete_before_constraints() RETURNS boolean
    LANGUAGE sql
    AS $$

ALTER TABLE map_object_lang DROP CONSTRAINT map_object_lang_map_object_id_fk;
ALTER TABLE map_object_lang ADD CONSTRAINT map_object_lang_map_object_id_fk FOREIGN KEY (map_object_id)
      REFERENCES map_object (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE elevator DROP CONSTRAINT elevator_map_object_id_fk;
ALTER TABLE elevator ADD CONSTRAINT elevator_map_object_id_fk FOREIGN KEY (map_object_id)
      REFERENCES map_object (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE platform DROP CONSTRAINT platform_map_object_id_fk;
ALTER TABLE platform ADD CONSTRAINT platform_map_object_id_fk FOREIGN KEY (map_object_id)
      REFERENCES map_object (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE;
      
ALTER TABLE wc DROP CONSTRAINT wc_map_object_id_fk;
ALTER TABLE wc ADD CONSTRAINT wc_map_object_id_fk FOREIGN KEY (map_object_id)
      REFERENCES map_object (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE rampskids DROP CONSTRAINT rampskids_map_object_id_fk;
ALTER TABLE rampskids ADD CONSTRAINT rampskids_map_object_id_fk FOREIGN KEY (map_object_id)
      REFERENCES map_object (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE elevator_lang DROP CONSTRAINT elevator_lang_elevator_id_fk;
ALTER TABLE elevator_lang ADD CONSTRAINT elevator_lang_elevator_id_fk FOREIGN KEY (elevator_id)
      REFERENCES elevator (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE rampskids_lang DROP CONSTRAINT rampskids_lang_rampskids_id_fk;
ALTER TABLE rampskids_lang ADD CONSTRAINT rampskids_lang_rampskids_id_fk FOREIGN KEY (rampskids_id)
      REFERENCES rampskids (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE platform_lang DROP CONSTRAINT platform_lang_platform_id_fk;
ALTER TABLE platform_lang ADD CONSTRAINT platform_lang_platform_id_fk FOREIGN KEY (platform_id)
      REFERENCES platform (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE wc_lang DROP CONSTRAINT wc_lang_wc_id_fk;
ALTER TABLE wc_lang ADD CONSTRAINT wc_lang_wc_id_fk FOREIGN KEY (wc_id)
      REFERENCES wc (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE map_object_draft DROP CONSTRAINT map_object_draft_map_object_object_id_fk;
ALTER TABLE map_object_draft ADD CONSTRAINT map_object_draft_map_object_object_id_fk FOREIGN KEY (map_object_object_id)
      REFERENCES map_object (object_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE;
      
--- START versions scheme

ALTER TABLE "versions".map_object_lang DROP CONSTRAINT map_object_lang_map_object_id_fk;
ALTER TABLE "versions".map_object_lang ADD CONSTRAINT map_object_lang_map_object_id_fk FOREIGN KEY (map_object_id)
      REFERENCES "versions".map_object (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE "versions".elevator DROP CONSTRAINT elevator_map_object_id_fk;
ALTER TABLE "versions".elevator ADD CONSTRAINT elevator_map_object_id_fk FOREIGN KEY (map_object_id)
      REFERENCES "versions".map_object (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE "versions".platform DROP CONSTRAINT platform_map_object_id_fk;
ALTER TABLE "versions".platform ADD CONSTRAINT platform_map_object_id_fk FOREIGN KEY (map_object_id)
      REFERENCES "versions".map_object (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE;
      
ALTER TABLE "versions".wc DROP CONSTRAINT wc_map_object_id_fk;
ALTER TABLE "versions".wc ADD CONSTRAINT wc_map_object_id_fk FOREIGN KEY (map_object_id)
      REFERENCES "versions".map_object (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE "versions".rampskids DROP CONSTRAINT rampskids_map_object_id_fk;
ALTER TABLE "versions".rampskids ADD CONSTRAINT rampskids_map_object_id_fk FOREIGN KEY (map_object_id)
      REFERENCES "versions".map_object (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE "versions".elevator_lang DROP CONSTRAINT elevator_lang_elevator_id_fk;
ALTER TABLE "versions".elevator_lang ADD CONSTRAINT elevator_lang_elevator_id_fk FOREIGN KEY (elevator_id)
      REFERENCES "versions".elevator (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE "versions".rampskids_lang DROP CONSTRAINT rampskids_lang_rampskids_id_fk;
ALTER TABLE "versions".rampskids_lang ADD CONSTRAINT rampskids_lang_rampskids_id_fk FOREIGN KEY (rampskids_id)
      REFERENCES "versions".rampskids (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE "versions".platform_lang DROP CONSTRAINT platform_lang_platform_id_fk;
ALTER TABLE "versions".platform_lang ADD CONSTRAINT platform_lang_platform_id_fk FOREIGN KEY (platform_id)
      REFERENCES "versions".platform (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE "versions".wc_lang DROP CONSTRAINT wc_lang_wc_id_fk;
ALTER TABLE "versions".wc_lang ADD CONSTRAINT wc_lang_wc_id_fk FOREIGN KEY (wc_id)
      REFERENCES "versions".wc (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE "versions".map_object DROP CONSTRAINT map_object_object_id_fk;
ALTER TABLE "versions".map_object ADD CONSTRAINT map_object_object_id_fk FOREIGN KEY (object_id)
      REFERENCES map_object (object_id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE;

SELECT TRUE;

$$;


ALTER FUNCTION public.delete_before_constraints() OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 379 (class 1255 OID 816912)
-- Name: delete_object(integer); Type: FUNCTION; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE FUNCTION delete_object(integer) RETURNS boolean
    LANGUAGE sql
    AS $_$

SELECT delete_before_constraints();

--- START DELETE SQL

UPDATE map_object 
SET parent_object_id = NULL 
WHERE parent_object_id = $1;

DELETE FROM map_object WHERE object_id = $1;

--- END DELETE SQL

SELECT delete_after_constraints();

SELECT TRUE;

$_$;


ALTER FUNCTION public.delete_object(integer) OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 370 (class 1255 OID 850243)
-- Name: delete_object_by_source(integer); Type: FUNCTION; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE FUNCTION delete_object_by_source(integer) RETURNS boolean
    LANGUAGE sql
    AS $_$

SELECT delete_before_constraints();

--- START DELETE SQL

UPDATE map_object 
SET parent_object_id = NULL 
WHERE parent_object_id IN (
    SELECT object_id FROM map_object WHERE source_id = $1
);
DELETE FROM map_object WHERE source_id = $1;

--- END DELETE SQL

SELECT delete_after_constraints();

SELECT TRUE;

$_$;


ALTER FUNCTION public.delete_object_by_source(integer) OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 375 (class 1255 OID 996543)
-- Name: map_object_lang_prepare_search_title(); Type: FUNCTION; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE FUNCTION map_object_lang_prepare_search_title() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
  NEW.search_title = remove_diacritics(NEW.title);

  RETURN NEW;
END;

$$;


ALTER FUNCTION public.map_object_lang_prepare_search_title() OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 369 (class 1255 OID 737393)
-- Name: object_backup(integer); Type: FUNCTION; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE FUNCTION object_backup(_object_id integer) RETURNS boolean
    LANGUAGE plpgsql SECURITY DEFINER
    AS $$
DECLARE
  _map_object_id integer;
  _rampskids rampskids%ROWTYPE;
  _platform platform%ROWTYPE;
  _elevator elevator%ROWTYPE;
  _wc wc%ROWTYPE;

BEGIN
  SELECT id INTO _map_object_id FROM map_object WHERE object_id = _object_id;

  -- 1. zaloha hlavni tabulky vc. jazykove zavislych dat
  IF _map_object_id IS NULL THEN
    RAISE EXCEPTION 'Objekt s object_id % neexistuje.', _object_id;
  END IF;

  INSERT INTO "versions".map_object
    SELECT * FROM map_object WHERE id = _map_object_id;

  INSERT INTO "versions".map_object_lang
    SELECT * FROM map_object_lang WHERE map_object_id = _map_object_id;

  -- 2. zaloha ramp vc. jazykove zavislych dat
  FOR _rampskids IN (SELECT id FROM rampskids WHERE map_object_id = _map_object_id) LOOP
    INSERT INTO "versions".rampskids
      SELECT * FROM rampskids WHERE id = _rampskids.id;
    INSERT INTO "versions".rampskids_lang
      SELECT * FROM rampskids_lang WHERE rampskids_id = _rampskids.id;

    DELETE FROM rampskids_lang WHERE rampskids_id = _rampskids.id;
    DELETE FROM rampskids WHERE id = _rampskids.id;
  END LOOP;

  -- 3. zaloha plosin vc. jazykove zavislych dat
  FOR _platform IN (SELECT id FROM platform WHERE map_object_id = _map_object_id) LOOP
    INSERT INTO "versions".platform
      SELECT * FROM platform WHERE id = _platform.id;
    INSERT INTO "versions".platform_lang
      SELECT * FROM platform_lang WHERE platform_id = _platform.id;

    DELETE FROM platform_lang WHERE platform_id = _platform.id;
    DELETE FROM platform WHERE id = _platform.id;
  END LOOP;

  -- 4. zaloha vytahu vc. jazykove zavislych dat
  FOR _elevator IN (SELECT id FROM elevator WHERE map_object_id = _map_object_id) LOOP
    INSERT INTO "versions".elevator
      SELECT * FROM elevator WHERE id = _elevator.id;
    INSERT INTO "versions".elevator_lang
      SELECT * FROM elevator_lang WHERE elevator_id = _elevator.id;

    DELETE FROM elevator_lang WHERE elevator_id = _elevator.id;
    DELETE FROM elevator WHERE id = _elevator.id;
  END LOOP;

  -- 5. zaloha wc vc. jazykove zavislych dat
  FOR _wc IN (SELECT id FROM wc WHERE map_object_id = _map_object_id) LOOP
    INSERT INTO "versions".wc
      SELECT * FROM wc WHERE id = _wc.id;
    INSERT INTO "versions".wc_lang
      SELECT * FROM wc_lang WHERE wc_id = _wc.id;

    DELETE FROM wc_lang WHERE wc_id = _wc.id;
    DELETE FROM wc WHERE id = _wc.id;
  END LOOP;

  -- odlozim kontrolu existence rodicovskeho zaznamu na konec transakce - po object_backup se provadi INSERT
  SET CONSTRAINTS map_object_parent_object_id_fk DEFERRED;
  SET CONSTRAINTS "versions".map_object_parent_object_id_fk DEFERRED;
  SET CONSTRAINTS "versions".map_object_object_id_fk DEFERRED;
  SET CONSTRAINTS map_object_draft_map_object_object_id_fk DEFERRED;

  DELETE FROM map_object_lang WHERE map_object_id = _map_object_id;
  DELETE FROM map_object WHERE id = _map_object_id;

  RETURN TRUE;
END;
$$;


ALTER FUNCTION public.object_backup(_object_id integer) OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 380 (class 1255 OID 992626)
-- Name: object_merge(integer, integer); Type: FUNCTION; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE FUNCTION object_merge(_object_id_1 integer, _object_id_2 integer) RETURNS integer
    LANGUAGE plpgsql SECURITY DEFINER
    AS $$
DECLARE
  _modified_1 timestamp without time zone;
  _modified_2 timestamp without time zone;
  _new_object_id integer;
  _old_object_id integer;
  _map_object map_object%ROWTYPE;

BEGIN
  SELECT modified_date INTO _modified_1 FROM map_object WHERE object_id = _object_id_1;
  SELECT modified_date INTO _modified_2 FROM map_object WHERE object_id = _object_id_2;

  -- zjistim, jaky objekt ma novejsi data - z toho druheho pak presunu verze a smazu jej
  IF _modified_1 IS NULL OR _modified_2 IS NULL THEN
    RAISE EXCEPTION 'Objekt s object_id % nebo % neexistuje.', _object_id_1, _object_id_2;
  ELSE
    IF _modified_1 < _modified_2 THEN
      _new_object_id = _object_id_2;
      _old_object_id = _object_id_1;
    ELSE
      _new_object_id = _object_id_1;
      _old_object_id = _object_id_2;
    END IF;
  END IF;

  DELETE FROM map_object_draft WHERE map_object_object_id = _old_object_id;
  PERFORM object_backup(_old_object_id);

  UPDATE map_object
  SET parent_object_id = _new_object_id
  WHERE parent_object_id = _old_object_id;

  UPDATE "versions".map_object
  SET parent_object_id = _new_object_id
  WHERE parent_object_id = _old_object_id;

  UPDATE "versions".map_object
  SET object_id = _new_object_id
  WHERE object_id = _old_object_id; 

  RETURN _new_object_id;
END;
$$;


ALTER FUNCTION public.object_merge(_object_id_1 integer, _object_id_2 integer) OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 374 (class 1255 OID 946979)
-- Name: object_version_revert(integer); Type: FUNCTION; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE FUNCTION object_version_revert(_version_id integer) RETURNS boolean
    LANGUAGE plpgsql SECURITY DEFINER
    AS $$
DECLARE
  _object_id integer;
  _rampskids rampskids%ROWTYPE;
  _platform platform%ROWTYPE;
  _elevator elevator%ROWTYPE;
  _wc wc%ROWTYPE;

BEGIN
  SELECT object_id INTO _object_id FROM versions.map_object WHERE id = _version_id;
  
  IF _object_id IS NULL THEN
      RAISE EXCEPTION 'Verze s id % neexistuje.', _version_id;
  END IF;

  PERFORM object_backup(_object_id);

  -- 1. revert hlavni tabulky vc. jazykove zavislych dat
  INSERT INTO map_object
    SELECT * FROM "versions".map_object WHERE id = _version_id;

  INSERT INTO map_object_lang
    SELECT * FROM "versions".map_object_lang WHERE map_object_id = _version_id;

  -- 2. revert ramp vc. jazykove zavislych dat
  FOR _rampskids IN (SELECT id FROM "versions".rampskids WHERE map_object_id = _version_id) LOOP
    INSERT INTO rampskids
      SELECT * FROM "versions".rampskids WHERE id = _rampskids.id;
    INSERT INTO rampskids_lang
      SELECT * FROM "versions".rampskids_lang WHERE rampskids_id = _rampskids.id;

    DELETE FROM "versions".rampskids_lang WHERE rampskids_id = _rampskids.id;
    DELETE FROM "versions".rampskids WHERE id = _rampskids.id;
  END LOOP;

  -- 3. revert plosin vc. jazykove zavislych dat
  FOR _platform IN (SELECT id FROM "versions".platform WHERE map_object_id = _version_id) LOOP
    INSERT INTO platform
      SELECT * FROM "versions".platform WHERE id = _platform.id;
    INSERT INTO platform_lang
      SELECT * FROM "versions".platform_lang WHERE platform_id = _platform.id;

    DELETE FROM "versions".platform_lang WHERE platform_id = _platform.id;
    DELETE FROM "versions".platform WHERE id = _platform.id;
  END LOOP;

  -- 4. revert vytahu vc. jazykove zavislych dat
  FOR _elevator IN (SELECT id FROM "versions".elevator WHERE map_object_id = _version_id) LOOP
    INSERT INTO elevator
      SELECT * FROM "versions".elevator WHERE id = _elevator.id;
    INSERT INTO elevator_lang
      SELECT * FROM "versions".elevator_lang WHERE elevator_id = _elevator.id;

    DELETE FROM "versions".elevator_lang WHERE elevator_id = _elevator.id;
    DELETE FROM "versions".elevator WHERE id = _elevator.id;
  END LOOP;

  -- 5. revert wc vc. jazykove zavislych dat
  FOR _wc IN (SELECT id FROM "versions".wc WHERE map_object_id = _version_id) LOOP
    INSERT INTO wc
      SELECT * FROM "versions".wc WHERE id = _wc.id;
    INSERT INTO wc_lang
      SELECT * FROM "versions".wc_lang WHERE wc_id = _wc.id;

    DELETE FROM "versions".wc_lang WHERE wc_id = _wc.id;
    DELETE FROM "versions".wc WHERE id = _wc.id;
  END LOOP;

  DELETE FROM "versions".map_object_lang WHERE map_object_id = _version_id;
  DELETE FROM "versions".map_object WHERE id = _version_id;

  RETURN TRUE;
END;
$$;


ALTER FUNCTION public.object_version_revert(_version_id integer) OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 376 (class 1255 OID 986086)
-- Name: object_version_split(integer); Type: FUNCTION; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE FUNCTION object_version_split(_version_id integer) RETURNS integer
    LANGUAGE plpgsql SECURITY DEFINER
    AS $$
DECLARE
  _object_id integer;
  _map_object map_object%ROWTYPE;
  _map_object_lang map_object_lang%ROWTYPE;
  _rampskids rampskids%ROWTYPE;
  _platform platform%ROWTYPE;
  _elevator elevator%ROWTYPE;
  _wc wc%ROWTYPE;
  _new_object_id integer;
  _new_map_object_id integer;
  _rampskids_inner rampskids%ROWTYPE;
  _rampskids_lang rampskids_lang%ROWTYPE;
  _platform_inner platform%ROWTYPE;
  _platform_lang platform_lang%ROWTYPE;
  _elevator_inner elevator%ROWTYPE;
  _elevator_lang elevator_lang%ROWTYPE;
  _wc_inner wc%ROWTYPE;
  _wc_lang wc_lang%ROWTYPE;
  _new_atch_id integer;

BEGIN
  SELECT object_id INTO _object_id FROM versions.map_object WHERE id = _version_id;
  
  IF _object_id IS NULL THEN
      RAISE EXCEPTION 'Verze s id % neexistuje.', _version_id;
  END IF;

  _new_object_id = nextval('object_id_seq');
  _new_map_object_id = nextval('map_object_id_seq');

  -- 1. split hlavni tabulky vc. jazykove zavislych dat
   SELECT * INTO _map_object FROM "versions".map_object WHERE id = _version_id;
  _map_object.id = _new_map_object_id;
  _map_object.object_id = _new_object_id;
  INSERT INTO map_object SELECT _map_object.*;

   SELECT * INTO _map_object_lang FROM "versions".map_object_lang WHERE map_object_id = _version_id;
  _map_object_lang.map_object_id = _new_map_object_id;
  INSERT INTO map_object_lang SELECT _map_object_lang.*;

  -- 2. split ramp vc. jazykove zavislych dat
  FOR _rampskids IN (SELECT id FROM "versions".rampskids WHERE map_object_id = _version_id) LOOP
     _new_atch_id = nextval('rampskids_id_seq');
     
     SELECT * INTO _rampskids_inner FROM "versions".rampskids WHERE id = _rampskids.id;
     _rampskids_inner.id = _new_atch_id;
     _rampskids_inner.map_object_id = _new_map_object_id;
     INSERT INTO rampskids SELECT _rampskids_inner.*;
  
     SELECT * INTO _rampskids_lang FROM "versions".rampskids_lang WHERE rampskids_id = _rampskids.id;
     
     IF _rampskids_lang.lang_id IS NOT NULL THEN
        _rampskids_lang.rampskids_id = _new_atch_id;
        INSERT INTO rampskids_lang SELECT _rampskids_lang.*;
     END IF;
  END LOOP;

  -- 3. split plosin vc. jazykove zavislych dat
  FOR _platform IN (SELECT id FROM "versions".platform WHERE map_object_id = _version_id) LOOP
     _new_atch_id = nextval('platform_id_seq');
     
     SELECT * INTO _platform_inner FROM "versions".platform WHERE id = _platform.id;
     _platform_inner.id = _new_atch_id;
     _platform_inner.map_object_id = _new_map_object_id;
     INSERT INTO platform SELECT _platform_inner.*;
  
     SELECT * INTO _platform_lang FROM "versions".platform_lang WHERE platform_id = _platform.id;
     
     IF _platform_lang.lang_id IS NOT NULL THEN
        _platform_lang.platform_id = _new_atch_id;
        INSERT INTO platform_lang SELECT _platform_lang.*;
     END IF;
  END LOOP;

  -- 4. split vytahu vc. jazykove zavislych dat
  FOR _elevator IN (SELECT id FROM "versions".elevator WHERE map_object_id = _version_id) LOOP
     _new_atch_id = nextval('elevator_id_seq');
     
     SELECT * INTO _elevator_inner FROM "versions".elevator WHERE id = _elevator.id;
     _elevator_inner.id = _new_atch_id;
     _elevator_inner.map_object_id = _new_map_object_id;
     INSERT INTO elevator SELECT _elevator_inner.*;
  
     SELECT * INTO _elevator_lang FROM "versions".elevator_lang WHERE elevator_id = _elevator.id;
     
     IF _elevator_lang.lang_id IS NOT NULL THEN
        _elevator_lang.elevator_id = _new_atch_id;
        INSERT INTO elevator_lang SELECT _elevator_lang.*;
     END IF;
  END LOOP;
  
  -- 5. split wc vc. jazykove zavislych dat
  FOR _wc IN (SELECT id FROM "versions".wc WHERE map_object_id = _version_id) LOOP
     _new_atch_id = nextval('wc_id_seq');
     
     SELECT * INTO _wc_inner FROM "versions".wc WHERE id = _wc.id;
     _wc_inner.id = _new_atch_id;
     _wc_inner.map_object_id = _new_map_object_id;
     INSERT INTO wc SELECT _wc_inner.*;
  
     SELECT * INTO _wc_lang FROM "versions".wc_lang WHERE wc_id = _wc.id;
     
     IF _wc_lang.lang_id IS NOT NULL THEN
        _wc_lang.wc_id = _new_atch_id;
        INSERT INTO wc_lang SELECT _wc_lang.*;
     END IF;
  END LOOP;
  
  RETURN _new_object_id;
END;
$$;


ALTER FUNCTION public.object_version_split(_version_id integer) OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 373 (class 1255 OID 943697)
-- Name: remove_diacritics(text); Type: FUNCTION; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE FUNCTION remove_diacritics(text) RETURNS text
    LANGUAGE plpgsql IMMUTABLE
    AS $_$
DECLARE
    _ret text;
BEGIN
    BEGIN
        SELECT to_ascii(convert_to($1, 'latin2'),'latin2') INTO _ret;
    EXCEPTION
        WHEN OTHERS THEN
            SELECT unaccent_string($1) INTO _ret;
    END;

    RETURN regexp_replace(_ret, '[\s]+', ' ', 'g');
END;
$_$;


ALTER FUNCTION public.remove_diacritics(text) OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 367 (class 1255 OID 731320)
-- Name: set_modified_before_insert_update(); Type: FUNCTION; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE FUNCTION set_modified_before_insert_update() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
  NEW.modified_date = CURRENT_TIMESTAMP;

  RETURN NEW;
END;

$$;


ALTER FUNCTION public.set_modified_before_insert_update() OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 368 (class 1255 OID 943695)
-- Name: to_ascii(bytea, name); Type: FUNCTION; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE FUNCTION to_ascii(bytea, name) RETURNS text
    LANGUAGE internal STRICT
    AS $$to_ascii_encname$$;


ALTER FUNCTION public.to_ascii(bytea, name) OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 372 (class 1255 OID 943696)
-- Name: unaccent_string(text); Type: FUNCTION; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE FUNCTION unaccent_string(text) RETURNS text
    LANGUAGE sql IMMUTABLE STRICT
    AS $_$
SELECT translate(
    $1,
    'âãäåāăąáÁÂÃÄÅĀĂĄÁèééêëēĕėęěĒĔĖĘĚìíîïìĩīĭÌÍÎÏÌĨĪĬóôõöōŏőÒÓÔÕÖŌŎŐùúûüũūŭůÙÚÛÜŨŪŬŮşšŠčČřŘžŽýÝťŤďĎňŇ',
    'aaaaaaaaaaaaaaaaaeeeeeeeeeeeeeeeiiiiiiiiiiiiiiiiooooooooooooooouuuuuuuuuuuuuuuusssccrrzzyyttddnn'
);
$_$;


ALTER FUNCTION public.unaccent_string(text) OWNER TO mapy_pristupnosti_db_01;

-- Function: service.invalidate_markers_cache()
CREATE OR REPLACE FUNCTION service.invalidate_markers_cache()
  RETURNS trigger AS
$BODY$
BEGIN
  TRUNCATE service.markers_cache;
  RETURN NEW;
END;

$BODY$
LANGUAGE plpgsql VOLATILE;

ALTER FUNCTION service.invalidate_markers_cache() OWNER TO mapy_pristupnosti_db_01;


SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 235 (class 1259 OID 736181)
-- Name: a_o_b_announcement; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE a_o_b_announcement (
    id integer NOT NULL,
    title character varying(255),
    pair_key character varying(255),
    combine_key json
);


ALTER TABLE a_o_b_announcement OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 234 (class 1259 OID 736179)
-- Name: a_o_b_announcement_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE a_o_b_announcement_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE a_o_b_announcement_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3165 (class 0 OID 0)
-- Dependencies: 234
-- Name: a_o_b_announcement_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE a_o_b_announcement_id_seq OWNED BY a_o_b_announcement.id;


--
-- TOC entry 180 (class 1259 OID 731147)
-- Name: accessibility; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE accessibility (
    id integer NOT NULL,
    title character varying(255) NOT NULL,
    pair_key character varying(255)
);


ALTER TABLE accessibility OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 179 (class 1259 OID 731145)
-- Name: accessibility_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE accessibility_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE accessibility_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3166 (class 0 OID 0)
-- Dependencies: 179
-- Name: accessibility_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE accessibility_id_seq OWNED BY accessibility.id;


--
-- TOC entry 207 (class 1259 OID 736069)
-- Name: bell_type; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE bell_type (
    id integer NOT NULL,
    title character varying(255),
    pair_key character varying(255)
);


ALTER TABLE bell_type OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 206 (class 1259 OID 736067)
-- Name: bell_type_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE bell_type_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE bell_type_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3167 (class 0 OID 0)
-- Dependencies: 206
-- Name: bell_type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE bell_type_id_seq OWNED BY bell_type.id;


--
-- TOC entry 299 (class 1259 OID 819678)
-- Name: contrast_marking_localization; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE contrast_marking_localization (
    id integer NOT NULL,
    title character varying(255),
    pair_key character varying(255),
    combine_key json
);


ALTER TABLE contrast_marking_localization OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 298 (class 1259 OID 819676)
-- Name: contrast_marking_localization_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE contrast_marking_localization_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE contrast_marking_localization_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3168 (class 0 OID 0)
-- Dependencies: 298
-- Name: contrast_marking_localization_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE contrast_marking_localization_id_seq OWNED BY contrast_marking_localization.id;


--
-- TOC entry 211 (class 1259 OID 736085)
-- Name: door_opening; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE door_opening (
    id integer NOT NULL,
    title character varying(255),
    pair_key character varying(255)
);


ALTER TABLE door_opening OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 213 (class 1259 OID 736093)
-- Name: door_opening_direction; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE door_opening_direction (
    id integer NOT NULL,
    title character varying(255),
    pair_key character varying(255)
);


ALTER TABLE door_opening_direction OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 212 (class 1259 OID 736091)
-- Name: door_opening_direction_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE door_opening_direction_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE door_opening_direction_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3169 (class 0 OID 0)
-- Dependencies: 212
-- Name: door_opening_direction_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE door_opening_direction_id_seq OWNED BY door_opening_direction.id;


--
-- TOC entry 210 (class 1259 OID 736083)
-- Name: door_opening_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE door_opening_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE door_opening_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3170 (class 0 OID 0)
-- Dependencies: 210
-- Name: door_opening_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE door_opening_id_seq OWNED BY door_opening.id;


--
-- TOC entry 209 (class 1259 OID 736077)
-- Name: door_type; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE door_type (
    id integer NOT NULL,
    title character varying(255),
    pair_key character varying(255)
);


ALTER TABLE door_type OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 208 (class 1259 OID 736075)
-- Name: door_type_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE door_type_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE door_type_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3171 (class 0 OID 0)
-- Dependencies: 208
-- Name: door_type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE door_type_id_seq OWNED BY door_type.id;


--
-- TOC entry 195 (class 1259 OID 731388)
-- Name: elevator; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE elevator (
    id integer NOT NULL,
    map_object_id integer NOT NULL,
    elevator_access_id integer,
    elevator_type_id integer,
    elevator_driveoff_id integer,
    elevator_control1_max_height integer,
    elevator_control1_relief_marking_id integer,
    elevator_control1_flat_marking_id integer,
    elevator_is_control1_braille_marking boolean,
    elevator_is_a_o_b boolean,
    elevator_aob_is_above_door boolean,
    elevator_a_o_b_announcements_scheme_id integer,
    elevator_is_cage_passthrough boolean,
    elevator_cage_width integer,
    elevator_cage_depth integer,
    elevator_cage_seconddoor_localization_id integer,
    elevator_cage_control_distance integer,
    elevator_cage_control_height integer,
    elevator_control2_relief_marking_id integer,
    elevator_control2_flat_marking_id integer,
    elevator_is_control2_braille_marking boolean,
    elevator_is_cage_control_announcement_acoustic boolean,
    elevator_is_cage_control_announcement_phonetic boolean,
    elevator_is_cage_handle boolean,
    elevator_handle_localization_id integer,
    elevator_is_cage_mirror boolean,
    elevator_cage_mirror_localization_id integer,
    elevator_cage_mirror_height integer,
    elevator_is_cage_seat boolean,
    elevator_is_cage_seat_withinreach boolean,
    elevator_is_cage_seat_functional boolean,
    entryarea_width integer,
    entryarea_depth integer,
    entryarea_height_elevation integer,
    door1_width integer,
    door1_opening_id integer,
    door2_width integer,
    door2_opening_id integer
);


ALTER TABLE elevator OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 239 (class 1259 OID 736197)
-- Name: elevator_cage_mirror_localization; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE elevator_cage_mirror_localization (
    id integer NOT NULL,
    title character varying(255),
    pair_key character varying(255)
);


ALTER TABLE elevator_cage_mirror_localization OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 238 (class 1259 OID 736195)
-- Name: elevator_cage_mirror_localization_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE elevator_cage_mirror_localization_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE elevator_cage_mirror_localization_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3172 (class 0 OID 0)
-- Dependencies: 238
-- Name: elevator_cage_mirror_localization_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE elevator_cage_mirror_localization_id_seq OWNED BY elevator_cage_mirror_localization.id;


--
-- TOC entry 237 (class 1259 OID 736189)
-- Name: elevator_cage_seconddoor_localization; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE elevator_cage_seconddoor_localization (
    id integer NOT NULL,
    title character varying(255),
    pair_key character varying(255)
);


ALTER TABLE elevator_cage_seconddoor_localization OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 236 (class 1259 OID 736187)
-- Name: elevator_cage_seconddoor_localization_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE elevator_cage_seconddoor_localization_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE elevator_cage_seconddoor_localization_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3173 (class 0 OID 0)
-- Dependencies: 236
-- Name: elevator_cage_seconddoor_localization_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE elevator_cage_seconddoor_localization_id_seq OWNED BY elevator_cage_seconddoor_localization.id;


--
-- TOC entry 233 (class 1259 OID 736173)
-- Name: elevator_control_flat_marking; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE elevator_control_flat_marking (
    id integer NOT NULL,
    title character varying(255),
    pair_key character varying(255),
    combine_key json
);


ALTER TABLE elevator_control_flat_marking OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 232 (class 1259 OID 736171)
-- Name: elevator_control_flat_marking_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE elevator_control_flat_marking_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE elevator_control_flat_marking_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3174 (class 0 OID 0)
-- Dependencies: 232
-- Name: elevator_control_flat_marking_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE elevator_control_flat_marking_id_seq OWNED BY elevator_control_flat_marking.id;


--
-- TOC entry 231 (class 1259 OID 736165)
-- Name: elevator_control_relief_marking; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE elevator_control_relief_marking (
    id integer NOT NULL,
    title character varying(255),
    pair_key character varying(255)
);


ALTER TABLE elevator_control_relief_marking OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 230 (class 1259 OID 736163)
-- Name: elevator_control_relief_marking_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE elevator_control_relief_marking_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE elevator_control_relief_marking_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3175 (class 0 OID 0)
-- Dependencies: 230
-- Name: elevator_control_relief_marking_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE elevator_control_relief_marking_id_seq OWNED BY elevator_control_relief_marking.id;


--
-- TOC entry 229 (class 1259 OID 736157)
-- Name: elevator_driveoff; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE elevator_driveoff (
    id integer NOT NULL,
    title character varying(255),
    pair_key character varying(255)
);


ALTER TABLE elevator_driveoff OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 228 (class 1259 OID 736155)
-- Name: elevator_driveoff_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE elevator_driveoff_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE elevator_driveoff_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3176 (class 0 OID 0)
-- Dependencies: 228
-- Name: elevator_driveoff_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE elevator_driveoff_id_seq OWNED BY elevator_driveoff.id;


--
-- TOC entry 225 (class 1259 OID 736141)
-- Name: elevator_handle_localization; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE elevator_handle_localization (
    id integer NOT NULL,
    title character varying(255),
    pair_key character varying(255)
);


ALTER TABLE elevator_handle_localization OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 224 (class 1259 OID 736139)
-- Name: elevator_handle_localization_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE elevator_handle_localization_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE elevator_handle_localization_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3177 (class 0 OID 0)
-- Dependencies: 224
-- Name: elevator_handle_localization_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE elevator_handle_localization_id_seq OWNED BY elevator_handle_localization.id;


--
-- TOC entry 194 (class 1259 OID 731386)
-- Name: elevator_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE elevator_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE elevator_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3178 (class 0 OID 0)
-- Dependencies: 194
-- Name: elevator_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE elevator_id_seq OWNED BY elevator.id;


--
-- TOC entry 285 (class 1259 OID 737334)
-- Name: elevator_lang; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE elevator_lang (
    elevator_id integer NOT NULL,
    lang_id character(2) NOT NULL,
    elevator_localization character varying(255),
    elevator_access_provided_by character varying(255),
    elevator_connects_floors character varying(255),
    elevator_aob_localization character varying(255),
    elevator_has_notes text,
    elevator_has_description text
);


ALTER TABLE elevator_lang OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 227 (class 1259 OID 736149)
-- Name: elevator_type; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE elevator_type (
    id integer NOT NULL,
    title character varying(255),
    pair_key character varying(255)
);


ALTER TABLE elevator_type OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 226 (class 1259 OID 736147)
-- Name: elevator_type_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE elevator_type_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE elevator_type_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3179 (class 0 OID 0)
-- Dependencies: 226
-- Name: elevator_type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE elevator_type_id_seq OWNED BY elevator_type.id;


--
-- TOC entry 205 (class 1259 OID 736061)
-- Name: entrance_accessibility; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE entrance_accessibility (
    id integer NOT NULL,
    title character varying(255),
    pair_key character varying(255)
);


ALTER TABLE entrance_accessibility OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 204 (class 1259 OID 736059)
-- Name: entrance_accessibility_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE entrance_accessibility_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE entrance_accessibility_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3180 (class 0 OID 0)
-- Dependencies: 204
-- Name: entrance_accessibility_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE entrance_accessibility_id_seq OWNED BY entrance_accessibility.id;


--
-- TOC entry 203 (class 1259 OID 736053)
-- Name: entrance_guidingline; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE entrance_guidingline (
    id integer NOT NULL,
    title character varying(255),
    pair_key character varying(255),
    combine_key json
);


ALTER TABLE entrance_guidingline OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 202 (class 1259 OID 736051)
-- Name: entrance_guidingline_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE entrance_guidingline_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE entrance_guidingline_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3181 (class 0 OID 0)
-- Dependencies: 202
-- Name: entrance_guidingline_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE entrance_guidingline_id_seq OWNED BY entrance_guidingline.id;


--
-- TOC entry 245 (class 1259 OID 736221)
-- Name: entryarea_entry; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE entryarea_entry (
    id integer NOT NULL,
    title character varying(255),
    pair_key character varying(255)
);


ALTER TABLE entryarea_entry OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 244 (class 1259 OID 736219)
-- Name: entryarea_entry_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE entryarea_entry_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE entryarea_entry_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3182 (class 0 OID 0)
-- Dependencies: 244
-- Name: entryarea_entry_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE entryarea_entry_id_seq OWNED BY entryarea_entry.id;


--
-- TOC entry 291 (class 1259 OID 757641)
-- Name: exchange_source; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE exchange_source (
    id integer NOT NULL,
    title character varying(255) NOT NULL,
    format character varying(255) NOT NULL,
    editable boolean DEFAULT false NOT NULL
);


ALTER TABLE exchange_source OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 263 (class 1259 OID 736293)
-- Name: hallway_door_marking; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE hallway_door_marking (
    id integer NOT NULL,
    title character varying(255),
    pair_key character varying(255)
);


ALTER TABLE hallway_door_marking OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 262 (class 1259 OID 736291)
-- Name: hallway_door_marking_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE hallway_door_marking_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE hallway_door_marking_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3183 (class 0 OID 0)
-- Dependencies: 262
-- Name: hallway_door_marking_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE hallway_door_marking_id_seq OWNED BY hallway_door_marking.id;


--
-- TOC entry 265 (class 1259 OID 736301)
-- Name: handle_type; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE handle_type (
    id integer NOT NULL,
    title character varying(255),
    pair_key character varying(255)
);


ALTER TABLE handle_type OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 264 (class 1259 OID 736299)
-- Name: handle_type_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE handle_type_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE handle_type_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3184 (class 0 OID 0)
-- Dependencies: 264
-- Name: handle_type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE handle_type_id_seq OWNED BY handle_type.id;


--
-- TOC entry 301 (class 1259 OID 878359)
-- Name: import; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE import (
    id integer NOT NULL,
    created timestamp without time zone DEFAULT now(),
    last_run timestamp without time zone,
    last_success timestamp without time zone,
    source_id integer NOT NULL,
    user_id integer NOT NULL,
    url text NOT NULL,
    hours_offset integer DEFAULT 24 NOT NULL,
    certified boolean DEFAULT false NOT NULL,
    license_id integer DEFAULT 1 NOT NULL
);


ALTER TABLE import OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 300 (class 1259 OID 878357)
-- Name: import_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE import_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE import_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3185 (class 0 OID 0)
-- Dependencies: 300
-- Name: import_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE import_id_seq OWNED BY import.id;


--
-- TOC entry 303 (class 1259 OID 878381)
-- Name: import_log; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE import_log (
    id integer NOT NULL,
    import_id integer,
    created timestamp without time zone DEFAULT now(),
    data json NOT NULL,
    manual_settings json,
    count integer NOT NULL
);


ALTER TABLE import_log OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 302 (class 1259 OID 878379)
-- Name: import_log_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE import_log_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE import_log_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3186 (class 0 OID 0)
-- Dependencies: 302
-- Name: import_log_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE import_log_id_seq OWNED BY import_log.id;


--
-- TOC entry 290 (class 1259 OID 757639)
-- Name: import_source_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE import_source_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE import_source_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3187 (class 0 OID 0)
-- Dependencies: 290
-- Name: import_source_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE import_source_id_seq OWNED BY exchange_source.id;


--
-- TOC entry 174 (class 1259 OID 731124)
-- Name: lang; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE lang (
    id character(2) NOT NULL,
    title character varying(255) NOT NULL,
    pair_key character varying(255)
);


ALTER TABLE lang OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 176 (class 1259 OID 731131)
-- Name: license; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE license (
    id integer NOT NULL,
    title character varying(255) NOT NULL,
    url text,
    pair_key character varying(255)
);


ALTER TABLE license OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 175 (class 1259 OID 731129)
-- Name: license_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE license_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE license_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3188 (class 0 OID 0)
-- Dependencies: 175
-- Name: license_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE license_id_seq OWNED BY license.id;


--
-- TOC entry 336 (class 1259 OID 943162)
-- Name: log; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE log (
    id integer NOT NULL,
    changed_id integer,
    title character varying(128),
    user_id integer NOT NULL,
    created timestamp without time zone DEFAULT now(),
    custom_data json,
    module_key character varying(64) NOT NULL,
    action_key character varying(64) NOT NULL
);


ALTER TABLE log OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 335 (class 1259 OID 943160)
-- Name: log_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE log_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE log_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3189 (class 0 OID 0)
-- Dependencies: 335
-- Name: log_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE log_id_seq OWNED BY log.id;


--
-- TOC entry 295 (class 1259 OID 816188)
-- Name: object_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE object_id_seq
    START WITH 10000
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE object_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 186 (class 1259 OID 731282)
-- Name: map_object; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE map_object (
    id integer NOT NULL,
    object_id integer DEFAULT nextval('object_id_seq'::regclass) NOT NULL,
    parent_object_id integer,
    modified_date timestamp without time zone NOT NULL,
    mapping_date timestamp without time zone NOT NULL,
    user_id integer DEFAULT 1 NOT NULL,
    object_type_id integer NOT NULL,
    accessibility_id integer NOT NULL,
    license_id integer NOT NULL,
    certified boolean DEFAULT false NOT NULL,
    organization_ic character varying(255),
    organization_name character varying(255),
    latitude numeric,
    longitude numeric,
    ruian_address integer,
    zipcode character varying(255),
    city character varying(255),
    city_part character varying(255),
    street character varying(255),
    street_desc_no integer,
    street_no_is_alternative boolean NOT NULL DEFAULT FALSE,
    street_orient_no integer,
    street_orient_symbol character varying(255),
    entrance1_is_reserved_parking boolean,
    entrance1_number_of_reserved_parking integer,
    entrance1_is_longitudinal_inclination boolean,
    entrance1_longitudinal_inclination double precision,
    entrance1_is_transverse_inclination boolean,
    entrance1_transverse_inclination double precision,
    entrance1_is_difficult_surface boolean,
    entrance1_guidingline_id integer,
    entrance1_accessibility_id integer,
    entrance1_area_before_door_width integer,
    entrance1_area_before_door_depth integer,
    entrance1_lobby_width integer,
    entrance1_lobby_depth integer,
    entrance1_is_a_o_b boolean,
    entrance1_aob_is_above_door boolean,
    entrance1_bell_type_id integer,
    entrance1_bell_height integer,
    entrance1_bell_indentation integer,
    entrance1_steps1_number_of integer,
    entrance1_steps1_height integer,
    entrance1_steps1_depth integer,
    entrance1_steps2_number_of integer,
    entrance1_steps2_height integer,
    entrance1_steps2_depth integer,
    entrance1_contrast_marking_is_glass_surfaces boolean,
    entrance1_steps_is_contrast_marked boolean,
    entrance1_door1_mainpanel_width integer,
    entrance1_door1_sidepanel_width integer,
    entrance1_door1_type_id integer,
    entrance1_door1_opening_id integer,
    entrance1_door1_opening_direction_id integer,
    entrance1_door1_step_height integer,
    entrance1_door2_mainpanel_width integer,
    entrance1_door2_sidepanel_width integer,
    entrance1_door2_type_id integer,
    entrance1_door2_opening_id integer,
    entrance1_door2_opening_direction_id integer,
    entrance1_door2_step_height integer,
    entrance2_is_side_entrance_marked boolean,
    entrance2_is_side_entrance_information boolean,
    entrance2_access_id integer,
    entrance2_is_reserved_parking boolean,
    entrance2_number_of_reserved_parking integer,
    entrance2_is_longitudinal_inclination boolean,
    entrance2_longitudinal_inclination double precision,
    entrance2_is_transverse_inclination boolean,
    entrance2_transverse_inclination double precision,
    entrance2_is_difficult_surface boolean,
    entrance2_guidingline_id integer,
    entrance2_accessibility_id integer,
    entrance2_area_before_door_width integer,
    entrance2_area_before_door_depth integer,
    entrance2_lobby_width integer,
    entrance2_lobby_depth integer,
    entrance2_is_a_o_b boolean,
    entrance2_aob_is_above_door boolean,
    entrance2_bell_type_id integer,
    entrance2_bell_height integer,
    entrance2_bell_indentation integer,
    entrance2_steps1_number_of integer,
    entrance2_steps1_height integer,
    entrance2_steps1_depth integer,
    entrance2_steps2_number_of integer,
    entrance2_steps2_height integer,
    entrance2_steps2_depth integer,
    entrance2_contrast_marking_is_glass_surfaces boolean,
    entrance2_steps_is_contrast_marked boolean,
    entrance2_door1_mainpanel_width integer,
    entrance2_door1_sidepanel_width integer,
    entrance2_door1_type_id integer,
    entrance2_door1_opening_id integer,
    entrance2_door1_opening_direction_id integer,
    entrance2_door1_step_height integer,
    entrance2_door2_mainpanel_width integer,
    entrance2_door2_sidepanel_width integer,
    entrance2_door2_type_id integer,
    entrance2_door2_opening_id integer,
    entrance2_door2_opening_direction_id integer,
    entrance2_door2_step_height integer,
    object_is_steps boolean,
    object_steps1_number_of integer,
    object_steps1_height integer,
    object_steps1_depth integer,
    object_is_stairs boolean,
    object_stairs_type_id integer,
    object_stairs_width integer,
    object_stairs_is_bannister boolean,
    object_is_narrowed_passage boolean,
    object_narrowed_passage_width integer,
    object_is_tourniquet boolean,
    object_is_navigation_system boolean,
    object_interior_accessibility_id integer,
    object_contrast_marking_is_glass_surfaces boolean,
    object_steps_is_contrast_marked boolean,
    object_is_a_o_b boolean,
    object_aob_is_above_door boolean,
    source_id integer NOT NULL,
    external_data json,
    web_url character varying(255),
    region character varying(255),
    entrance1_contrast_marking_localization_id integer,
    entrance2_contrast_marking_localization_id integer,
    object_contrast_marking_localization_id integer,
    data_owner_url character varying(255)
);


ALTER TABLE map_object OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 312 (class 1259 OID 892232)
-- Name: map_object_draft; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE map_object_draft (
    id integer NOT NULL,
    data json,
    pair_key character varying(255),
    user_id integer,
    map_object_object_id integer
);


ALTER TABLE map_object_draft OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 311 (class 1259 OID 892230)
-- Name: map_object_draft_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE map_object_draft_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE map_object_draft_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3190 (class 0 OID 0)
-- Dependencies: 311
-- Name: map_object_draft_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE map_object_draft_id_seq OWNED BY map_object_draft.id;


--
-- TOC entry 185 (class 1259 OID 731280)
-- Name: map_object_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE map_object_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE map_object_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3191 (class 0 OID 0)
-- Dependencies: 185
-- Name: map_object_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE map_object_id_seq OWNED BY map_object.id;


--
-- TOC entry 187 (class 1259 OID 731323)
-- Name: map_object_lang; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE map_object_lang (
    map_object_id integer NOT NULL,
    lang_id character(2) NOT NULL,
    title character varying(255) NOT NULL,
    description text,
    object_type_custom character varying(255),
    entrance1_reserved_parking_localization character varying(255),
    entrance1_reserved_parking_access_description character varying(255),
    entrance1_longitudinal_inclination_localization character varying(255),
    entrance1_transverse_inclination_localization character varying(255),
    entrance1_difficult_surface_description character varying(255),
    entrance1_aob_localization character varying(255),
    entrance1_has_description text,
    entrance1_has_notes text,
    entrance2_localization character varying(255),
    entrance2_access_provided_by character varying(255),
    entrance2_reserved_parking_localization character varying(255),
    entrance2_reserved_parking_access_description character varying(255),
    entrance2_longitudinal_inclination_localization character varying(255),
    entrance2_transverse_inclination_localization character varying(255),
    entrance2_difficult_surface_description character varying(255),
    entrance2_aob_localization character varying(255),
    entrance2_has_description text,
    entrance2_has_notes text,
    object_steps1_localization character varying(255),
    object_narrowed_passage_localization character varying(255),
    object_tourniquet_localization character varying(255),
    object_navigation_system_description character varying(255),
    object_has_description text,
    object_has_notes text,
    object_aob_localization character varying(255),
    search_title character varying(255)
);


ALTER TABLE map_object_lang OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 241 (class 1259 OID 736205)
-- Name: mappable_entity_access; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE mappable_entity_access (
    id integer NOT NULL,
    title character varying(255),
    pair_key character varying(255)
);


ALTER TABLE mappable_entity_access OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 240 (class 1259 OID 736203)
-- Name: mappable_entity_access_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE mappable_entity_access_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE mappable_entity_access_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3192 (class 0 OID 0)
-- Dependencies: 240
-- Name: mappable_entity_access_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE mappable_entity_access_id_seq OWNED BY mappable_entity_access.id;


--
-- TOC entry 201 (class 1259 OID 736045)
-- Name: object_interior_accessibility; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE object_interior_accessibility (
    id integer NOT NULL,
    title character varying(255),
    pair_key character varying(255)
);


ALTER TABLE object_interior_accessibility OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 200 (class 1259 OID 736043)
-- Name: object_interior_accessibility_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE object_interior_accessibility_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE object_interior_accessibility_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3193 (class 0 OID 0)
-- Dependencies: 200
-- Name: object_interior_accessibility_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE object_interior_accessibility_id_seq OWNED BY object_interior_accessibility.id;


--
-- TOC entry 199 (class 1259 OID 736027)
-- Name: object_stairs_type; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE object_stairs_type (
    id integer NOT NULL,
    title character varying(255),
    pair_key character varying(255),
    combine_key json
);


ALTER TABLE object_stairs_type OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 198 (class 1259 OID 736025)
-- Name: object_stairs_type_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE object_stairs_type_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE object_stairs_type_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3194 (class 0 OID 0)
-- Dependencies: 198
-- Name: object_stairs_type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE object_stairs_type_id_seq OWNED BY object_stairs_type.id;


--
-- TOC entry 178 (class 1259 OID 731139)
-- Name: object_type; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE object_type (
    id integer NOT NULL,
    title character varying(255) NOT NULL,
    pair_key character varying(255),
    combine_key json
);


ALTER TABLE object_type OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 177 (class 1259 OID 731137)
-- Name: object_type_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE object_type_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE object_type_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3195 (class 0 OID 0)
-- Dependencies: 177
-- Name: object_type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE object_type_id_seq OWNED BY object_type.id;


--
-- TOC entry 193 (class 1259 OID 731370)
-- Name: platform; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE platform (
    id integer NOT NULL,
    map_object_id integer NOT NULL,
    platform_relation_id integer,
    platform_access_id integer,
    platform_type_id integer,
    platform_max_load integer,
    platform_width integer,
    platform_depth integer,
    platform_is_min_parameters boolean,
    platform_number_of_steps integer,
    platform_number_of_floors integer,
    platform_outside_bottom_control_height integer,
    platform_outside_top_control_height integer,
    platform_inside_control_height integer,
    entryarea1_entry_id integer,
    entryarea1_is_entry_closing boolean,
    entryarea1_entry_width integer,
    entryarea1_width integer,
    entryarea1_depth integer,
    entryarea1_height_elevation integer,
    entryarea1_bell_type_id integer,
    entryarea1_bell_height integer,
    entryarea1_bell_indentation integer,
    entryarea2_entry_id integer,
    entryarea2_is_entry_closing boolean,
    entryarea2_entry_width integer,
    entryarea2_width integer,
    entryarea2_depth integer,
    entryarea2_height_elevation integer,
    entryarea2_bell_type_id integer,
    entryarea2_bell_height integer,
    entryarea2_bell_indentation integer
);


ALTER TABLE platform OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 192 (class 1259 OID 731368)
-- Name: platform_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE platform_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE platform_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3196 (class 0 OID 0)
-- Dependencies: 192
-- Name: platform_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE platform_id_seq OWNED BY platform.id;


--
-- TOC entry 286 (class 1259 OID 737352)
-- Name: platform_lang; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE platform_lang (
    platform_id integer NOT NULL,
    lang_id character(2) NOT NULL,
    platform_localization character varying(255),
    platform_has_notes text,
    platform_has_description text
);


ALTER TABLE platform_lang OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 243 (class 1259 OID 736213)
-- Name: platform_type; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE platform_type (
    id integer NOT NULL,
    title character varying(255),
    pair_key character varying(255)
);


ALTER TABLE platform_type OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 242 (class 1259 OID 736211)
-- Name: platform_type_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE platform_type_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE platform_type_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3197 (class 0 OID 0)
-- Dependencies: 242
-- Name: platform_type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE platform_type_id_seq OWNED BY platform_type.id;


--
-- TOC entry 223 (class 1259 OID 736133)
-- Name: ramp_handle_localization; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE ramp_handle_localization (
    id integer NOT NULL,
    title character varying(255),
    pair_key character varying(255)
);


ALTER TABLE ramp_handle_localization OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 222 (class 1259 OID 736131)
-- Name: ramp_handle_localization_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE ramp_handle_localization_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE ramp_handle_localization_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3198 (class 0 OID 0)
-- Dependencies: 222
-- Name: ramp_handle_localization_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE ramp_handle_localization_id_seq OWNED BY ramp_handle_localization.id;


--
-- TOC entry 215 (class 1259 OID 736101)
-- Name: ramp_skids_localization; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE ramp_skids_localization (
    id integer NOT NULL,
    title character varying(255),
    pair_key character varying(255)
);


ALTER TABLE ramp_skids_localization OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 214 (class 1259 OID 736099)
-- Name: ramp_skids_localization_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE ramp_skids_localization_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE ramp_skids_localization_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3199 (class 0 OID 0)
-- Dependencies: 214
-- Name: ramp_skids_localization_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE ramp_skids_localization_id_seq OWNED BY ramp_skids_localization.id;


--
-- TOC entry 217 (class 1259 OID 736109)
-- Name: ramp_skids_mobility; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE ramp_skids_mobility (
    id integer NOT NULL,
    title character varying(255),
    pair_key character varying(255)
);


ALTER TABLE ramp_skids_mobility OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 216 (class 1259 OID 736107)
-- Name: ramp_skids_mobility_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE ramp_skids_mobility_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE ramp_skids_mobility_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3200 (class 0 OID 0)
-- Dependencies: 216
-- Name: ramp_skids_mobility_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE ramp_skids_mobility_id_seq OWNED BY ramp_skids_mobility.id;


--
-- TOC entry 221 (class 1259 OID 736125)
-- Name: ramp_surface; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE ramp_surface (
    id integer NOT NULL,
    title character varying(255),
    pair_key character varying(255)
);


ALTER TABLE ramp_surface OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 220 (class 1259 OID 736123)
-- Name: ramp_surface_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE ramp_surface_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE ramp_surface_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3201 (class 0 OID 0)
-- Dependencies: 220
-- Name: ramp_surface_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE ramp_surface_id_seq OWNED BY ramp_surface.id;


--
-- TOC entry 219 (class 1259 OID 736117)
-- Name: ramp_type; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE ramp_type (
    id integer NOT NULL,
    title character varying(255),
    pair_key character varying(255)
);


ALTER TABLE ramp_type OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 218 (class 1259 OID 736115)
-- Name: ramp_type_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE ramp_type_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE ramp_type_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3202 (class 0 OID 0)
-- Dependencies: 218
-- Name: ramp_type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE ramp_type_id_seq OWNED BY ramp_type.id;


--
-- TOC entry 191 (class 1259 OID 731352)
-- Name: rampskids; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE rampskids (
    id integer NOT NULL,
    map_object_id integer NOT NULL,
    ramp_relation_id integer,
    ramp_localization_id integer,
    ramp_mobility_id integer,
    ramp_type_id integer,
    ramp_number_of_legs integer,
    rampleg1_width integer,
    rampleg1_length integer,
    rampleg1_inclination double precision,
    rampleg2_width integer,
    rampleg2_length integer,
    rampleg2_inclination double precision,
    rampleg3_width integer,
    rampleg3_length integer,
    rampleg3_inclination double precision,
    rampleg4_width integer,
    rampleg4_length integer,
    rampleg4_inclination double precision,
    ramp_bottom_entryarea_width integer,
    ramp_bottom_entryarea_depth integer,
    ramp_top_entryarea_width integer,
    ramp_top_entryarea_depth integer,
    ramp_landings_entryarea_width integer,
    ramp_landings_entryarea_depth integer,
    ramp_surface_id integer,
    ramp_is_handle boolean,
    ramp_handle_orientation_id integer,
    ramp_handle1_height integer,
    skids_localization_id integer,
    skids_mobility_id integer,
    skids_inclination double precision,
    skids_length integer,
    ramp_handle2_height integer
);


ALTER TABLE rampskids OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 190 (class 1259 OID 731350)
-- Name: rampskids_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE rampskids_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE rampskids_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3203 (class 0 OID 0)
-- Dependencies: 190
-- Name: rampskids_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE rampskids_id_seq OWNED BY rampskids.id;


--
-- TOC entry 287 (class 1259 OID 737370)
-- Name: rampskids_lang; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE rampskids_lang (
    rampskids_id integer NOT NULL,
    lang_id character(2) NOT NULL,
    ramp_interior_localization character varying(255),
    ramp_access_provided_by character varying(255),
    skids_interior_localization character varying(255),
    ramp_skids_has_notes text,
    ramp_skids_has_description text
);


ALTER TABLE rampskids_lang OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 189 (class 1259 OID 731344)
-- Name: rampskids_platform_relation; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE rampskids_platform_relation (
    id integer NOT NULL,
    title character varying(255) NOT NULL,
    pair_key character varying(255)
);


ALTER TABLE rampskids_platform_relation OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 188 (class 1259 OID 731342)
-- Name: rampskids_platform_relation_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE rampskids_platform_relation_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE rampskids_platform_relation_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3204 (class 0 OID 0)
-- Dependencies: 188
-- Name: rampskids_platform_relation_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE rampskids_platform_relation_id_seq OWNED BY rampskids_platform_relation.id;


--
-- TOC entry 182 (class 1259 OID 731155)
-- Name: role; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE role (
    id integer NOT NULL,
    title character varying(255) NOT NULL,
    pair_key character varying(255)
);


ALTER TABLE role OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 181 (class 1259 OID 731153)
-- Name: role_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE role_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE role_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3205 (class 0 OID 0)
-- Dependencies: 181
-- Name: role_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE role_id_seq OWNED BY role.id;


--
-- TOC entry 294 (class 1259 OID 807245)
-- Name: ruian; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE ruian (
    id integer NOT NULL,
    zipcode character varying(255) NOT NULL,
    city character varying(255) NOT NULL,
    city_momc character varying(255),
    city_part character varying(255),
    street character varying(255),
    street_desc_no integer,
    street_no_is_alternative boolean NOT NULL DEFAULT FALSE,
    street_orient_no integer,
    street_orient_symbol character varying(255)
);


ALTER TABLE ruian OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 337 (class 1259 OID 943677)
-- Name: ruian_city; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE ruian_city (
    zipcode character varying(255) NOT NULL,
    city character varying(255) NOT NULL,
    city_part character varying(255) NOT NULL,
    search_city character varying(255),
    search_city_part character varying(255)
);


ALTER TABLE ruian_city OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 273 (class 1259 OID 736333)
-- Name: tap_type; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE tap_type (
    id integer NOT NULL,
    title character varying(255),
    pair_key character varying(255)
);


ALTER TABLE tap_type OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 272 (class 1259 OID 736331)
-- Name: tap_type_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE tap_type_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE tap_type_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3206 (class 0 OID 0)
-- Dependencies: 272
-- Name: tap_type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE tap_type_id_seq OWNED BY tap_type.id;


--
-- TOC entry 184 (class 1259 OID 731163)
-- Name: user; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE "user" (
    id integer NOT NULL,
    email character varying(255) NOT NULL,
    login character varying(255) NOT NULL,
    password character varying(256),
    invalid_login_count integer DEFAULT 0,
    last_invalid_login_timestamp timestamp without time zone,
    password_reset_token character varying(256),
    password_reset_token_time timestamp without time zone,
    certified boolean DEFAULT false NOT NULL,
    firstname character varying(255),
    surname character varying(255),
    phone character varying(255),
    city character varying(255),
    ic character varying(255),
    ic_title character varying(255),
    ic_place character varying(255),
    ic_form character varying(255),
    role_id integer NOT NULL,
    parent_id integer,
    license_id integer,
    CONSTRAINT user_email_check CHECK (((email)::text = btrim(lower((email)::text)))),
    CONSTRAINT user_login_check CHECK (((login)::text = btrim(lower((login)::text))))
);


ALTER TABLE "user" OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 183 (class 1259 OID 731161)
-- Name: user_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE user_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE user_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3207 (class 0 OID 0)
-- Dependencies: 183
-- Name: user_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE user_id_seq OWNED BY "user".id;


--
-- TOC entry 306 (class 1259 OID 881082)
-- Name: v_a_o_b_announcement; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_a_o_b_announcement AS
 SELECT i.id,
    o.title
   FROM (a_o_b_announcement o
     JOIN ( SELECT a_o_b_announcement.id,
            (json_array_elements_text(COALESCE(a_o_b_announcement.combine_key, to_json(ARRAY[a_o_b_announcement.id]))))::integer AS single_id
           FROM a_o_b_announcement) i ON ((o.id = i.single_id)));


ALTER TABLE v_a_o_b_announcement OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 338 (class 1259 OID 992840)
-- Name: v_aob; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_aob AS
 SELECT (10000000 + o.object_id) AS id,
    NULL::integer AS aob_announcements_scheme_id,
    lower((o.entrance1_aob_is_above_door)::text) AS aob_is_above_door,
    cs.entrance1_aob_localization AS cs_aob_localization,
    en.entrance1_aob_localization AS en_aob_localization
   FROM ((map_object o
     LEFT JOIN map_object_lang cs ON (((o.id = cs.map_object_id) AND (cs.lang_id = 'cs'::bpchar))))
     LEFT JOIN map_object_lang en ON (((o.id = en.map_object_id) AND (en.lang_id = 'en'::bpchar))))
  WHERE o.entrance1_is_a_o_b
UNION ALL
 SELECT (20000000 + o.object_id) AS id,
    NULL::integer AS aob_announcements_scheme_id,
    lower((o.entrance2_aob_is_above_door)::text) AS aob_is_above_door,
    cs.entrance2_aob_localization AS cs_aob_localization,
    en.entrance2_aob_localization AS en_aob_localization
   FROM ((map_object o
     LEFT JOIN map_object_lang cs ON (((o.id = cs.map_object_id) AND (cs.lang_id = 'cs'::bpchar))))
     LEFT JOIN map_object_lang en ON (((o.id = en.map_object_id) AND (en.lang_id = 'en'::bpchar))))
  WHERE o.entrance2_is_a_o_b
UNION ALL
 SELECT (30000000 + o.object_id) AS id,
    NULL::integer AS aob_announcements_scheme_id,
    lower((o.object_aob_is_above_door)::text) AS aob_is_above_door,
    cs.object_aob_localization AS cs_aob_localization,
    en.object_aob_localization AS en_aob_localization
   FROM ((map_object o
     LEFT JOIN map_object_lang cs ON (((o.id = cs.map_object_id) AND (cs.lang_id = 'cs'::bpchar))))
     LEFT JOIN map_object_lang en ON (((o.id = en.map_object_id) AND (en.lang_id = 'en'::bpchar))))
  WHERE o.object_is_a_o_b
UNION ALL
 SELECT ((40000000 + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS id,
    a.elevator_a_o_b_announcements_scheme_id AS aob_announcements_scheme_id,
    lower((a.elevator_aob_is_above_door)::text) AS aob_is_above_door,
    cs.elevator_aob_localization AS cs_aob_localization,
    en.elevator_aob_localization AS en_aob_localization
   FROM (((elevator a
     JOIN map_object o ON ((a.map_object_id = o.id)))
     LEFT JOIN elevator_lang cs ON (((a.id = cs.elevator_id) AND (cs.lang_id = 'cs'::bpchar))))
     LEFT JOIN elevator_lang en ON (((a.id = en.elevator_id) AND (en.lang_id = 'en'::bpchar))))
  WHERE a.elevator_is_a_o_b;


ALTER TABLE v_aob OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 313 (class 1259 OID 941081)
-- Name: v_bell; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_bell AS
 SELECT (((100000000 + 50000000) + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS id,
    a.entryarea1_bell_type_id AS bell_type_id,
    a.entryarea1_bell_height AS bell_height,
    a.entryarea1_bell_indentation AS bell_indentation
   FROM (platform a
     JOIN map_object o ON ((a.map_object_id = o.id)))
  WHERE (a.entryarea1_bell_type_id IS NOT NULL)
UNION ALL
 SELECT (((200000000 + 50000000) + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS id,
    a.entryarea2_bell_type_id AS bell_type_id,
    a.entryarea2_bell_height AS bell_height,
    a.entryarea2_bell_indentation AS bell_indentation
   FROM (platform a
     JOIN map_object o ON ((a.map_object_id = o.id)))
  WHERE (a.entryarea2_bell_type_id IS NOT NULL)
UNION ALL
 SELECT (10000000 + o.object_id) AS id,
    o.entrance1_bell_type_id AS bell_type_id,
    o.entrance1_bell_height AS bell_height,
    o.entrance1_bell_indentation AS bell_indentation
   FROM map_object o
  WHERE (o.entrance1_bell_type_id IS NOT NULL)
UNION ALL
 SELECT (20000000 + o.object_id) AS id,
    o.entrance2_bell_type_id AS bell_type_id,
    o.entrance2_bell_height AS bell_height,
    o.entrance2_bell_indentation AS bell_indentation
   FROM map_object o
  WHERE (o.entrance2_bell_type_id IS NOT NULL);


ALTER TABLE v_bell OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 339 (class 1259 OID 992845)
-- Name: v_cm; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_cm AS
 SELECT (10000000 + o.object_id) AS id,
    lower((o.entrance1_contrast_marking_is_glass_surfaces)::text) AS contrast_marking_is_glass_surfaces,
    o.entrance1_contrast_marking_localization_id AS contrast_marking_localization_id
   FROM map_object o
  WHERE o.entrance1_contrast_marking_is_glass_surfaces
UNION ALL
 SELECT (20000000 + o.object_id) AS id,
    lower((o.entrance2_contrast_marking_is_glass_surfaces)::text) AS contrast_marking_is_glass_surfaces,
    o.entrance2_contrast_marking_localization_id AS contrast_marking_localization_id
   FROM map_object o
  WHERE o.entrance2_contrast_marking_is_glass_surfaces
UNION ALL
 SELECT (30000000 + o.object_id) AS id,
    lower((o.object_contrast_marking_is_glass_surfaces)::text) AS contrast_marking_is_glass_surfaces,
    o.object_contrast_marking_localization_id AS contrast_marking_localization_id
   FROM map_object o
  WHERE o.object_contrast_marking_is_glass_surfaces;


ALTER TABLE v_cm OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3208 (class 0 OID 0)
-- Dependencies: 339
-- Name: VIEW v_cm; Type: COMMENT; Schema: public; Owner: mapy_pristupnosti_db_01
--

COMMENT ON VIEW v_cm IS 'v_contrast_marking pro object, entrance_main, enstrance_side';


--
-- TOC entry 310 (class 1259 OID 881443)
-- Name: v_cml; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_cml AS
 SELECT i.id,
    o.title
   FROM (contrast_marking_localization o
     JOIN ( SELECT contrast_marking_localization.id,
            (json_array_elements_text(COALESCE(contrast_marking_localization.combine_key, to_json(ARRAY[contrast_marking_localization.id]))))::integer AS single_id
           FROM contrast_marking_localization) i ON ((o.id = i.single_id)));


ALTER TABLE v_cml OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3209 (class 0 OID 0)
-- Dependencies: 310
-- Name: VIEW v_cml; Type: COMMENT; Schema: public; Owner: mapy_pristupnosti_db_01
--

COMMENT ON VIEW v_cml IS 'v_contrast_marking_localization';


--
-- TOC entry 314 (class 1259 OID 941117)
-- Name: v_door_entrance; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_door_entrance AS
 SELECT (10000000 + o.object_id) AS id,
    (110000000 + o.object_id) AS door_id,
    o.entrance1_door1_mainpanel_width AS mainpanel_width,
    o.entrance1_door1_sidepanel_width AS sidepanel_width,
    o.entrance1_door1_type_id AS type_id,
    o.entrance1_door1_opening_id AS opening_id,
    o.entrance1_door1_opening_direction_id AS opening_direction_id,
    o.entrance1_door1_step_height AS step_height
   FROM map_object o
  WHERE (o.entrance1_door1_type_id IS NOT NULL)
UNION ALL
 SELECT (20000000 + o.object_id) AS id,
    (120000000 + o.object_id) AS door_id,
    o.entrance2_door1_mainpanel_width AS mainpanel_width,
    o.entrance2_door1_sidepanel_width AS sidepanel_width,
    o.entrance2_door1_type_id AS type_id,
    o.entrance2_door1_opening_id AS opening_id,
    o.entrance2_door1_opening_direction_id AS opening_direction_id,
    o.entrance2_door1_step_height AS step_height
   FROM map_object o
  WHERE (o.entrance2_door1_type_id IS NOT NULL);


ALTER TABLE v_door_entrance OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 197 (class 1259 OID 731401)
-- Name: wc; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE wc (
    id integer NOT NULL,
    map_object_id integer NOT NULL,
    wc_accessibility_id integer,
    wc_cabin_access_id integer,
    wc_cabin_localization_id integer,
    wc_switch_id integer,
    wc_switch_height integer,
    wc_cabin_width integer,
    wc_cabin_depth integer,
    wc_flushing_back_height integer,
    wc_flushing_side_height integer,
    wc_flushing_side_distanc integer,
    wc_flushing_id integer,
    wc_flushing_difficulty_id integer,
    wc_handles_distance integer,
    wc_basin_left_distance integer,
    wc_basin_right_distance integer,
    wc_basin_back_indentation integer,
    wc_basin_seat_height integer,
    wc_basin_is_paper_reach boolean,
    wc_basin_space_id integer,
    wc_is_changingdesk boolean,
    wc_is_changingdesk_obstructs boolean,
    wc_changingdesk_id integer,
    wc_is_alarmbutton boolean,
    wc_alarmbutton_top_heigh integer,
    wc_alarmbutton_bottom_height integer,
    wc_is_regular_w_c boolean,
    wc_is_regular_w_c_braille_marking boolean,
    wc_cabin_door_disposition_id integer,
    wc_cabin_w_c_basin_disposition_id integer,
    wc_cabin_wash_basin_disposition_id integer,
    hallway1_width integer,
    hallway1_depth integer,
    hallway1_door_width integer,
    hallway1_door_marking_id integer,
    hallway2_width integer,
    hallway2_depth integer,
    hallway2_door_width integer,
    hallway2_door_marking_id integer,
    handle1_type_id integer,
    handle1_height integer,
    handle1_length integer,
    handle2_type_id integer,
    handle2_height integer,
    handle2_length integer,
    door_width integer,
    door_opening_direction_id integer,
    door_is_marking boolean,
    door_handle_position_id integer,
    washbasin_height integer,
    washbasin_underpass_id integer,
    washbasin_is_handle boolean,
    tap_height integer,
    tap_type_id integer,
    washbasin_handle_type_id integer,
    washbasin_handle_height integer,
    washbasin_handle_length integer
);


ALTER TABLE wc OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 340 (class 1259 OID 992850)
-- Name: v_door_hallway; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_door_hallway AS
 SELECT (((100000000 + 70000000) + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS id,
    a.hallway1_door_width AS hallway_door_width,
    lower(((COALESCE(a.hallway1_door_marking_id, 0) <> 2))::text) AS hallway_door_is_marking,
    lower(((COALESCE(a.hallway1_door_marking_id, 0) = 3))::text) AS hallway_door_is_braille_marking
   FROM (wc a
     JOIN map_object o ON ((a.map_object_id = o.id)))
  WHERE (a.hallway1_door_width IS NOT NULL)
UNION ALL
 SELECT (((200000000 + 70000000) + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS id,
    a.hallway2_door_width AS hallway_door_width,
    lower(((COALESCE(a.hallway2_door_marking_id, 0) <> 2))::text) AS hallway_door_is_marking,
    lower(((COALESCE(a.hallway2_door_marking_id, 0) = 3))::text) AS hallway_door_is_braille_marking
   FROM (wc a
     JOIN map_object o ON ((a.map_object_id = o.id)))
  WHERE (a.hallway2_door_width IS NOT NULL);


ALTER TABLE v_door_hallway OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 315 (class 1259 OID 941122)
-- Name: v_door_lobby; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_door_lobby AS
 SELECT (10000000 + o.object_id) AS id,
    (210000000 + o.object_id) AS door_id,
    o.entrance1_door2_mainpanel_width AS mainpanel_width,
    o.entrance1_door2_sidepanel_width AS sidepanel_width,
    o.entrance1_door2_type_id AS type_id,
    o.entrance1_door2_opening_id AS opening_id,
    o.entrance1_door2_opening_direction_id AS opening_direction_id,
    o.entrance1_door2_step_height AS step_height
   FROM map_object o
  WHERE (o.entrance1_door2_type_id IS NOT NULL)
UNION ALL
 SELECT (20000000 + o.object_id) AS id,
    (220000000 + o.object_id) AS door_id,
    o.entrance2_door2_mainpanel_width AS mainpanel_width,
    o.entrance2_door2_sidepanel_width AS sidepanel_width,
    o.entrance2_door2_type_id AS type_id,
    o.entrance2_door2_opening_id AS opening_id,
    o.entrance2_door2_opening_direction_id AS opening_direction_id,
    o.entrance2_door2_step_height AS step_height
   FROM map_object o
  WHERE (o.entrance2_door2_type_id IS NOT NULL);


ALTER TABLE v_door_lobby OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 341 (class 1259 OID 992855)
-- Name: v_door_wc; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_door_wc AS
 SELECT ((70000000 + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS id,
    a.door_width,
    a.door_opening_direction_id,
    lower((a.door_is_marking)::text) AS door_is_marking
   FROM (wc a
     JOIN map_object o ON ((a.map_object_id = o.id)))
  WHERE (a.door_width IS NOT NULL);


ALTER TABLE v_door_wc OWNER TO mapy_pristupnosti_db_01;

--
-- Name: v_elevator_control_flat_marking; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_elevator_control_flat_marking AS
 SELECT i.id,
    o.title
   FROM (elevator_control_flat_marking o
     JOIN ( SELECT elevator_control_flat_marking.id,
            (json_array_elements_text(COALESCE(elevator_control_flat_marking.combine_key, to_json(ARRAY[elevator_control_flat_marking.id]))))::integer AS single_id
           FROM elevator_control_flat_marking) i ON ((o.id = i.single_id)));


ALTER TABLE v_elevator_control_flat_marking OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 342 (class 1259 OID 992860)
-- Name: v_elevator; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_elevator AS
 SELECT (30000000 + o.object_id) AS id,
    ((40000000 + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS elevator_id,
    a.elevator_access_id,
    a.elevator_type_id,
    a.elevator_driveoff_id,
    a.elevator_control1_max_height,
    a.elevator_control1_relief_marking_id,
    a.elevator_control1_flat_marking_id,
    lower((a.elevator_is_control1_braille_marking)::text) AS elevator_is_control1_braille_marking,
    lower((a.elevator_is_a_o_b)::text) AS elevator_is_a_o_b,
    lower((a.elevator_is_cage_passthrough)::text) AS elevator_is_cage_passthrough,
    a.elevator_cage_width,
    a.elevator_cage_depth,
    a.elevator_cage_seconddoor_localization_id,
    a.elevator_cage_control_distance,
    a.elevator_cage_control_height,
    a.elevator_control2_relief_marking_id,
    a.elevator_control2_flat_marking_id,
    lower((a.elevator_is_control2_braille_marking)::text) AS elevator_is_control2_braille_marking,
    lower((a.elevator_is_cage_control_announcement_acoustic)::text) AS elevator_is_cage_control_announcement_acoustic,
    lower((a.elevator_is_cage_control_announcement_phonetic)::text) AS elevator_is_cage_control_announcement_phonetic,
    lower((a.elevator_is_cage_handle)::text) AS elevator_is_cage_handle,
    lower((a.elevator_is_cage_mirror)::text) AS elevator_is_cage_mirror,
    a.elevator_cage_mirror_localization_id,
    a.elevator_cage_mirror_height,
    lower((a.elevator_is_cage_seat)::text) AS elevator_is_cage_seat,
    lower((a.elevator_is_cage_seat_withinreach)::text) AS elevator_is_cage_seat_withinreach,
    lower((a.elevator_is_cage_seat_functional)::text) AS elevator_is_cage_seat_functional,
    cs.elevator_localization AS cs_elevator_localization,
    cs.elevator_access_provided_by AS cs_elevator_access_provided_by,
    cs.elevator_connects_floors AS cs_elevator_connects_floors,
    cs.elevator_has_notes AS cs_elevator_has_notes,
    cs.elevator_has_description AS cs_elevator_has_description,
    en.elevator_localization AS en_elevator_localization,
    en.elevator_access_provided_by AS en_elevator_access_provided_by,
    en.elevator_connects_floors AS en_elevator_connects_floors,
    en.elevator_has_notes AS en_elevator_has_notes,
    en.elevator_has_description AS en_elevator_has_description
   FROM (((elevator a
     JOIN map_object o ON ((a.map_object_id = o.id)))
     LEFT JOIN elevator_lang cs ON (((a.id = cs.elevator_id) AND (cs.lang_id = 'cs'::bpchar))))
     LEFT JOIN elevator_lang en ON (((a.id = en.elevator_id) AND (en.lang_id = 'en'::bpchar))));


ALTER TABLE v_elevator OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 319 (class 1259 OID 941148)
-- Name: v_elevator_cage_door; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_elevator_cage_door AS
 SELECT ((40000000 + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS id,
    (((200000000 + 40000000) + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS door_id,
    a.door2_width AS door_width,
    a.door2_opening_id AS door_opening_id
   FROM (elevator a
     JOIN map_object o ON ((a.map_object_id = o.id)))
  WHERE (a.door2_opening_id IS NOT NULL);


ALTER TABLE v_elevator_cage_door OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 317 (class 1259 OID 941138)
-- Name: v_elevator_cage_handle; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_elevator_cage_handle AS
 SELECT ((40000000 + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS id,
    a.elevator_handle_localization_id
   FROM (elevator a
     JOIN map_object o ON ((a.map_object_id = o.id)))
  WHERE (a.elevator_handle_localization_id IS NOT NULL);


ALTER TABLE v_elevator_cage_handle OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 316 (class 1259 OID 941132)
-- Name: v_elevator_entryarea; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_elevator_entryarea AS
 SELECT ((40000000 + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS id,
    a.entryarea_width,
    a.entryarea_depth,
    a.entryarea_height_elevation
   FROM (elevator a
     JOIN map_object o ON ((a.map_object_id = o.id)))
  WHERE (a.entryarea_width IS NOT NULL);


ALTER TABLE v_elevator_entryarea OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 318 (class 1259 OID 941143)
-- Name: v_elevator_shaft_door; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_elevator_shaft_door AS
 SELECT ((40000000 + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS id,
    (((100000000 + 40000000) + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS door_id,
    a.door1_width AS door_width,
    a.door1_opening_id AS door_opening_id
   FROM (elevator a
     JOIN map_object o ON ((a.map_object_id = o.id)))
  WHERE (a.door1_opening_id IS NOT NULL);


ALTER TABLE v_elevator_shaft_door OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 307 (class 1259 OID 881092)
-- Name: v_entrance_guidingline; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_entrance_guidingline AS
 SELECT i.id,
    o.title
   FROM (entrance_guidingline o
     JOIN ( SELECT entrance_guidingline.id,
            (json_array_elements_text(COALESCE(entrance_guidingline.combine_key, to_json(ARRAY[entrance_guidingline.id]))))::integer AS single_id
           FROM entrance_guidingline) i ON ((o.id = i.single_id)));


ALTER TABLE v_entrance_guidingline OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 343 (class 1259 OID 992865)
-- Name: v_entrance_main; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_entrance_main AS
 SELECT o.id,
    (10000000 + o.object_id) AS entrance_id,
    lower((o.entrance1_is_reserved_parking)::text) AS is_reserved_parking,
    o.entrance1_number_of_reserved_parking AS number_of_reserved_parking,
    o.entrance1_guidingline_id AS guidingline_id,
    o.entrance1_accessibility_id AS accessibility_id,
    lower((o.entrance1_is_longitudinal_inclination)::text) AS is_longitudinal_inclination,
    o.entrance1_longitudinal_inclination AS longitudinal_inclination,
    lower((o.entrance1_is_transverse_inclination)::text) AS is_transverse_inclination,
    o.entrance1_transverse_inclination AS transverse_inclination,
    lower((o.entrance1_is_difficult_surface)::text) AS is_difficult_surface,
    o.entrance1_area_before_door_width AS area_before_door_width,
    o.entrance1_area_before_door_depth AS area_before_door_depth,
    o.entrance1_lobby_width AS lobby_width,
    o.entrance1_lobby_depth AS lobby_depth,
    lower((o.entrance1_is_a_o_b)::text) AS is_a_o_b,
    cs.entrance1_reserved_parking_localization AS cs_reserved_parking_localization,
    cs.entrance1_reserved_parking_access_description AS cs_reserved_parking_access_description,
    cs.entrance1_longitudinal_inclination_localization AS cs_longitudinal_inclination_localization,
    cs.entrance1_transverse_inclination_localization AS cs_transverse_inclination_localization,
    cs.entrance1_difficult_surface_description AS cs_difficult_surface_description,
    cs.entrance1_has_description AS cs_has_description,
    cs.entrance1_has_notes AS cs_has_notes,
    en.entrance1_reserved_parking_localization AS en_reserved_parking_localization,
    en.entrance1_reserved_parking_access_description AS en_reserved_parking_access_description,
    en.entrance1_longitudinal_inclination_localization AS en_longitudinal_inclination_localization,
    en.entrance1_transverse_inclination_localization AS en_transverse_inclination_localization,
    en.entrance1_difficult_surface_description AS en_difficult_surface_description,
    en.entrance1_has_description AS en_has_description,
    en.entrance1_has_notes AS en_has_notes
   FROM ((map_object o
     LEFT JOIN map_object_lang cs ON (((o.id = cs.map_object_id) AND (cs.lang_id = 'cs'::bpchar))))
     LEFT JOIN map_object_lang en ON (((o.id = en.map_object_id) AND (en.lang_id = 'en'::bpchar))))
  WHERE (o.entrance1_accessibility_id IS NOT NULL);


ALTER TABLE v_entrance_main OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 344 (class 1259 OID 992870)
-- Name: v_entrance_side; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_entrance_side AS
 SELECT o.id,
    (20000000 + o.object_id) AS entrance_id,
    lower((o.entrance2_is_reserved_parking)::text) AS is_reserved_parking,
    o.entrance2_number_of_reserved_parking AS number_of_reserved_parking,
    o.entrance2_guidingline_id AS guidingline_id,
    o.entrance2_accessibility_id AS accessibility_id,
    lower((o.entrance2_is_side_entrance_marked)::text) AS is_side_entrance_marked,
    lower((o.entrance2_is_side_entrance_information)::text) AS is_side_entrance_information,
    o.entrance2_access_id AS access_id,
    cs.entrance2_access_provided_by AS cs_access_provided_by,
    en.entrance2_access_provided_by AS en_access_provided_by,
    lower((o.entrance2_is_longitudinal_inclination)::text) AS is_longitudinal_inclination,
    o.entrance2_longitudinal_inclination AS longitudinal_inclination,
    lower((o.entrance2_is_transverse_inclination)::text) AS is_transverse_inclination,
    o.entrance2_transverse_inclination AS transverse_inclination,
    lower((o.entrance2_is_difficult_surface)::text) AS is_difficult_surface,
    o.entrance2_area_before_door_width AS area_before_door_width,
    o.entrance2_area_before_door_depth AS area_before_door_depth,
    o.entrance2_lobby_width AS lobby_width,
    o.entrance2_lobby_depth AS lobby_depth,
    lower((o.entrance2_is_a_o_b)::text) AS is_a_o_b,
    cs.entrance2_reserved_parking_localization AS cs_reserved_parking_localization,
    cs.entrance2_reserved_parking_access_description AS cs_reserved_parking_access_description,
    cs.entrance2_longitudinal_inclination_localization AS cs_longitudinal_inclination_localization,
    cs.entrance2_transverse_inclination_localization AS cs_transverse_inclination_localization,
    cs.entrance2_difficult_surface_description AS cs_difficult_surface_description,
    cs.entrance2_has_description AS cs_has_description,
    cs.entrance2_has_notes AS cs_has_notes,
    en.entrance2_reserved_parking_localization AS en_reserved_parking_localization,
    en.entrance2_reserved_parking_access_description AS en_reserved_parking_access_description,
    en.entrance2_longitudinal_inclination_localization AS en_longitudinal_inclination_localization,
    en.entrance2_transverse_inclination_localization AS en_transverse_inclination_localization,
    en.entrance2_difficult_surface_description AS en_difficult_surface_description,
    en.entrance2_has_description AS en_has_description,
    en.entrance2_has_notes AS en_has_notes
   FROM ((map_object o
     LEFT JOIN map_object_lang cs ON (((o.id = cs.map_object_id) AND (cs.lang_id = 'cs'::bpchar))))
     LEFT JOIN map_object_lang en ON (((o.id = en.map_object_id) AND (en.lang_id = 'en'::bpchar))))
  WHERE (o.entrance2_accessibility_id IS NOT NULL);


ALTER TABLE v_entrance_side OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 353 (class 1259 OID 992919)
-- Name: v_object; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_object AS
 SELECT o.id,
    o.object_id,
    (30000000 + o.object_id) AS pair_id,
    o.user_id,
    o.ruian_address,
    o.web_url,
    lower((o.certified)::text) AS certified,
    lower((((o.mapping_date + '10 years'::interval) > ('now'::text)::date))::text) AS up_to_date,
    (o.mapping_date)::date AS mapping_date,
    o.object_type_id,
    o.accessibility_id,
    lower((o.object_is_steps)::text) AS object_is_steps,
    lower((o.object_is_stairs)::text) AS object_is_stairs,
    o.object_stairs_type_id,
    o.object_stairs_width,
    lower((o.object_stairs_is_bannister)::text) AS object_stairs_is_bannister,
    lower((o.object_is_narrowed_passage)::text) AS object_is_narrowed_passage,
    o.object_narrowed_passage_width,
    lower((o.object_is_tourniquet)::text) AS object_is_tourniquet,
    lower((o.object_is_navigation_system)::text) AS object_is_navigation_system,
    o.object_interior_accessibility_id,
    lower((o.object_steps_is_contrast_marked)::text) AS object_steps_is_contrast_marked,
    cs.title AS cs_title,
    en.title AS en_title,
    cs.description AS cs_description,
    en.description AS en_description,
    cs.object_narrowed_passage_localization AS cs_object_narrowed_passage_localization,
    cs.object_tourniquet_localization AS cs_object_tourniquet_localization,
    cs.object_navigation_system_description AS cs_object_navigation_system_description,
    cs.object_has_description AS cs_object_has_description,
    cs.object_has_notes AS cs_object_has_notes,
    en.object_narrowed_passage_localization AS en_object_narrowed_passage_localization,
    en.object_tourniquet_localization AS en_object_tourniquet_localization,
    en.object_navigation_system_description AS en_object_navigation_system_description,
    en.object_has_description AS en_object_has_description,
    en.object_has_notes AS en_object_has_notes,
    license.title AS license_title,
    exchange_source.title AS source_title
   FROM ((((map_object o
     LEFT JOIN exchange_source ON ((o.source_id = exchange_source.id)))
     LEFT JOIN license ON ((o.license_id = license.id)))
     LEFT JOIN map_object_lang cs ON (((o.id = cs.map_object_id) AND (cs.lang_id = 'cs'::bpchar))))
     LEFT JOIN map_object_lang en ON (((o.id = en.map_object_id) AND (en.lang_id = 'en'::bpchar))));


ALTER TABLE v_object OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 305 (class 1259 OID 881078)
-- Name: v_object_stairs_type; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_object_stairs_type AS
 SELECT i.id,
    o.title
   FROM (object_stairs_type o
     JOIN ( SELECT object_stairs_type.id,
            (json_array_elements_text(COALESCE(object_stairs_type.combine_key, to_json(ARRAY[object_stairs_type.id]))))::integer AS single_id
           FROM object_stairs_type) i ON ((o.id = i.single_id)));


ALTER TABLE v_object_stairs_type OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 304 (class 1259 OID 878863)
-- Name: v_object_type; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_object_type AS
 SELECT i.id,
    o.title
   FROM (object_type o
     JOIN ( SELECT object_type.id,
            (json_array_elements_text(COALESCE(object_type.combine_key, to_json(ARRAY[object_type.id]))))::integer AS single_id
           FROM object_type) i ON ((o.id = i.single_id)));


ALTER TABLE v_object_type OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 345 (class 1259 OID 992880)
-- Name: v_platform; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_platform AS
 SELECT ((COALESCE(a.platform_relation_id, 3) * 10000000) + o.object_id) AS id,
    ((50000000 + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS platform_id,
    a.platform_access_id,
    a.platform_type_id,
    a.platform_max_load,
    a.platform_width,
    a.platform_depth,
    lower((a.platform_is_min_parameters)::text) AS platform_is_min_parameters,
    a.platform_number_of_steps,
    a.platform_number_of_floors,
    a.platform_outside_bottom_control_height,
    a.platform_outside_top_control_height,
    a.platform_inside_control_height,
    cs.platform_localization AS cs_platform_localization,
    en.platform_localization AS en_platform_localization,
    cs.platform_has_notes AS cs_platform_has_notes,
    en.platform_has_notes AS en_platform_has_notes,
    cs.platform_has_description AS cs_platform_has_description,
    en.platform_has_description AS en_platform_has_description
   FROM (((platform a
     JOIN map_object o ON ((a.map_object_id = o.id)))
     LEFT JOIN platform_lang cs ON (((a.id = cs.platform_id) AND (cs.lang_id = 'cs'::bpchar))))
     LEFT JOIN platform_lang en ON (((a.id = en.platform_id) AND (en.lang_id = 'en'::bpchar))));


ALTER TABLE v_platform OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 346 (class 1259 OID 992885)
-- Name: v_platform_entryarea_bottom; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_platform_entryarea_bottom AS
 SELECT ((50000000 + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS id,
    (((100000000 + 50000000) + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS platform_entryarea_id,
    a.entryarea1_entry_id AS entry_id,
    lower((a.entryarea1_is_entry_closing)::text) AS is_entry_closing,
    a.entryarea1_entry_width AS entry_width,
    a.entryarea1_width AS width,
    a.entryarea1_depth AS depth,
    a.entryarea1_height_elevation AS height_elevation
   FROM (platform a
     JOIN map_object o ON ((a.map_object_id = o.id)))
  WHERE (a.entryarea1_entry_id IS NOT NULL);


ALTER TABLE v_platform_entryarea_bottom OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 347 (class 1259 OID 992890)
-- Name: v_platform_entryarea_top; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_platform_entryarea_top AS
 SELECT ((50000000 + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS id,
    (((200000000 + 50000000) + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS platform_entryarea_id,
    a.entryarea2_entry_id AS entry_id,
    lower((a.entryarea2_is_entry_closing)::text) AS is_entry_closing,
    a.entryarea2_entry_width AS entry_width,
    a.entryarea2_width AS width,
    a.entryarea2_depth AS depth,
    a.entryarea2_height_elevation AS height_elevation
   FROM (platform a
     JOIN map_object o ON ((a.map_object_id = o.id)))
  WHERE (a.entryarea1_entry_id IS NOT NULL);


ALTER TABLE v_platform_entryarea_top OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 348 (class 1259 OID 992895)
-- Name: v_ramp; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_ramp AS
 SELECT ((COALESCE(a.ramp_relation_id, 3) * 10000000) + o.object_id) AS id,
    ((60000000 + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS ramp_id,
    a.ramp_localization_id,
    a.ramp_mobility_id,
    a.ramp_type_id,
    a.ramp_number_of_legs,
    a.ramp_surface_id,
    lower((a.ramp_is_handle)::text) AS ramp_is_handle,
    cs.ramp_interior_localization AS cs_ramp_interior_localization,
    cs.ramp_access_provided_by AS cs_ramp_access_provided_by,
    cs.ramp_skids_has_notes AS cs_ramp_skids_has_notes,
    cs.ramp_skids_has_description AS cs_ramp_skids_has_description,
    en.ramp_interior_localization AS en_ramp_interior_localization,
    en.ramp_access_provided_by AS en_ramp_access_provided_by,
    en.ramp_skids_has_notes AS en_ramp_skids_has_notes,
    en.ramp_skids_has_description AS en_ramp_skids_has_description
   FROM (((rampskids a
     JOIN map_object o ON ((a.map_object_id = o.id)))
     LEFT JOIN rampskids_lang cs ON (((a.id = cs.rampskids_id) AND (cs.lang_id = 'cs'::bpchar))))
     LEFT JOIN rampskids_lang en ON (((a.id = en.rampskids_id) AND (en.lang_id = 'en'::bpchar))))
  WHERE (a.ramp_localization_id IS NOT NULL);


ALTER TABLE v_ramp OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 331 (class 1259 OID 941282)
-- Name: v_ramp_entryarea_bottom; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_ramp_entryarea_bottom AS
 SELECT ((60000000 + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS id,
    (((100000000 + 60000000) + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS ramp_entryarea_id,
    a.ramp_bottom_entryarea_width AS entry_width,
    a.ramp_bottom_entryarea_depth AS entry_depth
   FROM (rampskids a
     JOIN map_object o ON ((a.map_object_id = o.id)))
  WHERE (a.ramp_bottom_entryarea_width IS NOT NULL);


ALTER TABLE v_ramp_entryarea_bottom OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 333 (class 1259 OID 941293)
-- Name: v_ramp_entryarea_landings; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_ramp_entryarea_landings AS
 SELECT ((60000000 + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS id,
    (((300000000 + 60000000) + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS ramp_entryarea_id,
    a.ramp_landings_entryarea_width AS entry_width,
    a.ramp_landings_entryarea_depth AS entry_depth
   FROM (rampskids a
     JOIN map_object o ON ((a.map_object_id = o.id)))
  WHERE (a.ramp_landings_entryarea_width IS NOT NULL);


ALTER TABLE v_ramp_entryarea_landings OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 332 (class 1259 OID 941287)
-- Name: v_ramp_entryarea_top; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_ramp_entryarea_top AS
 SELECT ((60000000 + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS id,
    (((200000000 + 60000000) + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS ramp_entryarea_id,
    a.ramp_top_entryarea_width AS entry_width,
    a.ramp_top_entryarea_depth AS entry_depth
   FROM (rampskids a
     JOIN map_object o ON ((a.map_object_id = o.id)))
  WHERE (a.ramp_top_entryarea_width IS NOT NULL);


ALTER TABLE v_ramp_entryarea_top OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 334 (class 1259 OID 941298)
-- Name: v_ramp_handle; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_ramp_handle AS
 SELECT ((60000000 + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS id,
    a.ramp_handle_orientation_id,
    a.ramp_handle1_height,
    a.ramp_handle2_height
   FROM (rampskids a
     JOIN map_object o ON ((a.map_object_id = o.id)))
  WHERE a.ramp_is_handle;


ALTER TABLE v_ramp_handle OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 330 (class 1259 OID 941277)
-- Name: v_rampleg; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_rampleg AS
 SELECT ((60000000 + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS id,
    (((100000000 + 60000000) + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS rampleg_id,
    a.rampleg1_width AS rampleg_width,
    a.rampleg1_length AS rampleg_length,
    a.rampleg1_inclination AS rampleg_inclination
   FROM (rampskids a
     JOIN map_object o ON ((a.map_object_id = o.id)))
  WHERE (a.rampleg1_width IS NOT NULL)
UNION ALL
 SELECT ((60000000 + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS id,
    (((200000000 + 60000000) + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS rampleg_id,
    a.rampleg2_width AS rampleg_width,
    a.rampleg2_length AS rampleg_length,
    a.rampleg2_inclination AS rampleg_inclination
   FROM (rampskids a
     JOIN map_object o ON ((a.map_object_id = o.id)))
  WHERE (a.rampleg2_width IS NOT NULL)
UNION ALL
 SELECT ((60000000 + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS id,
    (((300000000 + 60000000) + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS rampleg_id,
    a.rampleg3_width AS rampleg_width,
    a.rampleg3_length AS rampleg_length,
    a.rampleg3_inclination AS rampleg_inclination
   FROM (rampskids a
     JOIN map_object o ON ((a.map_object_id = o.id)))
  WHERE (a.rampleg3_width IS NOT NULL)
UNION ALL
 SELECT ((60000000 + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS id,
    (((400000000 + 60000000) + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS rampleg_id,
    a.rampleg4_width AS rampleg_width,
    a.rampleg4_length AS rampleg_length,
    a.rampleg4_inclination AS rampleg_inclination
   FROM (rampskids a
     JOIN map_object o ON ((a.map_object_id = o.id)))
  WHERE (a.rampleg4_width IS NOT NULL);


ALTER TABLE v_rampleg OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 329 (class 1259 OID 941272)
-- Name: v_skids; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_skids AS
 SELECT (30000000 + o.object_id) AS id,
    ((80000000 + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS skids_id,
    a.skids_localization_id,
    a.skids_mobility_id,
    a.skids_inclination,
    a.skids_length,
    cs.skids_interior_localization AS cs_skids_interior_localization,
    cs.ramp_skids_has_notes AS cs_ramp_skids_has_notes,
    cs.ramp_skids_has_description AS cs_ramp_skids_has_description,
    en.skids_interior_localization AS en_skids_interior_localization,
    en.ramp_skids_has_notes AS en_ramp_skids_has_notes,
    en.ramp_skids_has_description AS en_ramp_skids_has_description
   FROM (((rampskids a
     JOIN map_object o ON ((a.map_object_id = o.id)))
     LEFT JOIN rampskids_lang cs ON (((a.id = cs.rampskids_id) AND (cs.lang_id = 'cs'::bpchar))))
     LEFT JOIN rampskids_lang en ON (((a.id = en.rampskids_id) AND (en.lang_id = 'en'::bpchar))))
  WHERE (a.skids_localization_id IS NOT NULL);


ALTER TABLE v_skids OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 349 (class 1259 OID 992900)
-- Name: v_steps_interior; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_steps_interior AS
 SELECT o.id,
    (30000000 + o.object_id) AS steps_id,
    o.object_steps1_number_of AS steps_number_of,
    o.object_steps1_height AS steps_height,
    o.object_steps1_depth AS steps_depth,
    lower((o.object_steps_is_contrast_marked)::text) AS steps_is_contrast_marked,
    cs.object_steps1_localization AS cs_steps_localization,
    en.object_steps1_localization AS en_steps_localization
   FROM ((map_object o
     LEFT JOIN map_object_lang cs ON (((o.id = cs.map_object_id) AND (cs.lang_id = 'cs'::bpchar))))
     LEFT JOIN map_object_lang en ON (((o.id = en.map_object_id) AND (en.lang_id = 'en'::bpchar))))
  WHERE o.object_is_steps;


ALTER TABLE v_steps_interior OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 350 (class 1259 OID 992905)
-- Name: v_user; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_user AS
 SELECT "user".id,
    "user".login,
    lower(("user".certified)::text) AS certified
   FROM "user";


ALTER TABLE v_user OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 257 (class 1259 OID 736269)
-- Name: w_c_basin_space; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE w_c_basin_space (
    id integer NOT NULL,
    title character varying(255),
    pair_key character varying(255)
);


ALTER TABLE w_c_basin_space OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 321 (class 1259 OID 941175)
-- Name: v_w_c_bs; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_w_c_bs AS
 SELECT w_c_basin_space.id,
    w_c_basin_space.title
   FROM w_c_basin_space;


ALTER TABLE v_w_c_bs OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3210 (class 0 OID 0)
-- Dependencies: 321
-- Name: VIEW v_w_c_bs; Type: COMMENT; Schema: public; Owner: mapy_pristupnosti_db_01
--

COMMENT ON VIEW v_w_c_bs IS 'View pro w_c_basin_space - kvůli konfliktu jmen R2RML parseru.';


--
-- TOC entry 249 (class 1259 OID 736237)
-- Name: w_c_cabin_localization; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE w_c_cabin_localization (
    id integer NOT NULL,
    title character varying(255),
    pair_key character varying(255),
    combine_key json
);


ALTER TABLE w_c_cabin_localization OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 308 (class 1259 OID 881096)
-- Name: v_w_c_cabin_localization; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_w_c_cabin_localization AS
 SELECT i.id,
    o.title
   FROM (w_c_cabin_localization o
     JOIN ( SELECT w_c_cabin_localization.id,
            (json_array_elements_text(COALESCE(w_c_cabin_localization.combine_key, to_json(ARRAY[w_c_cabin_localization.id]))))::integer AS single_id
           FROM w_c_cabin_localization) i ON ((o.id = i.single_id)));


ALTER TABLE v_w_c_cabin_localization OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 269 (class 1259 OID 736317)
-- Name: w_c_door_handle_position; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE w_c_door_handle_position (
    id integer NOT NULL,
    title character varying(255),
    pair_key character varying(255),
    combine_key json
);


ALTER TABLE w_c_door_handle_position OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 309 (class 1259 OID 881100)
-- Name: v_w_c_door_handle_position; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_w_c_door_handle_position AS
 SELECT i.id,
    o.title
   FROM (w_c_door_handle_position o
     JOIN ( SELECT w_c_door_handle_position.id,
            (json_array_elements_text(COALESCE(w_c_door_handle_position.combine_key, to_json(ARRAY[w_c_door_handle_position.id]))))::integer AS single_id
           FROM w_c_door_handle_position) i ON ((o.id = i.single_id)));


ALTER TABLE v_w_c_door_handle_position OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 293 (class 1259 OID 806900)
-- Name: washbasin_handle_type; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE washbasin_handle_type (
    id integer NOT NULL,
    title character varying(255),
    pair_key character varying(255)
);


ALTER TABLE washbasin_handle_type OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 327 (class 1259 OID 941259)
-- Name: v_wb_ht; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_wb_ht AS
 SELECT washbasin_handle_type.id,
    washbasin_handle_type.title
   FROM washbasin_handle_type;


ALTER TABLE v_wb_ht OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 271 (class 1259 OID 736325)
-- Name: washbasin_underpass; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE washbasin_underpass (
    id integer NOT NULL,
    title character varying(255),
    pair_key character varying(255)
);


ALTER TABLE washbasin_underpass OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 328 (class 1259 OID 941263)
-- Name: v_wb_u; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_wb_u AS
 SELECT washbasin_underpass.id,
    washbasin_underpass.title
   FROM washbasin_underpass;


ALTER TABLE v_wb_u OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 284 (class 1259 OID 737316)
-- Name: wc_lang; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE wc_lang (
    wc_id integer NOT NULL,
    lang_id character(2) NOT NULL,
    wc_localization character varying(255),
    wc_has_notes text,
    wc_has_description text,
    wc_access_provided_by character varying(255)
);


ALTER TABLE wc_lang OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 351 (class 1259 OID 992909)
-- Name: v_wc; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_wc AS
 SELECT (30000000 + o.object_id) AS id,
    ((70000000 + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS wc_id,
    a.wc_accessibility_id,
    a.wc_cabin_access_id,
    a.wc_cabin_localization_id,
    a.wc_switch_id,
    a.wc_switch_height,
    a.wc_cabin_width,
    a.wc_cabin_depth,
    a.wc_flushing_back_height,
    a.wc_flushing_side_height,
    a.wc_flushing_side_distanc,
    a.wc_flushing_id,
    a.wc_flushing_difficulty_id,
    a.wc_handles_distance,
    a.wc_basin_left_distance,
    a.wc_basin_right_distance,
    a.wc_basin_back_indentation,
    a.wc_basin_seat_height,
    lower((a.wc_basin_is_paper_reach)::text) AS wc_basin_is_paper_reach,
    a.wc_basin_space_id,
    lower((a.wc_is_changingdesk)::text) AS wc_is_changingdesk,
    lower((a.wc_is_changingdesk_obstructs)::text) AS wc_is_changingdesk_obstructs,
    a.wc_changingdesk_id,
    lower((a.wc_is_alarmbutton)::text) AS wc_is_alarmbutton,
    a.wc_alarmbutton_top_heigh,
    a.wc_alarmbutton_bottom_height,
    lower((a.wc_is_regular_w_c)::text) AS wc_is_regular_w_c,
    lower((a.wc_is_regular_w_c_braille_marking)::text) AS wc_is_regular_w_c_braille_marking,
    a.wc_cabin_door_disposition_id,
    a.wc_cabin_w_c_basin_disposition_id,
    a.wc_cabin_wash_basin_disposition_id,
    cs.wc_localization AS cs_wc_localization,
    cs.wc_has_notes AS cs_wc_has_notes,
    cs.wc_has_description AS cs_wc_has_description,
    cs.wc_access_provided_by AS cs_wc_access_provided_by,
    en.wc_localization AS en_wc_localization,
    en.wc_has_notes AS en_wc_has_notes,
    en.wc_has_description AS en_wc_has_description,
    en.wc_access_provided_by AS en_wc_access_provided_by
   FROM (((wc a
     JOIN map_object o ON ((a.map_object_id = o.id)))
     LEFT JOIN wc_lang cs ON (((a.id = cs.wc_id) AND (cs.lang_id = 'cs'::bpchar))))
     LEFT JOIN wc_lang en ON (((a.id = en.wc_id) AND (en.lang_id = 'en'::bpchar))));


ALTER TABLE v_wc OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 322 (class 1259 OID 941201)
-- Name: v_wc_door_handle; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_wc_door_handle AS
 SELECT ((70000000 + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS id,
    a.door_handle_position_id
   FROM (wc a
     JOIN map_object o ON ((a.map_object_id = o.id)))
  WHERE (a.door_width IS NOT NULL);


ALTER TABLE v_wc_door_handle OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 320 (class 1259 OID 941164)
-- Name: v_wc_hallway; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_wc_hallway AS
 SELECT ((70000000 + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS id,
    (((100000000 + 70000000) + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS wc_hallway_id,
    a.hallway1_width AS hallway_width,
    a.hallway1_depth AS hallway_depth
   FROM (wc a
     JOIN map_object o ON ((a.map_object_id = o.id)))
  WHERE (a.hallway1_width IS NOT NULL)
UNION ALL
 SELECT ((70000000 + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS id,
    (((200000000 + 70000000) + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS wc_hallway_id,
    a.hallway2_width AS hallway_width,
    a.hallway2_depth AS hallway_depth
   FROM (wc a
     JOIN map_object o ON ((a.map_object_id = o.id)))
  WHERE (a.hallway2_width IS NOT NULL);


ALTER TABLE v_wc_hallway OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 325 (class 1259 OID 941239)
-- Name: v_wc_left_handle; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_wc_left_handle AS
 SELECT ((70000000 + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS id,
    (((200000000 + 70000000) + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS handle_id,
    a.handle1_type_id AS handle_type_id,
    a.handle1_height AS handle_height,
    a.handle1_length AS handle_length
   FROM (wc a
     JOIN map_object o ON ((a.map_object_id = o.id)))
  WHERE (a.handle1_type_id IS NOT NULL);


ALTER TABLE v_wc_left_handle OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 326 (class 1259 OID 941254)
-- Name: v_wc_right_handle; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_wc_right_handle AS
 SELECT ((70000000 + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS id,
    (((300000000 + 70000000) + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS handle_id,
    a.handle2_type_id AS handle_type_id,
    a.handle2_height AS handle_height,
    a.handle2_length AS handle_length
   FROM (wc a
     JOIN map_object o ON ((a.map_object_id = o.id)))
  WHERE (a.handle2_type_id IS NOT NULL);


ALTER TABLE v_wc_right_handle OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 352 (class 1259 OID 992914)
-- Name: v_wc_wb; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_wc_wb AS
 SELECT ((70000000 + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS id,
    a.washbasin_height,
    a.washbasin_underpass_id,
    lower((a.washbasin_is_handle)::text) AS washbasin_is_handle
   FROM (wc a
     JOIN map_object o ON ((a.map_object_id = o.id)))
  WHERE (a.washbasin_height IS NOT NULL);


ALTER TABLE v_wc_wb OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3211 (class 0 OID 0)
-- Dependencies: 352
-- Name: VIEW v_wc_wb; Type: COMMENT; Schema: public; Owner: mapy_pristupnosti_db_01
--

COMMENT ON VIEW v_wc_wb IS 'WcWashBasin';


--
-- TOC entry 324 (class 1259 OID 941234)
-- Name: v_wc_wb_h; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_wc_wb_h AS
 SELECT ((70000000 + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS id,
    (((100000000 + 70000000) + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS handle_id,
    a.washbasin_handle_type_id AS handle_type_id,
    a.washbasin_handle_height AS handle_height,
    a.washbasin_handle_length AS handle_length
   FROM (wc a
     JOIN map_object o ON ((a.map_object_id = o.id)))
  WHERE a.washbasin_is_handle;


ALTER TABLE v_wc_wb_h OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3212 (class 0 OID 0)
-- Dependencies: 324
-- Name: VIEW v_wc_wb_h; Type: COMMENT; Schema: public; Owner: mapy_pristupnosti_db_01
--

COMMENT ON VIEW v_wc_wb_h IS 'WashBasinHandle';


--
-- TOC entry 323 (class 1259 OID 941211)
-- Name: v_wc_wb_t; Type: VIEW; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE VIEW v_wc_wb_t AS
 SELECT ((70000000 + (1000000 * dense_rank() OVER (PARTITION BY a.map_object_id ORDER BY a.id))) + o.object_id) AS id,
    a.tap_height,
    a.tap_type_id
   FROM (wc a
     JOIN map_object o ON ((a.map_object_id = o.id)))
  WHERE (a.tap_type_id IS NOT NULL);


ALTER TABLE v_wc_wb_t OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3213 (class 0 OID 0)
-- Dependencies: 323
-- Name: VIEW v_wc_wb_t; Type: COMMENT; Schema: public; Owner: mapy_pristupnosti_db_01
--

COMMENT ON VIEW v_wc_wb_t IS 'WashBasinTap';


--
-- TOC entry 256 (class 1259 OID 736267)
-- Name: w_c_basin_space_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE w_c_basin_space_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE w_c_basin_space_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3214 (class 0 OID 0)
-- Dependencies: 256
-- Name: w_c_basin_space_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE w_c_basin_space_id_seq OWNED BY w_c_basin_space.id;


--
-- TOC entry 261 (class 1259 OID 736285)
-- Name: w_c_cabin_disposition; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE w_c_cabin_disposition (
    id integer NOT NULL,
    title character varying(255),
    pair_key character varying(255)
);


ALTER TABLE w_c_cabin_disposition OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 260 (class 1259 OID 736283)
-- Name: w_c_cabin_disposition_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE w_c_cabin_disposition_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE w_c_cabin_disposition_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3215 (class 0 OID 0)
-- Dependencies: 260
-- Name: w_c_cabin_disposition_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE w_c_cabin_disposition_id_seq OWNED BY w_c_cabin_disposition.id;


--
-- TOC entry 248 (class 1259 OID 736235)
-- Name: w_c_cabin_localization_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE w_c_cabin_localization_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE w_c_cabin_localization_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3216 (class 0 OID 0)
-- Dependencies: 248
-- Name: w_c_cabin_localization_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE w_c_cabin_localization_id_seq OWNED BY w_c_cabin_localization.id;


--
-- TOC entry 247 (class 1259 OID 736229)
-- Name: w_c_categorization; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE w_c_categorization (
    id integer NOT NULL,
    title character varying(255),
    pair_key character varying(255)
);


ALTER TABLE w_c_categorization OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 246 (class 1259 OID 736227)
-- Name: w_c_categorization_m_k_p_o_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE w_c_categorization_m_k_p_o_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE w_c_categorization_m_k_p_o_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3217 (class 0 OID 0)
-- Dependencies: 246
-- Name: w_c_categorization_m_k_p_o_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE w_c_categorization_m_k_p_o_id_seq OWNED BY w_c_categorization.id;


--
-- TOC entry 259 (class 1259 OID 736277)
-- Name: w_c_changingdesk; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE w_c_changingdesk (
    id integer NOT NULL,
    title character varying(255),
    pair_key character varying(255)
);


ALTER TABLE w_c_changingdesk OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 258 (class 1259 OID 736275)
-- Name: w_c_changingdesk_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE w_c_changingdesk_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE w_c_changingdesk_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3218 (class 0 OID 0)
-- Dependencies: 258
-- Name: w_c_changingdesk_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE w_c_changingdesk_id_seq OWNED BY w_c_changingdesk.id;


--
-- TOC entry 268 (class 1259 OID 736315)
-- Name: w_c_door_handle_position_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE w_c_door_handle_position_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE w_c_door_handle_position_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3219 (class 0 OID 0)
-- Dependencies: 268
-- Name: w_c_door_handle_position_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE w_c_door_handle_position_id_seq OWNED BY w_c_door_handle_position.id;


--
-- TOC entry 267 (class 1259 OID 736309)
-- Name: w_c_door_opening_direction; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE w_c_door_opening_direction (
    id integer NOT NULL,
    title character varying(255),
    pair_key character varying(255)
);


ALTER TABLE w_c_door_opening_direction OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 266 (class 1259 OID 736307)
-- Name: w_c_door_opening_direction_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE w_c_door_opening_direction_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE w_c_door_opening_direction_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3220 (class 0 OID 0)
-- Dependencies: 266
-- Name: w_c_door_opening_direction_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE w_c_door_opening_direction_id_seq OWNED BY w_c_door_opening_direction.id;


--
-- TOC entry 253 (class 1259 OID 736253)
-- Name: w_c_flushing; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE w_c_flushing (
    id integer NOT NULL,
    title character varying(255),
    pair_key character varying(255)
);


ALTER TABLE w_c_flushing OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 255 (class 1259 OID 736261)
-- Name: w_c_flushing_difficulty; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE w_c_flushing_difficulty (
    id integer NOT NULL,
    title character varying(255),
    pair_key character varying(255)
);


ALTER TABLE w_c_flushing_difficulty OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 254 (class 1259 OID 736259)
-- Name: w_c_flushing_difficulty_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE w_c_flushing_difficulty_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE w_c_flushing_difficulty_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3221 (class 0 OID 0)
-- Dependencies: 254
-- Name: w_c_flushing_difficulty_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE w_c_flushing_difficulty_id_seq OWNED BY w_c_flushing_difficulty.id;


--
-- TOC entry 252 (class 1259 OID 736251)
-- Name: w_c_flushing_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE w_c_flushing_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE w_c_flushing_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3222 (class 0 OID 0)
-- Dependencies: 252
-- Name: w_c_flushing_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE w_c_flushing_id_seq OWNED BY w_c_flushing.id;


--
-- TOC entry 251 (class 1259 OID 736245)
-- Name: w_c_switch; Type: TABLE; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE w_c_switch (
    id integer NOT NULL,
    title character varying(255),
    pair_key character varying(255)
);


ALTER TABLE w_c_switch OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 250 (class 1259 OID 736243)
-- Name: w_c_switch_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE w_c_switch_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE w_c_switch_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3223 (class 0 OID 0)
-- Dependencies: 250
-- Name: w_c_switch_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE w_c_switch_id_seq OWNED BY w_c_switch.id;


--
-- TOC entry 292 (class 1259 OID 806898)
-- Name: washbasin_handle_type_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE washbasin_handle_type_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE washbasin_handle_type_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3224 (class 0 OID 0)
-- Dependencies: 292
-- Name: washbasin_handle_type_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE washbasin_handle_type_id_seq OWNED BY washbasin_handle_type.id;


--
-- TOC entry 270 (class 1259 OID 736323)
-- Name: washbasin_underpass_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE washbasin_underpass_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE washbasin_underpass_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3225 (class 0 OID 0)
-- Dependencies: 270
-- Name: washbasin_underpass_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE washbasin_underpass_id_seq OWNED BY washbasin_underpass.id;


--
-- TOC entry 196 (class 1259 OID 731399)
-- Name: wc_id_seq; Type: SEQUENCE; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE wc_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE wc_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3226 (class 0 OID 0)
-- Dependencies: 196
-- Name: wc_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE wc_id_seq OWNED BY wc.id;


SET search_path = service, pg_catalog;

--
-- TOC entry 296 (class 1259 OID 819339)
-- Name: api_quota; Type: TABLE; Schema: service; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE api_quota (
    id integer NOT NULL,
    ip character varying(255) NOT NULL,
    ping timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE api_quota OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 297 (class 1259 OID 819347)
-- Name: api_quota_id_seq; Type: SEQUENCE; Schema: service; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE api_quota_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE api_quota_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3227 (class 0 OID 0)
-- Dependencies: 297
-- Name: api_quota_id_seq; Type: SEQUENCE OWNED BY; Schema: service; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE api_quota_id_seq OWNED BY api_quota.id;


--
-- TOC entry 289 (class 1259 OID 757490)
-- Name: geocoding_request; Type: TABLE; Schema: service; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE geocoding_request (
    id integer NOT NULL,
    result character varying(64),
    priority boolean DEFAULT false NOT NULL,
    object_id integer NOT NULL
);


ALTER TABLE geocoding_request OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 288 (class 1259 OID 757488)
-- Name: geocoding_request_id_seq; Type: SEQUENCE; Schema: service; Owner: mapy_pristupnosti_db_01
--

CREATE SEQUENCE geocoding_request_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE geocoding_request_id_seq OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 3228 (class 0 OID 0)
-- Dependencies: 288
-- Name: geocoding_request_id_seq; Type: SEQUENCE OWNED BY; Schema: service; Owner: mapy_pristupnosti_db_01
--

ALTER SEQUENCE geocoding_request_id_seq OWNED BY geocoding_request.id;

-- Table: service.markers_cache

-- DROP TABLE service.markers_cache;

CREATE TABLE service.markers_cache
(
  key text NOT NULL,
  data json NOT NULL,
  expire timestamp without time zone,
  CONSTRAINT markers_cache_pk PRIMARY KEY (key)
);

ALTER TABLE service.markers_cache OWNER TO mapy_pristupnosti_db_01;

-- Index: service.markers_cache_expire_index

-- DROP INDEX service.markers_cache_expire_index;

CREATE INDEX markers_cache_expire_index
  ON service.markers_cache
  USING btree
  (expire);


SET search_path = versions, pg_catalog;

--
-- TOC entry 280 (class 1259 OID 737038)
-- Name: elevator; Type: TABLE; Schema: versions; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE elevator (
    id integer NOT NULL,
    map_object_id integer NOT NULL,
    elevator_access_id integer,
    elevator_type_id integer,
    elevator_driveoff_id integer,
    elevator_control1_max_height integer,
    elevator_control1_relief_marking_id integer,
    elevator_control1_flat_marking_id integer,
    elevator_is_control1_braille_marking boolean,
    elevator_is_a_o_b boolean,
    elevator_aob_is_above_door boolean,
    elevator_a_o_b_announcements_scheme_id integer,
    elevator_is_cage_passthrough boolean,
    elevator_cage_width integer,
    elevator_cage_depth integer,
    elevator_cage_seconddoor_localization_id integer,
    elevator_cage_control_distance integer,
    elevator_cage_control_height integer,
    elevator_control2_relief_marking_id integer,
    elevator_control2_flat_marking_id integer,
    elevator_is_control2_braille_marking boolean,
    elevator_is_cage_control_announcement_acoustic boolean,
    elevator_is_cage_control_announcement_phonetic boolean,
    elevator_is_cage_handle boolean,
    elevator_handle_localization_id integer,
    elevator_is_cage_mirror boolean,
    elevator_cage_mirror_localization_id integer,
    elevator_cage_mirror_height integer,
    elevator_is_cage_seat boolean,
    elevator_is_cage_seat_withinreach boolean,
    elevator_is_cage_seat_functional boolean,
    entryarea_width integer,
    entryarea_depth integer,
    entryarea_height_elevation integer,
    door1_width integer,
    door1_opening_id integer,
    door2_width integer,
    door2_opening_id integer
);


ALTER TABLE elevator OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 281 (class 1259 OID 737113)
-- Name: elevator_lang; Type: TABLE; Schema: versions; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE elevator_lang (
    elevator_id integer NOT NULL,
    lang_id character(2) NOT NULL,
    elevator_localization character varying(255),
    elevator_access_provided_by character varying(255),
    elevator_connects_floors character varying(255),
    elevator_aob_localization character varying(255),
    elevator_has_notes text,
    elevator_has_description text
);


ALTER TABLE elevator_lang OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 274 (class 1259 OID 736745)
-- Name: map_object; Type: TABLE; Schema: versions; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE map_object (
    id integer NOT NULL,
    object_id integer NOT NULL,
    parent_object_id integer,
    modified_date timestamp without time zone NOT NULL,
    mapping_date timestamp without time zone NOT NULL,
    user_id integer DEFAULT 1 NOT NULL,
    object_type_id integer NOT NULL,
    accessibility_id integer NOT NULL,
    license_id integer NOT NULL,
    certified boolean DEFAULT false NOT NULL,
    organization_ic character varying(255),
    organization_name character varying(255),
    latitude numeric,
    longitude numeric,
    ruian_address integer,
    zipcode character varying(255),
    city character varying(255),
    city_part character varying(255),
    street character varying(255),
    street_desc_no integer,
    street_no_is_alternative boolean NOT NULL DEFAULT FALSE,
    street_orient_no integer,
    street_orient_symbol character varying(255),
    entrance1_is_reserved_parking boolean,
    entrance1_number_of_reserved_parking integer,
    entrance1_is_longitudinal_inclination boolean,
    entrance1_longitudinal_inclination double precision,
    entrance1_is_transverse_inclination boolean,
    entrance1_transverse_inclination double precision,
    entrance1_is_difficult_surface boolean,
    entrance1_guidingline_id integer,
    entrance1_accessibility_id integer,
    entrance1_area_before_door_width integer,
    entrance1_area_before_door_depth integer,
    entrance1_lobby_width integer,
    entrance1_lobby_depth integer,
    entrance1_is_a_o_b boolean,
    entrance1_aob_is_above_door boolean,
    entrance1_bell_type_id integer,
    entrance1_bell_height integer,
    entrance1_bell_indentation integer,
    entrance1_steps1_number_of integer,
    entrance1_steps1_height integer,
    entrance1_steps1_depth integer,
    entrance1_steps2_number_of integer,
    entrance1_steps2_height integer,
    entrance1_steps2_depth integer,
    entrance1_contrast_marking_is_glass_surfaces boolean,
    entrance1_steps_is_contrast_marked boolean,
    entrance1_door1_mainpanel_width integer,
    entrance1_door1_sidepanel_width integer,
    entrance1_door1_type_id integer,
    entrance1_door1_opening_id integer,
    entrance1_door1_opening_direction_id integer,
    entrance1_door1_step_height integer,
    entrance1_door2_mainpanel_width integer,
    entrance1_door2_sidepanel_width integer,
    entrance1_door2_type_id integer,
    entrance1_door2_opening_id integer,
    entrance1_door2_opening_direction_id integer,
    entrance1_door2_step_height integer,
    entrance2_is_side_entrance_marked boolean,
    entrance2_is_side_entrance_information boolean,
    entrance2_access_id integer,
    entrance2_is_reserved_parking boolean,
    entrance2_number_of_reserved_parking integer,
    entrance2_is_longitudinal_inclination boolean,
    entrance2_longitudinal_inclination double precision,
    entrance2_is_transverse_inclination boolean,
    entrance2_transverse_inclination double precision,
    entrance2_is_difficult_surface boolean,
    entrance2_guidingline_id integer,
    entrance2_accessibility_id integer,
    entrance2_area_before_door_width integer,
    entrance2_area_before_door_depth integer,
    entrance2_lobby_width integer,
    entrance2_lobby_depth integer,
    entrance2_is_a_o_b boolean,
    entrance2_aob_is_above_door boolean,
    entrance2_bell_type_id integer,
    entrance2_bell_height integer,
    entrance2_bell_indentation integer,
    entrance2_steps1_number_of integer,
    entrance2_steps1_height integer,
    entrance2_steps1_depth integer,
    entrance2_steps2_number_of integer,
    entrance2_steps2_height integer,
    entrance2_steps2_depth integer,
    entrance2_contrast_marking_is_glass_surfaces boolean,
    entrance2_steps_is_contrast_marked boolean,
    entrance2_door1_mainpanel_width integer,
    entrance2_door1_sidepanel_width integer,
    entrance2_door1_type_id integer,
    entrance2_door1_opening_id integer,
    entrance2_door1_opening_direction_id integer,
    entrance2_door1_step_height integer,
    entrance2_door2_mainpanel_width integer,
    entrance2_door2_sidepanel_width integer,
    entrance2_door2_type_id integer,
    entrance2_door2_opening_id integer,
    entrance2_door2_opening_direction_id integer,
    entrance2_door2_step_height integer,
    object_is_steps boolean,
    object_steps1_number_of integer,
    object_steps1_height integer,
    object_steps1_depth integer,
    object_is_stairs boolean,
    object_stairs_type_id integer,
    object_stairs_width integer,
    object_stairs_is_bannister boolean,
    object_is_narrowed_passage boolean,
    object_narrowed_passage_width integer,
    object_is_tourniquet boolean,
    object_is_navigation_system boolean,
    object_interior_accessibility_id integer,
    object_contrast_marking_is_glass_surfaces boolean,
    object_steps_is_contrast_marked boolean,
    object_is_a_o_b boolean,
    object_aob_is_above_door boolean,
    source_id integer NOT NULL,
    external_data json,
    web_url character varying(255),
    region character varying(255),
    entrance1_contrast_marking_localization_id integer,
    entrance2_contrast_marking_localization_id integer,
    object_contrast_marking_localization_id integer,
    data_owner_url character varying(255)
);


ALTER TABLE map_object OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 275 (class 1259 OID 736887)
-- Name: map_object_lang; Type: TABLE; Schema: versions; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE map_object_lang (
    map_object_id integer NOT NULL,
    lang_id character(2) NOT NULL,
    title character varying(255) NOT NULL,
    description text,
    object_type_custom character varying(255),
    entrance1_reserved_parking_localization character varying(255),
    entrance1_reserved_parking_access_description character varying(255),
    entrance1_longitudinal_inclination_localization character varying(255),
    entrance1_transverse_inclination_localization character varying(255),
    entrance1_difficult_surface_description character varying(255),
    entrance1_aob_localization character varying(255),
    entrance1_has_description text,
    entrance1_has_notes text,
    entrance2_localization character varying(255),
    entrance2_access_provided_by character varying(255),
    entrance2_reserved_parking_localization character varying(255),
    entrance2_reserved_parking_access_description character varying(255),
    entrance2_longitudinal_inclination_localization character varying(255),
    entrance2_transverse_inclination_localization character varying(255),
    entrance2_difficult_surface_description character varying(255),
    entrance2_aob_localization character varying(255),
    entrance2_has_description text,
    entrance2_has_notes text,
    object_steps1_localization character varying(255),
    object_narrowed_passage_localization character varying(255),
    object_tourniquet_localization character varying(255),
    object_navigation_system_description character varying(255),
    object_has_description text,
    object_has_notes text,
    object_aob_localization character varying(255),
    search_title character varying(255)
);


ALTER TABLE map_object_lang OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 278 (class 1259 OID 736975)
-- Name: platform; Type: TABLE; Schema: versions; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE platform (
    id integer NOT NULL,
    map_object_id integer NOT NULL,
    platform_relation_id integer,
    platform_access_id integer,
    platform_type_id integer,
    platform_max_load integer,
    platform_width integer,
    platform_depth integer,
    platform_is_min_parameters boolean,
    platform_number_of_steps integer,
    platform_number_of_floors integer,
    platform_outside_bottom_control_height integer,
    platform_outside_top_control_height integer,
    platform_inside_control_height integer,
    entryarea1_entry_id integer,
    entryarea1_is_entry_closing boolean,
    entryarea1_entry_width integer,
    entryarea1_width integer,
    entryarea1_depth integer,
    entryarea1_height_elevation integer,
    entryarea1_bell_type_id integer,
    entryarea1_bell_height integer,
    entryarea1_bell_indentation integer,
    entryarea2_entry_id integer,
    entryarea2_is_entry_closing boolean,
    entryarea2_entry_width integer,
    entryarea2_width integer,
    entryarea2_depth integer,
    entryarea2_height_elevation integer,
    entryarea2_bell_type_id integer,
    entryarea2_bell_height integer,
    entryarea2_bell_indentation integer
);


ALTER TABLE platform OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 279 (class 1259 OID 737020)
-- Name: platform_lang; Type: TABLE; Schema: versions; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE platform_lang (
    platform_id integer NOT NULL,
    lang_id character(2) NOT NULL,
    platform_localization character varying(255),
    platform_has_notes text,
    platform_has_description text
);


ALTER TABLE platform_lang OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 276 (class 1259 OID 736907)
-- Name: rampskids; Type: TABLE; Schema: versions; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE rampskids (
    id integer NOT NULL,
    map_object_id integer NOT NULL,
    ramp_relation_id integer,
    ramp_localization_id integer,
    ramp_mobility_id integer,
    ramp_type_id integer,
    ramp_number_of_legs integer,
    rampleg1_width integer,
    rampleg1_length integer,
    rampleg1_inclination double precision,
    rampleg2_width integer,
    rampleg2_length integer,
    rampleg2_inclination double precision,
    rampleg3_width integer,
    rampleg3_length integer,
    rampleg3_inclination double precision,
    rampleg4_width integer,
    rampleg4_length integer,
    rampleg4_inclination double precision,
    ramp_bottom_entryarea_width integer,
    ramp_bottom_entryarea_depth integer,
    ramp_top_entryarea_width integer,
    ramp_top_entryarea_depth integer,
    ramp_landings_entryarea_width integer,
    ramp_landings_entryarea_depth integer,
    ramp_surface_id integer,
    ramp_is_handle boolean,
    ramp_handle_orientation_id integer,
    ramp_handle1_height integer,
    skids_localization_id integer,
    skids_mobility_id integer,
    skids_inclination double precision,
    skids_length integer,
    ramp_handle2_height integer
);


ALTER TABLE rampskids OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 277 (class 1259 OID 736957)
-- Name: rampskids_lang; Type: TABLE; Schema: versions; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE rampskids_lang (
    rampskids_id integer NOT NULL,
    lang_id character(2) NOT NULL,
    ramp_interior_localization character varying(255),
    ramp_access_provided_by character varying(255),
    skids_interior_localization character varying(255),
    ramp_skids_has_notes text,
    ramp_skids_has_description text
);


ALTER TABLE rampskids_lang OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 282 (class 1259 OID 737131)
-- Name: wc; Type: TABLE; Schema: versions; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE wc (
    id integer NOT NULL,
    map_object_id integer NOT NULL,
    wc_accessibility_id integer,
    wc_cabin_access_id integer,
    wc_cabin_localization_id integer,
    wc_switch_id integer,
    wc_switch_height integer,
    wc_cabin_width integer,
    wc_cabin_depth integer,
    wc_flushing_back_height integer,
    wc_flushing_side_height integer,
    wc_flushing_side_distanc integer,
    wc_flushing_id integer,
    wc_flushing_difficulty_id integer,
    wc_handles_distance integer,
    wc_basin_left_distance integer,
    wc_basin_right_distance integer,
    wc_basin_back_indentation integer,
    wc_basin_seat_height integer,
    wc_basin_is_paper_reach boolean,
    wc_basin_space_id integer,
    wc_is_changingdesk boolean,
    wc_is_changingdesk_obstructs boolean,
    wc_changingdesk_id integer,
    wc_is_alarmbutton boolean,
    wc_alarmbutton_top_heigh integer,
    wc_alarmbutton_bottom_height integer,
    wc_is_regular_w_c boolean,
    wc_is_regular_w_c_braille_marking boolean,
    wc_cabin_door_disposition_id integer,
    wc_cabin_w_c_basin_disposition_id integer,
    wc_cabin_wash_basin_disposition_id integer,
    hallway1_width integer,
    hallway1_depth integer,
    hallway1_door_width integer,
    hallway1_door_marking_id integer,
    hallway2_width integer,
    hallway2_depth integer,
    hallway2_door_width integer,
    hallway2_door_marking_id integer,
    handle1_type_id integer,
    handle1_height integer,
    handle1_length integer,
    handle2_type_id integer,
    handle2_height integer,
    handle2_length integer,
    door_width integer,
    door_opening_direction_id integer,
    door_is_marking boolean,
    door_handle_position_id integer,
    washbasin_height integer,
    washbasin_underpass_id integer,
    washbasin_is_handle boolean,
    tap_height integer,
    tap_type_id integer,
    washbasin_handle_type_id integer,
    washbasin_handle_height integer,
    washbasin_handle_length integer
);


ALTER TABLE wc OWNER TO mapy_pristupnosti_db_01;

--
-- TOC entry 283 (class 1259 OID 737231)
-- Name: wc_lang; Type: TABLE; Schema: versions; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE TABLE wc_lang (
    wc_id integer NOT NULL,
    lang_id character(2) NOT NULL,
    wc_localization character varying(255),
    wc_has_notes text,
    wc_has_description text,
    wc_access_provided_by character varying(255)
);


ALTER TABLE wc_lang OWNER TO mapy_pristupnosti_db_01;

SET search_path = public, pg_catalog;

--
-- TOC entry 2602 (class 2604 OID 736184)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY a_o_b_announcement ALTER COLUMN id SET DEFAULT nextval('a_o_b_announcement_id_seq'::regclass);


--
-- TOC entry 2568 (class 2604 OID 731150)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY accessibility ALTER COLUMN id SET DEFAULT nextval('accessibility_id_seq'::regclass);


--
-- TOC entry 2588 (class 2604 OID 736072)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY bell_type ALTER COLUMN id SET DEFAULT nextval('bell_type_id_seq'::regclass);


--
-- TOC entry 2630 (class 2604 OID 819681)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY contrast_marking_localization ALTER COLUMN id SET DEFAULT nextval('contrast_marking_localization_id_seq'::regclass);


--
-- TOC entry 2590 (class 2604 OID 736088)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY door_opening ALTER COLUMN id SET DEFAULT nextval('door_opening_id_seq'::regclass);


--
-- TOC entry 2591 (class 2604 OID 736096)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY door_opening_direction ALTER COLUMN id SET DEFAULT nextval('door_opening_direction_id_seq'::regclass);


--
-- TOC entry 2589 (class 2604 OID 736080)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY door_type ALTER COLUMN id SET DEFAULT nextval('door_type_id_seq'::regclass);


--
-- TOC entry 2582 (class 2604 OID 731391)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY elevator ALTER COLUMN id SET DEFAULT nextval('elevator_id_seq'::regclass);


--
-- TOC entry 2604 (class 2604 OID 736200)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY elevator_cage_mirror_localization ALTER COLUMN id SET DEFAULT nextval('elevator_cage_mirror_localization_id_seq'::regclass);


--
-- TOC entry 2603 (class 2604 OID 736192)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY elevator_cage_seconddoor_localization ALTER COLUMN id SET DEFAULT nextval('elevator_cage_seconddoor_localization_id_seq'::regclass);


--
-- TOC entry 2601 (class 2604 OID 736176)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY elevator_control_flat_marking ALTER COLUMN id SET DEFAULT nextval('elevator_control_flat_marking_id_seq'::regclass);


--
-- TOC entry 2600 (class 2604 OID 736168)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY elevator_control_relief_marking ALTER COLUMN id SET DEFAULT nextval('elevator_control_relief_marking_id_seq'::regclass);


--
-- TOC entry 2599 (class 2604 OID 736160)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY elevator_driveoff ALTER COLUMN id SET DEFAULT nextval('elevator_driveoff_id_seq'::regclass);


--
-- TOC entry 2597 (class 2604 OID 736144)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY elevator_handle_localization ALTER COLUMN id SET DEFAULT nextval('elevator_handle_localization_id_seq'::regclass);


--
-- TOC entry 2598 (class 2604 OID 736152)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY elevator_type ALTER COLUMN id SET DEFAULT nextval('elevator_type_id_seq'::regclass);


--
-- TOC entry 2587 (class 2604 OID 736064)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY entrance_accessibility ALTER COLUMN id SET DEFAULT nextval('entrance_accessibility_id_seq'::regclass);


--
-- TOC entry 2586 (class 2604 OID 736056)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY entrance_guidingline ALTER COLUMN id SET DEFAULT nextval('entrance_guidingline_id_seq'::regclass);


--
-- TOC entry 2607 (class 2604 OID 736224)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY entryarea_entry ALTER COLUMN id SET DEFAULT nextval('entryarea_entry_id_seq'::regclass);


--
-- TOC entry 2626 (class 2604 OID 757650)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY exchange_source ALTER COLUMN id SET DEFAULT nextval('import_source_id_seq'::regclass);


--
-- TOC entry 2616 (class 2604 OID 736296)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY hallway_door_marking ALTER COLUMN id SET DEFAULT nextval('hallway_door_marking_id_seq'::regclass);


--
-- TOC entry 2617 (class 2604 OID 736304)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY handle_type ALTER COLUMN id SET DEFAULT nextval('handle_type_id_seq'::regclass);


--
-- TOC entry 2631 (class 2604 OID 878362)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY import ALTER COLUMN id SET DEFAULT nextval('import_id_seq'::regclass);


--
-- TOC entry 2636 (class 2604 OID 878384)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY import_log ALTER COLUMN id SET DEFAULT nextval('import_log_id_seq'::regclass);


--
-- TOC entry 2566 (class 2604 OID 731134)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY license ALTER COLUMN id SET DEFAULT nextval('license_id_seq'::regclass);


--
-- TOC entry 2639 (class 2604 OID 943165)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY log ALTER COLUMN id SET DEFAULT nextval('log_id_seq'::regclass);


--
-- TOC entry 2575 (class 2604 OID 731285)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object ALTER COLUMN id SET DEFAULT nextval('map_object_id_seq'::regclass);


--
-- TOC entry 2638 (class 2604 OID 892235)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object_draft ALTER COLUMN id SET DEFAULT nextval('map_object_draft_id_seq'::regclass);


--
-- TOC entry 2605 (class 2604 OID 736208)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY mappable_entity_access ALTER COLUMN id SET DEFAULT nextval('mappable_entity_access_id_seq'::regclass);


--
-- TOC entry 2585 (class 2604 OID 736048)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY object_interior_accessibility ALTER COLUMN id SET DEFAULT nextval('object_interior_accessibility_id_seq'::regclass);


--
-- TOC entry 2584 (class 2604 OID 736030)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY object_stairs_type ALTER COLUMN id SET DEFAULT nextval('object_stairs_type_id_seq'::regclass);


--
-- TOC entry 2567 (class 2604 OID 731142)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY object_type ALTER COLUMN id SET DEFAULT nextval('object_type_id_seq'::regclass);


--
-- TOC entry 2581 (class 2604 OID 731373)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY platform ALTER COLUMN id SET DEFAULT nextval('platform_id_seq'::regclass);


--
-- TOC entry 2606 (class 2604 OID 736216)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY platform_type ALTER COLUMN id SET DEFAULT nextval('platform_type_id_seq'::regclass);


--
-- TOC entry 2596 (class 2604 OID 736136)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY ramp_handle_localization ALTER COLUMN id SET DEFAULT nextval('ramp_handle_localization_id_seq'::regclass);


--
-- TOC entry 2592 (class 2604 OID 736104)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY ramp_skids_localization ALTER COLUMN id SET DEFAULT nextval('ramp_skids_localization_id_seq'::regclass);


--
-- TOC entry 2593 (class 2604 OID 736112)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY ramp_skids_mobility ALTER COLUMN id SET DEFAULT nextval('ramp_skids_mobility_id_seq'::regclass);


--
-- TOC entry 2595 (class 2604 OID 736128)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY ramp_surface ALTER COLUMN id SET DEFAULT nextval('ramp_surface_id_seq'::regclass);


--
-- TOC entry 2594 (class 2604 OID 736120)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY ramp_type ALTER COLUMN id SET DEFAULT nextval('ramp_type_id_seq'::regclass);


--
-- TOC entry 2580 (class 2604 OID 731355)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY rampskids ALTER COLUMN id SET DEFAULT nextval('rampskids_id_seq'::regclass);


--
-- TOC entry 2579 (class 2604 OID 731347)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY rampskids_platform_relation ALTER COLUMN id SET DEFAULT nextval('rampskids_platform_relation_id_seq'::regclass);


--
-- TOC entry 2569 (class 2604 OID 731158)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY role ALTER COLUMN id SET DEFAULT nextval('role_id_seq'::regclass);


--
-- TOC entry 2621 (class 2604 OID 736336)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY tap_type ALTER COLUMN id SET DEFAULT nextval('tap_type_id_seq'::regclass);


--
-- TOC entry 2570 (class 2604 OID 731166)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY "user" ALTER COLUMN id SET DEFAULT nextval('user_id_seq'::regclass);


--
-- TOC entry 2613 (class 2604 OID 736272)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY w_c_basin_space ALTER COLUMN id SET DEFAULT nextval('w_c_basin_space_id_seq'::regclass);


--
-- TOC entry 2615 (class 2604 OID 736288)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY w_c_cabin_disposition ALTER COLUMN id SET DEFAULT nextval('w_c_cabin_disposition_id_seq'::regclass);


--
-- TOC entry 2609 (class 2604 OID 736240)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY w_c_cabin_localization ALTER COLUMN id SET DEFAULT nextval('w_c_cabin_localization_id_seq'::regclass);


--
-- TOC entry 2608 (class 2604 OID 736232)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY w_c_categorization ALTER COLUMN id SET DEFAULT nextval('w_c_categorization_m_k_p_o_id_seq'::regclass);


--
-- TOC entry 2614 (class 2604 OID 736280)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY w_c_changingdesk ALTER COLUMN id SET DEFAULT nextval('w_c_changingdesk_id_seq'::regclass);


--
-- TOC entry 2619 (class 2604 OID 736320)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY w_c_door_handle_position ALTER COLUMN id SET DEFAULT nextval('w_c_door_handle_position_id_seq'::regclass);


--
-- TOC entry 2618 (class 2604 OID 736312)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY w_c_door_opening_direction ALTER COLUMN id SET DEFAULT nextval('w_c_door_opening_direction_id_seq'::regclass);


--
-- TOC entry 2611 (class 2604 OID 736256)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY w_c_flushing ALTER COLUMN id SET DEFAULT nextval('w_c_flushing_id_seq'::regclass);


--
-- TOC entry 2612 (class 2604 OID 736264)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY w_c_flushing_difficulty ALTER COLUMN id SET DEFAULT nextval('w_c_flushing_difficulty_id_seq'::regclass);


--
-- TOC entry 2610 (class 2604 OID 736248)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY w_c_switch ALTER COLUMN id SET DEFAULT nextval('w_c_switch_id_seq'::regclass);


--
-- TOC entry 2627 (class 2604 OID 806903)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY washbasin_handle_type ALTER COLUMN id SET DEFAULT nextval('washbasin_handle_type_id_seq'::regclass);


--
-- TOC entry 2620 (class 2604 OID 736328)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY washbasin_underpass ALTER COLUMN id SET DEFAULT nextval('washbasin_underpass_id_seq'::regclass);


--
-- TOC entry 2583 (class 2604 OID 731404)
-- Name: id; Type: DEFAULT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc ALTER COLUMN id SET DEFAULT nextval('wc_id_seq'::regclass);


SET search_path = service, pg_catalog;

--
-- TOC entry 2628 (class 2604 OID 819349)
-- Name: id; Type: DEFAULT; Schema: service; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY api_quota ALTER COLUMN id SET DEFAULT nextval('api_quota_id_seq'::regclass);


--
-- TOC entry 2624 (class 2604 OID 757493)
-- Name: id; Type: DEFAULT; Schema: service; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY geocoding_request ALTER COLUMN id SET DEFAULT nextval('geocoding_request_id_seq'::regclass);


SET search_path = public, pg_catalog;

--
-- TOC entry 2712 (class 2606 OID 736186)
-- Name: a_o_b_announcement_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY a_o_b_announcement
    ADD CONSTRAINT a_o_b_announcement_pk PRIMARY KEY (id);


--
-- TOC entry 2648 (class 2606 OID 731152)
-- Name: accessibility_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY accessibility
    ADD CONSTRAINT accessibility_pk PRIMARY KEY (id);


--
-- TOC entry 2684 (class 2606 OID 736074)
-- Name: bell_type_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY bell_type
    ADD CONSTRAINT bell_type_pk PRIMARY KEY (id);


--
-- TOC entry 2793 (class 2606 OID 819686)
-- Name: contrast_marking_localization_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY contrast_marking_localization
    ADD CONSTRAINT contrast_marking_localization_pk PRIMARY KEY (id);


--
-- TOC entry 2690 (class 2606 OID 736098)
-- Name: door_opening_direction_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY door_opening_direction
    ADD CONSTRAINT door_opening_direction_pk PRIMARY KEY (id);


--
-- TOC entry 2688 (class 2606 OID 736090)
-- Name: door_opening_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY door_opening
    ADD CONSTRAINT door_opening_pk PRIMARY KEY (id);


--
-- TOC entry 2686 (class 2606 OID 736082)
-- Name: door_type_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY door_type
    ADD CONSTRAINT door_type_pk PRIMARY KEY (id);


--
-- TOC entry 2716 (class 2606 OID 736202)
-- Name: elevator_cage_mirror_localization_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY elevator_cage_mirror_localization
    ADD CONSTRAINT elevator_cage_mirror_localization_pk PRIMARY KEY (id);


--
-- TOC entry 2714 (class 2606 OID 736194)
-- Name: elevator_cage_seconddoor_localization_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY elevator_cage_seconddoor_localization
    ADD CONSTRAINT elevator_cage_seconddoor_localization_pk PRIMARY KEY (id);


--
-- TOC entry 2710 (class 2606 OID 736178)
-- Name: elevator_control_flat_marking_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY elevator_control_flat_marking
    ADD CONSTRAINT elevator_control_flat_marking_pk PRIMARY KEY (id);


--
-- TOC entry 2708 (class 2606 OID 736170)
-- Name: elevator_control_relief_marking_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY elevator_control_relief_marking
    ADD CONSTRAINT elevator_control_relief_marking_pk PRIMARY KEY (id);


--
-- TOC entry 2706 (class 2606 OID 736162)
-- Name: elevator_driveoff_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY elevator_driveoff
    ADD CONSTRAINT elevator_driveoff_pk PRIMARY KEY (id);


--
-- TOC entry 2702 (class 2606 OID 736146)
-- Name: elevator_handle_localization_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY elevator_handle_localization
    ADD CONSTRAINT elevator_handle_localization_pk PRIMARY KEY (id);


--
-- TOC entry 2774 (class 2606 OID 737341)
-- Name: elevator_lang_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY elevator_lang
    ADD CONSTRAINT elevator_lang_pk PRIMARY KEY (elevator_id, lang_id);


--
-- TOC entry 2672 (class 2606 OID 731393)
-- Name: elevator_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY elevator
    ADD CONSTRAINT elevator_pk PRIMARY KEY (id);


--
-- TOC entry 2704 (class 2606 OID 736154)
-- Name: elevator_type_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY elevator_type
    ADD CONSTRAINT elevator_type_pk PRIMARY KEY (id);


--
-- TOC entry 2682 (class 2606 OID 736066)
-- Name: entrance_accessibility_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY entrance_accessibility
    ADD CONSTRAINT entrance_accessibility_pk PRIMARY KEY (id);


--
-- TOC entry 2680 (class 2606 OID 736058)
-- Name: entrance_guidingline_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY entrance_guidingline
    ADD CONSTRAINT entrance_guidingline_pk PRIMARY KEY (id);


--
-- TOC entry 2722 (class 2606 OID 736226)
-- Name: entryarea_entry_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY entryarea_entry
    ADD CONSTRAINT entryarea_entry_pk PRIMARY KEY (id);


--
-- TOC entry 2740 (class 2606 OID 736298)
-- Name: hallway_door_marking_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY hallway_door_marking
    ADD CONSTRAINT hallway_door_marking_pk PRIMARY KEY (id);


--
-- TOC entry 2742 (class 2606 OID 736306)
-- Name: handle_type_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY handle_type
    ADD CONSTRAINT handle_type_pk PRIMARY KEY (id);


--
-- TOC entry 2797 (class 2606 OID 878387)
-- Name: import_log_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY import_log
    ADD CONSTRAINT import_log_pk PRIMARY KEY (id);


--
-- TOC entry 2795 (class 2606 OID 878367)
-- Name: import_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY import
    ADD CONSTRAINT import_pk PRIMARY KEY (id);


--
-- TOC entry 2782 (class 2606 OID 757746)
-- Name: import_source_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY exchange_source
    ADD CONSTRAINT import_source_pk PRIMARY KEY (id);


--
-- TOC entry 2642 (class 2606 OID 731128)
-- Name: lang_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY lang
    ADD CONSTRAINT lang_pk PRIMARY KEY (id);


--
-- TOC entry 2644 (class 2606 OID 731136)
-- Name: license_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY license
    ADD CONSTRAINT license_pk PRIMARY KEY (id);


--
-- TOC entry 2801 (class 2606 OID 943171)
-- Name: log_pkey; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY log
    ADD CONSTRAINT log_pkey PRIMARY KEY (id);


--
-- TOC entry 2799 (class 2606 OID 892240)
-- Name: map_object_draft_pkey; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY map_object_draft
    ADD CONSTRAINT map_object_draft_pkey PRIMARY KEY (id);


--
-- TOC entry 2664 (class 2606 OID 731330)
-- Name: map_object_lang_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY map_object_lang
    ADD CONSTRAINT map_object_lang_pk PRIMARY KEY (map_object_id, lang_id);


--
-- TOC entry 2660 (class 2606 OID 731293)
-- Name: map_object_object_id_unique; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_object_id_unique UNIQUE (object_id);


--
-- TOC entry 2662 (class 2606 OID 731291)
-- Name: map_object_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_pk PRIMARY KEY (id);


--
-- TOC entry 2718 (class 2606 OID 736210)
-- Name: mappable_entity_access_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY mappable_entity_access
    ADD CONSTRAINT mappable_entity_access_pk PRIMARY KEY (id);


--
-- TOC entry 2678 (class 2606 OID 736050)
-- Name: object_interior_accessibility_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY object_interior_accessibility
    ADD CONSTRAINT object_interior_accessibility_pk PRIMARY KEY (id);


--
-- TOC entry 2676 (class 2606 OID 736032)
-- Name: object_stairs_type_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY object_stairs_type
    ADD CONSTRAINT object_stairs_type_pk PRIMARY KEY (id);


--
-- TOC entry 2646 (class 2606 OID 731144)
-- Name: object_type_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY object_type
    ADD CONSTRAINT object_type_pk PRIMARY KEY (id);


--
-- TOC entry 2776 (class 2606 OID 737359)
-- Name: platform_lang_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY platform_lang
    ADD CONSTRAINT platform_lang_pk PRIMARY KEY (platform_id, lang_id);


--
-- TOC entry 2670 (class 2606 OID 731375)
-- Name: platform_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY platform
    ADD CONSTRAINT platform_pk PRIMARY KEY (id);


--
-- TOC entry 2720 (class 2606 OID 736218)
-- Name: platform_type_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY platform_type
    ADD CONSTRAINT platform_type_pk PRIMARY KEY (id);


--
-- TOC entry 2700 (class 2606 OID 736138)
-- Name: ramp_handle_localization_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY ramp_handle_localization
    ADD CONSTRAINT ramp_handle_localization_pk PRIMARY KEY (id);


--
-- TOC entry 2692 (class 2606 OID 736106)
-- Name: ramp_skids_localization_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY ramp_skids_localization
    ADD CONSTRAINT ramp_skids_localization_pk PRIMARY KEY (id);


--
-- TOC entry 2694 (class 2606 OID 736114)
-- Name: ramp_skids_mobility_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY ramp_skids_mobility
    ADD CONSTRAINT ramp_skids_mobility_pk PRIMARY KEY (id);


--
-- TOC entry 2698 (class 2606 OID 736130)
-- Name: ramp_surface_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY ramp_surface
    ADD CONSTRAINT ramp_surface_pk PRIMARY KEY (id);


--
-- TOC entry 2696 (class 2606 OID 736122)
-- Name: ramp_type_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY ramp_type
    ADD CONSTRAINT ramp_type_pk PRIMARY KEY (id);


--
-- TOC entry 2778 (class 2606 OID 737377)
-- Name: rampskids_lang_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY rampskids_lang
    ADD CONSTRAINT rampskids_lang_pk PRIMARY KEY (rampskids_id, lang_id);


--
-- TOC entry 2668 (class 2606 OID 731357)
-- Name: rampskids_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY rampskids
    ADD CONSTRAINT rampskids_pk PRIMARY KEY (id);


--
-- TOC entry 2666 (class 2606 OID 731349)
-- Name: rampskids_platform_relation_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY rampskids_platform_relation
    ADD CONSTRAINT rampskids_platform_relation_pk PRIMARY KEY (id);


--
-- TOC entry 2650 (class 2606 OID 731160)
-- Name: role_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY role
    ADD CONSTRAINT role_pk PRIMARY KEY (id);


--
-- TOC entry 2803 (class 2606 OID 943688)
-- Name: ruian_city_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY ruian_city
    ADD CONSTRAINT ruian_city_pk PRIMARY KEY (zipcode, city, city_part);


--
-- TOC entry 2788 (class 2606 OID 807252)
-- Name: ruian_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY ruian
    ADD CONSTRAINT ruian_pk PRIMARY KEY (id);


--
-- TOC entry 2750 (class 2606 OID 736338)
-- Name: tap_type_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY tap_type
    ADD CONSTRAINT tap_type_pk PRIMARY KEY (id);


--
-- TOC entry 2654 (class 2606 OID 731175)
-- Name: user_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY "user"
    ADD CONSTRAINT user_pk PRIMARY KEY (id);


--
-- TOC entry 2734 (class 2606 OID 736274)
-- Name: w_c_basin_space_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY w_c_basin_space
    ADD CONSTRAINT w_c_basin_space_pk PRIMARY KEY (id);


--
-- TOC entry 2738 (class 2606 OID 736290)
-- Name: w_c_cabin_disposition_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY w_c_cabin_disposition
    ADD CONSTRAINT w_c_cabin_disposition_pk PRIMARY KEY (id);


--
-- TOC entry 2726 (class 2606 OID 736242)
-- Name: w_c_cabin_localization_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY w_c_cabin_localization
    ADD CONSTRAINT w_c_cabin_localization_pk PRIMARY KEY (id);


--
-- TOC entry 2724 (class 2606 OID 736234)
-- Name: w_c_categorization_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY w_c_categorization
    ADD CONSTRAINT w_c_categorization_pk PRIMARY KEY (id);


--
-- TOC entry 2736 (class 2606 OID 736282)
-- Name: w_c_changingdesk_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY w_c_changingdesk
    ADD CONSTRAINT w_c_changingdesk_pk PRIMARY KEY (id);


--
-- TOC entry 2746 (class 2606 OID 736322)
-- Name: w_c_door_handle_position_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY w_c_door_handle_position
    ADD CONSTRAINT w_c_door_handle_position_pk PRIMARY KEY (id);


--
-- TOC entry 2744 (class 2606 OID 736314)
-- Name: w_c_door_opening_direction_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY w_c_door_opening_direction
    ADD CONSTRAINT w_c_door_opening_direction_pk PRIMARY KEY (id);


--
-- TOC entry 2732 (class 2606 OID 736266)
-- Name: w_c_flushing_difficulty_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY w_c_flushing_difficulty
    ADD CONSTRAINT w_c_flushing_difficulty_pk PRIMARY KEY (id);


--
-- TOC entry 2730 (class 2606 OID 736258)
-- Name: w_c_flushing_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY w_c_flushing
    ADD CONSTRAINT w_c_flushing_pk PRIMARY KEY (id);


--
-- TOC entry 2728 (class 2606 OID 736250)
-- Name: w_c_switch_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY w_c_switch
    ADD CONSTRAINT w_c_switch_pk PRIMARY KEY (id);


--
-- TOC entry 2784 (class 2606 OID 806908)
-- Name: washbasin_handle_type_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY washbasin_handle_type
    ADD CONSTRAINT washbasin_handle_type_pk PRIMARY KEY (id);


--
-- TOC entry 2748 (class 2606 OID 736330)
-- Name: washbasin_underpass_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY washbasin_underpass
    ADD CONSTRAINT washbasin_underpass_pk PRIMARY KEY (id);


--
-- TOC entry 2772 (class 2606 OID 737323)
-- Name: wc_lang_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY wc_lang
    ADD CONSTRAINT wc_lang_pk PRIMARY KEY (wc_id, lang_id);


--
-- TOC entry 2674 (class 2606 OID 731406)
-- Name: wc_pk; Type: CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_pk PRIMARY KEY (id);


SET search_path = service, pg_catalog;

--
-- TOC entry 2790 (class 2606 OID 819355)
-- Name: api_quota_id_pk; Type: CONSTRAINT; Schema: service; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY api_quota
    ADD CONSTRAINT api_quota_id_pk PRIMARY KEY (id);


--
-- TOC entry 2780 (class 2606 OID 757499)
-- Name: geocoding_request_pk; Type: CONSTRAINT; Schema: service; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY geocoding_request
    ADD CONSTRAINT geocoding_request_pk PRIMARY KEY (id);


SET search_path = versions, pg_catalog;

--
-- TOC entry 2766 (class 2606 OID 737120)
-- Name: elevator_lang_pk; Type: CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY elevator_lang
    ADD CONSTRAINT elevator_lang_pk PRIMARY KEY (elevator_id, lang_id);


--
-- TOC entry 2764 (class 2606 OID 737042)
-- Name: elevator_pk; Type: CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY elevator
    ADD CONSTRAINT elevator_pk PRIMARY KEY (id);


--
-- TOC entry 2754 (class 2606 OID 736894)
-- Name: map_object_lang_pk; Type: CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY map_object_lang
    ADD CONSTRAINT map_object_lang_pk PRIMARY KEY (map_object_id, lang_id);


--
-- TOC entry 2752 (class 2606 OID 736753)
-- Name: map_object_pk; Type: CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_pk PRIMARY KEY (id);


--
-- TOC entry 2762 (class 2606 OID 737027)
-- Name: platform_lang_pk; Type: CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY platform_lang
    ADD CONSTRAINT platform_lang_pk PRIMARY KEY (platform_id, lang_id);


--
-- TOC entry 2760 (class 2606 OID 736979)
-- Name: platform_pk; Type: CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY platform
    ADD CONSTRAINT platform_pk PRIMARY KEY (id);


--
-- TOC entry 2758 (class 2606 OID 736964)
-- Name: rampskids_lang_pk; Type: CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY rampskids_lang
    ADD CONSTRAINT rampskids_lang_pk PRIMARY KEY (rampskids_id, lang_id);


--
-- TOC entry 2756 (class 2606 OID 736911)
-- Name: rampskids_pk; Type: CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY rampskids
    ADD CONSTRAINT rampskids_pk PRIMARY KEY (id);


--
-- TOC entry 2770 (class 2606 OID 737238)
-- Name: wc_lang_pk; Type: CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY wc_lang
    ADD CONSTRAINT wc_lang_pk PRIMARY KEY (wc_id, lang_id);


--
-- TOC entry 2768 (class 2606 OID 737135)
-- Name: wc_pk; Type: CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_pk PRIMARY KEY (id);


SET search_path = public, pg_catalog;

--
-- TOC entry 2655 (class 1259 OID 737448)
-- Name: i_map_object_accessibility; Type: INDEX; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE INDEX i_map_object_accessibility ON map_object USING btree (accessibility_id);


--
-- TOC entry 2656 (class 1259 OID 737449)
-- Name: i_map_object_mapping_date_certified; Type: INDEX; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE INDEX i_map_object_mapping_date_certified ON map_object USING btree (mapping_date, certified);


--
-- TOC entry 2657 (class 1259 OID 737447)
-- Name: i_map_object_object_type; Type: INDEX; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE INDEX i_map_object_object_type ON map_object USING btree (object_type_id);


--
-- TOC entry 2658 (class 1259 OID 757752)
-- Name: i_map_object_source_id; Type: INDEX; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE INDEX i_map_object_source_id ON map_object USING btree (source_id);


--
-- TOC entry 2785 (class 1259 OID 946685)
-- Name: ruian_index_1; Type: INDEX; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE INDEX ruian_index_1 ON ruian USING btree (zipcode, city_part, street_desc_no);


--
-- TOC entry 2786 (class 1259 OID 946686)
-- Name: ruian_index_2; Type: INDEX; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE INDEX ruian_index_2 ON ruian USING btree (city);


--
-- TOC entry 2651 (class 1259 OID 834991)
-- Name: user_email_unique; Type: INDEX; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE UNIQUE INDEX user_email_unique ON "user" USING btree (email);


--
-- TOC entry 2652 (class 1259 OID 834992)
-- Name: user_login_unique; Type: INDEX; Schema: public; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE UNIQUE INDEX user_login_unique ON "user" USING btree (login);


SET search_path = service, pg_catalog;

--
-- TOC entry 2791 (class 1259 OID 819363)
-- Name: api_quota_ip_uindex; Type: INDEX; Schema: service; Owner: mapy_pristupnosti_db_01; Tablespace: 
--

CREATE UNIQUE INDEX api_quota_ip_uindex ON api_quota USING btree (ip);


SET search_path = public, pg_catalog;

--
-- TOC entry 3001 (class 2620 OID 731322)
-- Name: map_object_before_insert_update; Type: TRIGGER; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE TRIGGER map_object_before_insert_update BEFORE INSERT OR UPDATE ON map_object FOR EACH ROW EXECUTE PROCEDURE set_modified_before_insert_update();


--
-- TOC entry 3002 (class 2620 OID 996545)
-- Name: map_object_lang_prepare_search_title; Type: TRIGGER; Schema: public; Owner: mapy_pristupnosti_db_01
--

CREATE TRIGGER map_object_lang_prepare_search_title BEFORE INSERT OR UPDATE ON map_object_lang FOR EACH ROW EXECUTE PROCEDURE map_object_lang_prepare_search_title();

-- Trigger: map_object_after_modification on public.map_object
CREATE TRIGGER map_object_after_modification AFTER INSERT OR UPDATE OR DELETE OR TRUNCATE ON public.map_object FOR EACH STATEMENT EXECUTE PROCEDURE service.invalidate_markers_cache();

--
-- TOC entry 2858 (class 2606 OID 736571)
-- Name: elevator_door1_opening_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY elevator
    ADD CONSTRAINT elevator_door1_opening_id_fk FOREIGN KEY (door1_opening_id) REFERENCES door_opening(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2857 (class 2606 OID 736576)
-- Name: elevator_door2_opening_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY elevator
    ADD CONSTRAINT elevator_door2_opening_id_fk FOREIGN KEY (door2_opening_id) REFERENCES door_opening(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2862 (class 2606 OID 736551)
-- Name: elevator_elevator_a_o_b_announcements_scheme_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY elevator
    ADD CONSTRAINT elevator_elevator_a_o_b_announcements_scheme_id_fk FOREIGN KEY (elevator_a_o_b_announcements_scheme_id) REFERENCES a_o_b_announcement(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2869 (class 2606 OID 736516)
-- Name: elevator_elevator_access_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY elevator
    ADD CONSTRAINT elevator_elevator_access_id_fk FOREIGN KEY (elevator_access_id) REFERENCES mappable_entity_access(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2859 (class 2606 OID 736566)
-- Name: elevator_elevator_cage_mirror_localization_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY elevator
    ADD CONSTRAINT elevator_elevator_cage_mirror_localization_id_fk FOREIGN KEY (elevator_cage_mirror_localization_id) REFERENCES elevator_cage_mirror_localization(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2861 (class 2606 OID 736556)
-- Name: elevator_elevator_cage_seconddoor_localization_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY elevator
    ADD CONSTRAINT elevator_elevator_cage_seconddoor_localization_id_fk FOREIGN KEY (elevator_cage_seconddoor_localization_id) REFERENCES elevator_cage_seconddoor_localization(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2865 (class 2606 OID 736536)
-- Name: elevator_elevator_control1_flat_marking_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY elevator
    ADD CONSTRAINT elevator_elevator_control1_flat_marking_id_fk FOREIGN KEY (elevator_control1_flat_marking_id) REFERENCES elevator_control_flat_marking(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2866 (class 2606 OID 736531)
-- Name: elevator_elevator_control1_relief_marking_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY elevator
    ADD CONSTRAINT elevator_elevator_control1_relief_marking_id_fk FOREIGN KEY (elevator_control1_relief_marking_id) REFERENCES elevator_control_relief_marking(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2863 (class 2606 OID 736546)
-- Name: elevator_elevator_control2_flat_marking_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY elevator
    ADD CONSTRAINT elevator_elevator_control2_flat_marking_id_fk FOREIGN KEY (elevator_control2_flat_marking_id) REFERENCES elevator_control_flat_marking(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2864 (class 2606 OID 736541)
-- Name: elevator_elevator_control2_relief_marking_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY elevator
    ADD CONSTRAINT elevator_elevator_control2_relief_marking_id_fk FOREIGN KEY (elevator_control2_relief_marking_id) REFERENCES elevator_control_relief_marking(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2867 (class 2606 OID 736526)
-- Name: elevator_elevator_driveoff_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY elevator
    ADD CONSTRAINT elevator_elevator_driveoff_id_fk FOREIGN KEY (elevator_driveoff_id) REFERENCES elevator_driveoff(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2860 (class 2606 OID 736561)
-- Name: elevator_elevator_handle_localization_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY elevator
    ADD CONSTRAINT elevator_elevator_handle_localization_id_fk FOREIGN KEY (elevator_handle_localization_id) REFERENCES elevator_handle_localization(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2868 (class 2606 OID 736521)
-- Name: elevator_elevator_type_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY elevator
    ADD CONSTRAINT elevator_elevator_type_id_fk FOREIGN KEY (elevator_type_id) REFERENCES elevator_type(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2987 (class 2606 OID 1005339)
-- Name: elevator_lang_elevator_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY elevator_lang
    ADD CONSTRAINT elevator_lang_elevator_id_fk FOREIGN KEY (elevator_id) REFERENCES elevator(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2988 (class 2606 OID 737347)
-- Name: elevator_lang_lang_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY elevator_lang
    ADD CONSTRAINT elevator_lang_lang_id_fk FOREIGN KEY (lang_id) REFERENCES lang(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2856 (class 2606 OID 1005359)
-- Name: elevator_map_object_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY elevator
    ADD CONSTRAINT elevator_map_object_id_fk FOREIGN KEY (map_object_id) REFERENCES map_object(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2995 (class 2606 OID 942198)
-- Name: import_license_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY import
    ADD CONSTRAINT import_license_id_fk FOREIGN KEY (license_id) REFERENCES license(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2997 (class 2606 OID 878388)
-- Name: import_log_import_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY import_log
    ADD CONSTRAINT import_log_import_id_fk FOREIGN KEY (import_id) REFERENCES import(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2996 (class 2606 OID 878374)
-- Name: import_source_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY import
    ADD CONSTRAINT import_source_id_fk FOREIGN KEY (source_id) REFERENCES exchange_source(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2994 (class 2606 OID 946833)
-- Name: import_user_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY import
    ADD CONSTRAINT import_user_id_fk FOREIGN KEY (user_id) REFERENCES "user"(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 3000 (class 2606 OID 946823)
-- Name: log_user_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY log
    ADD CONSTRAINT log_user_fk FOREIGN KEY (user_id) REFERENCES "user"(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2835 (class 2606 OID 731304)
-- Name: map_object_accessibility_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_accessibility_id_fk FOREIGN KEY (accessibility_id) REFERENCES accessibility(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2998 (class 2606 OID 1005319)
-- Name: map_object_draft_map_object_object_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object_draft
    ADD CONSTRAINT map_object_draft_map_object_object_id_fk FOREIGN KEY (map_object_object_id) REFERENCES map_object(object_id) ON UPDATE CASCADE DEFERRABLE;


--
-- TOC entry 2999 (class 2606 OID 946828)
-- Name: map_object_draft_user_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object_draft
    ADD CONSTRAINT map_object_draft_user_id_fk FOREIGN KEY (user_id) REFERENCES "user"(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2829 (class 2606 OID 736365)
-- Name: map_object_entrance1_accessibility_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance1_accessibility_id_fk FOREIGN KEY (entrance1_accessibility_id) REFERENCES entrance_accessibility(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2827 (class 2606 OID 736375)
-- Name: map_object_entrance1_bell_type_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance1_bell_type_id_fk FOREIGN KEY (entrance1_bell_type_id) REFERENCES bell_type(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2810 (class 2606 OID 819687)
-- Name: map_object_entrance1_contrast_marking_localization_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance1_contrast_marking_localization_id_fk FOREIGN KEY (entrance1_contrast_marking_localization_id) REFERENCES contrast_marking_localization(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2821 (class 2606 OID 736405)
-- Name: map_object_entrance1_door1_opening_direction_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance1_door1_opening_direction_id_fk FOREIGN KEY (entrance1_door1_opening_direction_id) REFERENCES door_opening_direction(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2823 (class 2606 OID 736395)
-- Name: map_object_entrance1_door1_opening_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance1_door1_opening_id_fk FOREIGN KEY (entrance1_door1_opening_id) REFERENCES door_opening(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2825 (class 2606 OID 736385)
-- Name: map_object_entrance1_door1_type_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance1_door1_type_id_fk FOREIGN KEY (entrance1_door1_type_id) REFERENCES door_type(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2815 (class 2606 OID 736435)
-- Name: map_object_entrance1_door2_opening_direction_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance1_door2_opening_direction_id_fk FOREIGN KEY (entrance1_door2_opening_direction_id) REFERENCES door_opening_direction(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2817 (class 2606 OID 736425)
-- Name: map_object_entrance1_door2_opening_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance1_door2_opening_id_fk FOREIGN KEY (entrance1_door2_opening_id) REFERENCES door_opening(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2819 (class 2606 OID 736415)
-- Name: map_object_entrance1_door2_type_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance1_door2_type_id_fk FOREIGN KEY (entrance1_door2_type_id) REFERENCES door_type(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2831 (class 2606 OID 736355)
-- Name: map_object_entrance1_guidingline_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance1_guidingline_id_fk FOREIGN KEY (entrance1_guidingline_id) REFERENCES entrance_guidingline(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2813 (class 2606 OID 736445)
-- Name: map_object_entrance2_access_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance2_access_id_fk FOREIGN KEY (entrance2_access_id) REFERENCES mappable_entity_access(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2828 (class 2606 OID 736370)
-- Name: map_object_entrance2_accessibility_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance2_accessibility_id_fk FOREIGN KEY (entrance2_accessibility_id) REFERENCES entrance_accessibility(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2826 (class 2606 OID 736380)
-- Name: map_object_entrance2_bell_type_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance2_bell_type_id_fk FOREIGN KEY (entrance2_bell_type_id) REFERENCES bell_type(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2809 (class 2606 OID 819692)
-- Name: map_object_entrance2_contrast_marking_localization_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance2_contrast_marking_localization_id_fk FOREIGN KEY (entrance2_contrast_marking_localization_id) REFERENCES contrast_marking_localization(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2820 (class 2606 OID 736410)
-- Name: map_object_entrance2_door1_opening_direction_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance2_door1_opening_direction_id_fk FOREIGN KEY (entrance2_door1_opening_direction_id) REFERENCES door_opening_direction(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2822 (class 2606 OID 736400)
-- Name: map_object_entrance2_door1_opening_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance2_door1_opening_id_fk FOREIGN KEY (entrance2_door1_opening_id) REFERENCES door_opening(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2824 (class 2606 OID 736390)
-- Name: map_object_entrance2_door1_type_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance2_door1_type_id_fk FOREIGN KEY (entrance2_door1_type_id) REFERENCES door_type(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2814 (class 2606 OID 736440)
-- Name: map_object_entrance2_door2_opening_direction_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance2_door2_opening_direction_id_fk FOREIGN KEY (entrance2_door2_opening_direction_id) REFERENCES door_opening_direction(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2816 (class 2606 OID 736430)
-- Name: map_object_entrance2_door2_opening_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance2_door2_opening_id_fk FOREIGN KEY (entrance2_door2_opening_id) REFERENCES door_opening(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2818 (class 2606 OID 736420)
-- Name: map_object_entrance2_door2_type_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance2_door2_type_id_fk FOREIGN KEY (entrance2_door2_type_id) REFERENCES door_type(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2830 (class 2606 OID 736360)
-- Name: map_object_entrance2_guidingline_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance2_guidingline_id_fk FOREIGN KEY (entrance2_guidingline_id) REFERENCES entrance_guidingline(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2838 (class 2606 OID 731336)
-- Name: map_object_lang_lang_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object_lang
    ADD CONSTRAINT map_object_lang_lang_id_fk FOREIGN KEY (lang_id) REFERENCES lang(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2837 (class 2606 OID 1005364)
-- Name: map_object_lang_map_object_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object_lang
    ADD CONSTRAINT map_object_lang_map_object_id_fk FOREIGN KEY (map_object_id) REFERENCES map_object(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2834 (class 2606 OID 731309)
-- Name: map_object_license_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_license_id_fk FOREIGN KEY (license_id) REFERENCES license(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2808 (class 2606 OID 819697)
-- Name: map_object_object_contrast_marking_localization_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_object_contrast_marking_localization_id_fk FOREIGN KEY (object_contrast_marking_localization_id) REFERENCES contrast_marking_localization(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2832 (class 2606 OID 736350)
-- Name: map_object_object_interior_accessibility_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_object_interior_accessibility_id_fk FOREIGN KEY (object_interior_accessibility_id) REFERENCES object_interior_accessibility(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2833 (class 2606 OID 736345)
-- Name: map_object_object_stairs_type_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_object_stairs_type_id_fk FOREIGN KEY (object_stairs_type_id) REFERENCES object_stairs_type(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2836 (class 2606 OID 731299)
-- Name: map_object_object_type_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_object_type_id_fk FOREIGN KEY (object_type_id) REFERENCES object_type(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2812 (class 2606 OID 737441)
-- Name: map_object_parent_object_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_parent_object_id_fk FOREIGN KEY (parent_object_id) REFERENCES map_object(object_id) DEFERRABLE;


--
-- TOC entry 2811 (class 2606 OID 807258)
-- Name: map_object_ruian_address_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_ruian_address_fk FOREIGN KEY (ruian_address) REFERENCES ruian(id) ON UPDATE CASCADE DEFERRABLE;


--
-- TOC entry 2807 (class 2606 OID 946844)
-- Name: map_object_user_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_user_id_fk FOREIGN KEY (user_id) REFERENCES "user"(id) ON UPDATE CASCADE ON DELETE SET DEFAULT;


--
-- TOC entry 2852 (class 2606 OID 736501)
-- Name: platform_entryarea1_bell_type_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY platform
    ADD CONSTRAINT platform_entryarea1_bell_type_id_fk FOREIGN KEY (entryarea1_bell_type_id) REFERENCES bell_type(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2853 (class 2606 OID 736496)
-- Name: platform_entryarea1_entry_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY platform
    ADD CONSTRAINT platform_entryarea1_entry_id_fk FOREIGN KEY (entryarea1_entry_id) REFERENCES entryarea_entry(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2850 (class 2606 OID 736511)
-- Name: platform_entryarea2_bell_type_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY platform
    ADD CONSTRAINT platform_entryarea2_bell_type_id_fk FOREIGN KEY (entryarea2_bell_type_id) REFERENCES bell_type(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2851 (class 2606 OID 736506)
-- Name: platform_entryarea2_entry_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY platform
    ADD CONSTRAINT platform_entryarea2_entry_id_fk FOREIGN KEY (entryarea2_entry_id) REFERENCES entryarea_entry(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2990 (class 2606 OID 737360)
-- Name: platform_lang_lang_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY platform_lang
    ADD CONSTRAINT platform_lang_lang_id_fk FOREIGN KEY (lang_id) REFERENCES lang(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2989 (class 2606 OID 1005329)
-- Name: platform_lang_platform_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY platform_lang
    ADD CONSTRAINT platform_lang_platform_id_fk FOREIGN KEY (platform_id) REFERENCES platform(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2848 (class 2606 OID 1005354)
-- Name: platform_map_object_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY platform
    ADD CONSTRAINT platform_map_object_id_fk FOREIGN KEY (map_object_id) REFERENCES map_object(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2855 (class 2606 OID 736486)
-- Name: platform_platform_access_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY platform
    ADD CONSTRAINT platform_platform_access_id_fk FOREIGN KEY (platform_access_id) REFERENCES mappable_entity_access(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2849 (class 2606 OID 731381)
-- Name: platform_platform_relation_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY platform
    ADD CONSTRAINT platform_platform_relation_id_fk FOREIGN KEY (platform_relation_id) REFERENCES rampskids_platform_relation(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2854 (class 2606 OID 736491)
-- Name: platform_platform_type_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY platform
    ADD CONSTRAINT platform_platform_type_id_fk FOREIGN KEY (platform_type_id) REFERENCES platform_type(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2992 (class 2606 OID 737378)
-- Name: rampskids_lang_lang_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY rampskids_lang
    ADD CONSTRAINT rampskids_lang_lang_id_fk FOREIGN KEY (lang_id) REFERENCES lang(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2991 (class 2606 OID 1005334)
-- Name: rampskids_lang_rampskids_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY rampskids_lang
    ADD CONSTRAINT rampskids_lang_rampskids_id_fk FOREIGN KEY (rampskids_id) REFERENCES rampskids(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2839 (class 2606 OID 1005344)
-- Name: rampskids_map_object_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY rampskids
    ADD CONSTRAINT rampskids_map_object_id_fk FOREIGN KEY (map_object_id) REFERENCES map_object(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2843 (class 2606 OID 736470)
-- Name: rampskids_ramp_handle_orientation_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY rampskids
    ADD CONSTRAINT rampskids_ramp_handle_orientation_id_fk FOREIGN KEY (ramp_handle_orientation_id) REFERENCES ramp_handle_localization(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2847 (class 2606 OID 736450)
-- Name: rampskids_ramp_localization_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY rampskids
    ADD CONSTRAINT rampskids_ramp_localization_id_fk FOREIGN KEY (ramp_localization_id) REFERENCES ramp_skids_localization(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2846 (class 2606 OID 736455)
-- Name: rampskids_ramp_mobility_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY rampskids
    ADD CONSTRAINT rampskids_ramp_mobility_id_fk FOREIGN KEY (ramp_mobility_id) REFERENCES ramp_skids_mobility(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2840 (class 2606 OID 731363)
-- Name: rampskids_ramp_relation_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY rampskids
    ADD CONSTRAINT rampskids_ramp_relation_id_fk FOREIGN KEY (ramp_relation_id) REFERENCES rampskids_platform_relation(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2844 (class 2606 OID 736465)
-- Name: rampskids_ramp_surface_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY rampskids
    ADD CONSTRAINT rampskids_ramp_surface_id_fk FOREIGN KEY (ramp_surface_id) REFERENCES ramp_surface(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2845 (class 2606 OID 736460)
-- Name: rampskids_ramp_type_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY rampskids
    ADD CONSTRAINT rampskids_ramp_type_id_fk FOREIGN KEY (ramp_type_id) REFERENCES ramp_type(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2842 (class 2606 OID 736475)
-- Name: rampskids_skids_localization_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY rampskids
    ADD CONSTRAINT rampskids_skids_localization_id_fk FOREIGN KEY (skids_localization_id) REFERENCES ramp_skids_localization(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2841 (class 2606 OID 736480)
-- Name: rampskids_skids_mobility_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY rampskids
    ADD CONSTRAINT rampskids_skids_mobility_id_fk FOREIGN KEY (skids_mobility_id) REFERENCES ramp_skids_mobility(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2805 (class 2606 OID 943627)
-- Name: user_license_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY "user"
    ADD CONSTRAINT user_license_id_fk FOREIGN KEY (license_id) REFERENCES license(id) ON UPDATE CASCADE;


--
-- TOC entry 2804 (class 2606 OID 946838)
-- Name: user_parent_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY "user"
    ADD CONSTRAINT user_parent_id_fk FOREIGN KEY (parent_id) REFERENCES "user"(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- TOC entry 2806 (class 2606 OID 731176)
-- Name: user_role_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY "user"
    ADD CONSTRAINT user_role_id_fk FOREIGN KEY (role_id) REFERENCES role(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2876 (class 2606 OID 736656)
-- Name: wc_door_handle_position_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_door_handle_position_id_fk FOREIGN KEY (door_handle_position_id) REFERENCES w_c_door_handle_position(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2877 (class 2606 OID 736651)
-- Name: wc_door_opening_direction_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_door_opening_direction_id_fk FOREIGN KEY (door_opening_direction_id) REFERENCES w_c_door_opening_direction(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2881 (class 2606 OID 736631)
-- Name: wc_hallway1_door_marking_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_hallway1_door_marking_id_fk FOREIGN KEY (hallway1_door_marking_id) REFERENCES hallway_door_marking(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2880 (class 2606 OID 736636)
-- Name: wc_hallway2_door_marking_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_hallway2_door_marking_id_fk FOREIGN KEY (hallway2_door_marking_id) REFERENCES hallway_door_marking(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2879 (class 2606 OID 736641)
-- Name: wc_handle1_type_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_handle1_type_id_fk FOREIGN KEY (handle1_type_id) REFERENCES handle_type(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2878 (class 2606 OID 736646)
-- Name: wc_handle2_type_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_handle2_type_id_fk FOREIGN KEY (handle2_type_id) REFERENCES handle_type(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2986 (class 2606 OID 737324)
-- Name: wc_lang_lang_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc_lang
    ADD CONSTRAINT wc_lang_lang_id_fk FOREIGN KEY (lang_id) REFERENCES lang(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2985 (class 2606 OID 1005324)
-- Name: wc_lang_wc_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc_lang
    ADD CONSTRAINT wc_lang_wc_id_fk FOREIGN KEY (wc_id) REFERENCES wc(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2870 (class 2606 OID 1005349)
-- Name: wc_map_object_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_map_object_id_fk FOREIGN KEY (map_object_id) REFERENCES map_object(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2874 (class 2606 OID 736666)
-- Name: wc_tap_type_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_tap_type_id_fk FOREIGN KEY (tap_type_id) REFERENCES tap_type(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2871 (class 2606 OID 806953)
-- Name: wc_washbasin_handle_type_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_washbasin_handle_type_id_fk FOREIGN KEY (washbasin_handle_type_id) REFERENCES washbasin_handle_type(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2875 (class 2606 OID 736661)
-- Name: wc_washbasin_underpass_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_washbasin_underpass_id_fk FOREIGN KEY (washbasin_underpass_id) REFERENCES washbasin_underpass(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2873 (class 2606 OID 736581)
-- Name: wc_wc_accessibility_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_wc_accessibility_id_fk FOREIGN KEY (wc_accessibility_id) REFERENCES w_c_categorization(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2886 (class 2606 OID 736606)
-- Name: wc_wc_basin_space_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_wc_basin_space_id_fk FOREIGN KEY (wc_basin_space_id) REFERENCES w_c_basin_space(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2872 (class 2606 OID 786456)
-- Name: wc_wc_cabin_access_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_wc_cabin_access_id_fk FOREIGN KEY (wc_cabin_access_id) REFERENCES mappable_entity_access(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2884 (class 2606 OID 736616)
-- Name: wc_wc_cabin_door_disposition_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_wc_cabin_door_disposition_id_fk FOREIGN KEY (wc_cabin_door_disposition_id) REFERENCES w_c_cabin_disposition(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2890 (class 2606 OID 736586)
-- Name: wc_wc_cabin_localization_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_wc_cabin_localization_id_fk FOREIGN KEY (wc_cabin_localization_id) REFERENCES w_c_cabin_localization(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2883 (class 2606 OID 736621)
-- Name: wc_wc_cabin_w_c_basin_disposition_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_wc_cabin_w_c_basin_disposition_id_fk FOREIGN KEY (wc_cabin_w_c_basin_disposition_id) REFERENCES w_c_cabin_disposition(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2882 (class 2606 OID 736626)
-- Name: wc_wc_cabin_wash_basin_disposition_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_wc_cabin_wash_basin_disposition_id_fk FOREIGN KEY (wc_cabin_wash_basin_disposition_id) REFERENCES w_c_cabin_disposition(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2885 (class 2606 OID 736611)
-- Name: wc_wc_changingdesk_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_wc_changingdesk_id_fk FOREIGN KEY (wc_changingdesk_id) REFERENCES w_c_changingdesk(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2887 (class 2606 OID 736601)
-- Name: wc_wc_flushing_difficulty_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_wc_flushing_difficulty_id_fk FOREIGN KEY (wc_flushing_difficulty_id) REFERENCES w_c_flushing_difficulty(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2888 (class 2606 OID 736596)
-- Name: wc_wc_flushing_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_wc_flushing_id_fk FOREIGN KEY (wc_flushing_id) REFERENCES w_c_flushing(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2889 (class 2606 OID 736591)
-- Name: wc_wc_switch_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_wc_switch_id_fk FOREIGN KEY (wc_switch_id) REFERENCES w_c_switch(id) ON UPDATE CASCADE ON DELETE RESTRICT;


SET search_path = service, pg_catalog;

--
-- TOC entry 2993 (class 2606 OID 757500)
-- Name: geocoding_request_object_id_fk; Type: FK CONSTRAINT; Schema: service; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY geocoding_request
    ADD CONSTRAINT geocoding_request_object_id_fk FOREIGN KEY (object_id) REFERENCES public.map_object(object_id) ON UPDATE CASCADE ON DELETE CASCADE;


SET search_path = versions, pg_catalog;

--
-- TOC entry 2959 (class 2606 OID 737043)
-- Name: elevator_door1_opening_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY elevator
    ADD CONSTRAINT elevator_door1_opening_id_fk FOREIGN KEY (door1_opening_id) REFERENCES public.door_opening(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2958 (class 2606 OID 737048)
-- Name: elevator_door2_opening_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY elevator
    ADD CONSTRAINT elevator_door2_opening_id_fk FOREIGN KEY (door2_opening_id) REFERENCES public.door_opening(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2957 (class 2606 OID 737053)
-- Name: elevator_elevator_a_o_b_announcements_scheme_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY elevator
    ADD CONSTRAINT elevator_elevator_a_o_b_announcements_scheme_id_fk FOREIGN KEY (elevator_a_o_b_announcements_scheme_id) REFERENCES public.a_o_b_announcement(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2956 (class 2606 OID 737058)
-- Name: elevator_elevator_access_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY elevator
    ADD CONSTRAINT elevator_elevator_access_id_fk FOREIGN KEY (elevator_access_id) REFERENCES public.mappable_entity_access(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2955 (class 2606 OID 737063)
-- Name: elevator_elevator_cage_mirror_localization_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY elevator
    ADD CONSTRAINT elevator_elevator_cage_mirror_localization_id_fk FOREIGN KEY (elevator_cage_mirror_localization_id) REFERENCES public.elevator_cage_mirror_localization(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2954 (class 2606 OID 737068)
-- Name: elevator_elevator_cage_seconddoor_localization_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY elevator
    ADD CONSTRAINT elevator_elevator_cage_seconddoor_localization_id_fk FOREIGN KEY (elevator_cage_seconddoor_localization_id) REFERENCES public.elevator_cage_seconddoor_localization(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2953 (class 2606 OID 737073)
-- Name: elevator_elevator_control1_flat_marking_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY elevator
    ADD CONSTRAINT elevator_elevator_control1_flat_marking_id_fk FOREIGN KEY (elevator_control1_flat_marking_id) REFERENCES public.elevator_control_flat_marking(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2952 (class 2606 OID 737078)
-- Name: elevator_elevator_control1_relief_marking_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY elevator
    ADD CONSTRAINT elevator_elevator_control1_relief_marking_id_fk FOREIGN KEY (elevator_control1_relief_marking_id) REFERENCES public.elevator_control_relief_marking(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2951 (class 2606 OID 737083)
-- Name: elevator_elevator_control2_flat_marking_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY elevator
    ADD CONSTRAINT elevator_elevator_control2_flat_marking_id_fk FOREIGN KEY (elevator_control2_flat_marking_id) REFERENCES public.elevator_control_flat_marking(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2950 (class 2606 OID 737088)
-- Name: elevator_elevator_control2_relief_marking_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY elevator
    ADD CONSTRAINT elevator_elevator_control2_relief_marking_id_fk FOREIGN KEY (elevator_control2_relief_marking_id) REFERENCES public.elevator_control_relief_marking(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2949 (class 2606 OID 737093)
-- Name: elevator_elevator_driveoff_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY elevator
    ADD CONSTRAINT elevator_elevator_driveoff_id_fk FOREIGN KEY (elevator_driveoff_id) REFERENCES public.elevator_driveoff(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2948 (class 2606 OID 737098)
-- Name: elevator_elevator_handle_localization_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY elevator
    ADD CONSTRAINT elevator_elevator_handle_localization_id_fk FOREIGN KEY (elevator_handle_localization_id) REFERENCES public.elevator_handle_localization(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2947 (class 2606 OID 737103)
-- Name: elevator_elevator_type_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY elevator
    ADD CONSTRAINT elevator_elevator_type_id_fk FOREIGN KEY (elevator_type_id) REFERENCES public.elevator_type(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2960 (class 2606 OID 1005289)
-- Name: elevator_lang_elevator_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY elevator_lang
    ADD CONSTRAINT elevator_lang_elevator_id_fk FOREIGN KEY (elevator_id) REFERENCES elevator(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2961 (class 2606 OID 737126)
-- Name: elevator_lang_lang_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY elevator_lang
    ADD CONSTRAINT elevator_lang_lang_id_fk FOREIGN KEY (lang_id) REFERENCES public.lang(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2946 (class 2606 OID 1005309)
-- Name: elevator_map_object_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY elevator
    ADD CONSTRAINT elevator_map_object_id_fk FOREIGN KEY (map_object_id) REFERENCES map_object(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2922 (class 2606 OID 736754)
-- Name: map_object_accessibility_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_accessibility_id_fk FOREIGN KEY (accessibility_id) REFERENCES public.accessibility(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2921 (class 2606 OID 736759)
-- Name: map_object_entrance1_accessibility_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance1_accessibility_id_fk FOREIGN KEY (entrance1_accessibility_id) REFERENCES public.entrance_accessibility(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2920 (class 2606 OID 736764)
-- Name: map_object_entrance1_bell_type_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance1_bell_type_id_fk FOREIGN KEY (entrance1_bell_type_id) REFERENCES public.bell_type(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2895 (class 2606 OID 819702)
-- Name: map_object_entrance1_contrast_marking_localization_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance1_contrast_marking_localization_id_fk FOREIGN KEY (entrance1_contrast_marking_localization_id) REFERENCES public.contrast_marking_localization(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2919 (class 2606 OID 736769)
-- Name: map_object_entrance1_door1_opening_direction_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance1_door1_opening_direction_id_fk FOREIGN KEY (entrance1_door1_opening_direction_id) REFERENCES public.door_opening_direction(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2918 (class 2606 OID 736774)
-- Name: map_object_entrance1_door1_opening_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance1_door1_opening_id_fk FOREIGN KEY (entrance1_door1_opening_id) REFERENCES public.door_opening(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2917 (class 2606 OID 736779)
-- Name: map_object_entrance1_door1_type_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance1_door1_type_id_fk FOREIGN KEY (entrance1_door1_type_id) REFERENCES public.door_type(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2916 (class 2606 OID 736784)
-- Name: map_object_entrance1_door2_opening_direction_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance1_door2_opening_direction_id_fk FOREIGN KEY (entrance1_door2_opening_direction_id) REFERENCES public.door_opening_direction(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2915 (class 2606 OID 736789)
-- Name: map_object_entrance1_door2_opening_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance1_door2_opening_id_fk FOREIGN KEY (entrance1_door2_opening_id) REFERENCES public.door_opening(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2914 (class 2606 OID 736794)
-- Name: map_object_entrance1_door2_type_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance1_door2_type_id_fk FOREIGN KEY (entrance1_door2_type_id) REFERENCES public.door_type(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2913 (class 2606 OID 736799)
-- Name: map_object_entrance1_guidingline_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance1_guidingline_id_fk FOREIGN KEY (entrance1_guidingline_id) REFERENCES public.entrance_guidingline(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2912 (class 2606 OID 736804)
-- Name: map_object_entrance2_access_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance2_access_id_fk FOREIGN KEY (entrance2_access_id) REFERENCES public.mappable_entity_access(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2911 (class 2606 OID 736809)
-- Name: map_object_entrance2_accessibility_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance2_accessibility_id_fk FOREIGN KEY (entrance2_accessibility_id) REFERENCES public.entrance_accessibility(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2910 (class 2606 OID 736814)
-- Name: map_object_entrance2_bell_type_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance2_bell_type_id_fk FOREIGN KEY (entrance2_bell_type_id) REFERENCES public.bell_type(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2894 (class 2606 OID 819707)
-- Name: map_object_entrance2_contrast_marking_localization_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance2_contrast_marking_localization_id_fk FOREIGN KEY (entrance2_contrast_marking_localization_id) REFERENCES public.contrast_marking_localization(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2909 (class 2606 OID 736819)
-- Name: map_object_entrance2_door1_opening_direction_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance2_door1_opening_direction_id_fk FOREIGN KEY (entrance2_door1_opening_direction_id) REFERENCES public.door_opening_direction(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2908 (class 2606 OID 736824)
-- Name: map_object_entrance2_door1_opening_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance2_door1_opening_id_fk FOREIGN KEY (entrance2_door1_opening_id) REFERENCES public.door_opening(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2907 (class 2606 OID 736829)
-- Name: map_object_entrance2_door1_type_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance2_door1_type_id_fk FOREIGN KEY (entrance2_door1_type_id) REFERENCES public.door_type(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2906 (class 2606 OID 736834)
-- Name: map_object_entrance2_door2_opening_direction_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance2_door2_opening_direction_id_fk FOREIGN KEY (entrance2_door2_opening_direction_id) REFERENCES public.door_opening_direction(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2905 (class 2606 OID 736839)
-- Name: map_object_entrance2_door2_opening_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance2_door2_opening_id_fk FOREIGN KEY (entrance2_door2_opening_id) REFERENCES public.door_opening(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2904 (class 2606 OID 736844)
-- Name: map_object_entrance2_door2_type_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance2_door2_type_id_fk FOREIGN KEY (entrance2_door2_type_id) REFERENCES public.door_type(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2903 (class 2606 OID 736849)
-- Name: map_object_entrance2_guidingline_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_entrance2_guidingline_id_fk FOREIGN KEY (entrance2_guidingline_id) REFERENCES public.entrance_guidingline(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2924 (class 2606 OID 736895)
-- Name: map_object_lang_lang_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object_lang
    ADD CONSTRAINT map_object_lang_lang_id_fk FOREIGN KEY (lang_id) REFERENCES public.lang(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2923 (class 2606 OID 1005314)
-- Name: map_object_lang_map_object_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object_lang
    ADD CONSTRAINT map_object_lang_map_object_id_fk FOREIGN KEY (map_object_id) REFERENCES map_object(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2902 (class 2606 OID 736854)
-- Name: map_object_license_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_license_id_fk FOREIGN KEY (license_id) REFERENCES public.license(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2893 (class 2606 OID 819712)
-- Name: map_object_object_contrast_marking_localization_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_object_contrast_marking_localization_id_fk FOREIGN KEY (object_contrast_marking_localization_id) REFERENCES public.contrast_marking_localization(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2891 (class 2606 OID 1005269)
-- Name: map_object_object_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_object_id_fk FOREIGN KEY (object_id) REFERENCES public.map_object(object_id) ON UPDATE CASCADE DEFERRABLE;


--
-- TOC entry 2901 (class 2606 OID 736859)
-- Name: map_object_object_interior_accessibility_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_object_interior_accessibility_id_fk FOREIGN KEY (object_interior_accessibility_id) REFERENCES public.object_interior_accessibility(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2900 (class 2606 OID 736864)
-- Name: map_object_object_stairs_type_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_object_stairs_type_id_fk FOREIGN KEY (object_stairs_type_id) REFERENCES public.object_stairs_type(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2899 (class 2606 OID 736869)
-- Name: map_object_object_type_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_object_type_id_fk FOREIGN KEY (object_type_id) REFERENCES public.object_type(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2897 (class 2606 OID 807263)
-- Name: map_object_parent_object_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_parent_object_id_fk FOREIGN KEY (parent_object_id) REFERENCES public.map_object(object_id) DEFERRABLE;


--
-- TOC entry 2896 (class 2606 OID 807268)
-- Name: map_object_ruian_address_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_ruian_address_fk FOREIGN KEY (ruian_address) REFERENCES public.ruian(id) ON UPDATE CASCADE DEFERRABLE;


--
-- TOC entry 2898 (class 2606 OID 786296)
-- Name: map_object_source_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_source_id_fk FOREIGN KEY (source_id) REFERENCES public.exchange_source(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2892 (class 2606 OID 946850)
-- Name: map_object_user_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY map_object
    ADD CONSTRAINT map_object_user_id_fk FOREIGN KEY (user_id) REFERENCES public."user"(id) ON UPDATE CASCADE ON DELETE SET DEFAULT;


--
-- TOC entry 2943 (class 2606 OID 736980)
-- Name: platform_entryarea1_bell_type_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY platform
    ADD CONSTRAINT platform_entryarea1_bell_type_id_fk FOREIGN KEY (entryarea1_bell_type_id) REFERENCES public.bell_type(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2942 (class 2606 OID 736985)
-- Name: platform_entryarea1_entry_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY platform
    ADD CONSTRAINT platform_entryarea1_entry_id_fk FOREIGN KEY (entryarea1_entry_id) REFERENCES public.entryarea_entry(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2941 (class 2606 OID 736990)
-- Name: platform_entryarea2_bell_type_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY platform
    ADD CONSTRAINT platform_entryarea2_bell_type_id_fk FOREIGN KEY (entryarea2_bell_type_id) REFERENCES public.bell_type(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2940 (class 2606 OID 736995)
-- Name: platform_entryarea2_entry_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY platform
    ADD CONSTRAINT platform_entryarea2_entry_id_fk FOREIGN KEY (entryarea2_entry_id) REFERENCES public.entryarea_entry(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2945 (class 2606 OID 737028)
-- Name: platform_lang_lang_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY platform_lang
    ADD CONSTRAINT platform_lang_lang_id_fk FOREIGN KEY (lang_id) REFERENCES public.lang(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2944 (class 2606 OID 1005279)
-- Name: platform_lang_platform_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY platform_lang
    ADD CONSTRAINT platform_lang_platform_id_fk FOREIGN KEY (platform_id) REFERENCES platform(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2936 (class 2606 OID 1005304)
-- Name: platform_map_object_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY platform
    ADD CONSTRAINT platform_map_object_id_fk FOREIGN KEY (map_object_id) REFERENCES map_object(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2939 (class 2606 OID 737005)
-- Name: platform_platform_access_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY platform
    ADD CONSTRAINT platform_platform_access_id_fk FOREIGN KEY (platform_access_id) REFERENCES public.mappable_entity_access(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2937 (class 2606 OID 737015)
-- Name: platform_platform_relation_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY platform
    ADD CONSTRAINT platform_platform_relation_id_fk FOREIGN KEY (platform_relation_id) REFERENCES public.rampskids_platform_relation(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2938 (class 2606 OID 737010)
-- Name: platform_platform_type_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY platform
    ADD CONSTRAINT platform_platform_type_id_fk FOREIGN KEY (platform_type_id) REFERENCES public.platform_type(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2935 (class 2606 OID 736965)
-- Name: rampskids_lang_lang_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY rampskids_lang
    ADD CONSTRAINT rampskids_lang_lang_id_fk FOREIGN KEY (lang_id) REFERENCES public.lang(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2934 (class 2606 OID 1005284)
-- Name: rampskids_lang_rampskids_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY rampskids_lang
    ADD CONSTRAINT rampskids_lang_rampskids_id_fk FOREIGN KEY (rampskids_id) REFERENCES rampskids(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2925 (class 2606 OID 1005294)
-- Name: rampskids_map_object_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY rampskids
    ADD CONSTRAINT rampskids_map_object_id_fk FOREIGN KEY (map_object_id) REFERENCES map_object(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2933 (class 2606 OID 736917)
-- Name: rampskids_ramp_handle_orientation_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY rampskids
    ADD CONSTRAINT rampskids_ramp_handle_orientation_id_fk FOREIGN KEY (ramp_handle_orientation_id) REFERENCES public.ramp_handle_localization(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2932 (class 2606 OID 736922)
-- Name: rampskids_ramp_localization_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY rampskids
    ADD CONSTRAINT rampskids_ramp_localization_id_fk FOREIGN KEY (ramp_localization_id) REFERENCES public.ramp_skids_localization(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2931 (class 2606 OID 736927)
-- Name: rampskids_ramp_mobility_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY rampskids
    ADD CONSTRAINT rampskids_ramp_mobility_id_fk FOREIGN KEY (ramp_mobility_id) REFERENCES public.ramp_skids_mobility(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2926 (class 2606 OID 736942)
-- Name: rampskids_ramp_relation_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY rampskids
    ADD CONSTRAINT rampskids_ramp_relation_id_fk FOREIGN KEY (ramp_relation_id) REFERENCES public.rampskids_platform_relation(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2930 (class 2606 OID 736932)
-- Name: rampskids_ramp_surface_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY rampskids
    ADD CONSTRAINT rampskids_ramp_surface_id_fk FOREIGN KEY (ramp_surface_id) REFERENCES public.ramp_surface(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2929 (class 2606 OID 736937)
-- Name: rampskids_ramp_type_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY rampskids
    ADD CONSTRAINT rampskids_ramp_type_id_fk FOREIGN KEY (ramp_type_id) REFERENCES public.ramp_type(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2928 (class 2606 OID 736947)
-- Name: rampskids_skids_localization_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY rampskids
    ADD CONSTRAINT rampskids_skids_localization_id_fk FOREIGN KEY (skids_localization_id) REFERENCES public.ramp_skids_localization(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2927 (class 2606 OID 736952)
-- Name: rampskids_skids_mobility_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY rampskids
    ADD CONSTRAINT rampskids_skids_mobility_id_fk FOREIGN KEY (skids_mobility_id) REFERENCES public.ramp_skids_mobility(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2982 (class 2606 OID 737136)
-- Name: wc_door_handle_position_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_door_handle_position_id_fk FOREIGN KEY (door_handle_position_id) REFERENCES public.w_c_door_handle_position(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2981 (class 2606 OID 737141)
-- Name: wc_door_opening_direction_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_door_opening_direction_id_fk FOREIGN KEY (door_opening_direction_id) REFERENCES public.w_c_door_opening_direction(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2980 (class 2606 OID 737146)
-- Name: wc_hallway1_door_marking_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_hallway1_door_marking_id_fk FOREIGN KEY (hallway1_door_marking_id) REFERENCES public.hallway_door_marking(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2979 (class 2606 OID 737151)
-- Name: wc_hallway2_door_marking_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_hallway2_door_marking_id_fk FOREIGN KEY (hallway2_door_marking_id) REFERENCES public.hallway_door_marking(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2978 (class 2606 OID 737156)
-- Name: wc_handle1_type_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_handle1_type_id_fk FOREIGN KEY (handle1_type_id) REFERENCES public.handle_type(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2977 (class 2606 OID 737161)
-- Name: wc_handle2_type_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_handle2_type_id_fk FOREIGN KEY (handle2_type_id) REFERENCES public.handle_type(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2984 (class 2606 OID 737239)
-- Name: wc_lang_lang_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc_lang
    ADD CONSTRAINT wc_lang_lang_id_fk FOREIGN KEY (lang_id) REFERENCES public.lang(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2983 (class 2606 OID 1005274)
-- Name: wc_lang_wc_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc_lang
    ADD CONSTRAINT wc_lang_wc_id_fk FOREIGN KEY (wc_id) REFERENCES wc(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2962 (class 2606 OID 1005299)
-- Name: wc_map_object_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_map_object_id_fk FOREIGN KEY (map_object_id) REFERENCES map_object(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2976 (class 2606 OID 737171)
-- Name: wc_tap_type_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_tap_type_id_fk FOREIGN KEY (tap_type_id) REFERENCES public.tap_type(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2963 (class 2606 OID 806940)
-- Name: wc_washbasin_handle_type_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_washbasin_handle_type_id_fk FOREIGN KEY (washbasin_handle_type_id) REFERENCES public.washbasin_handle_type(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2975 (class 2606 OID 737176)
-- Name: wc_washbasin_underpass_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_washbasin_underpass_id_fk FOREIGN KEY (washbasin_underpass_id) REFERENCES public.washbasin_underpass(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2965 (class 2606 OID 737181)
-- Name: wc_wc_accessibility_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_wc_accessibility_id_fk FOREIGN KEY (wc_accessibility_id) REFERENCES public.w_c_categorization(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2974 (class 2606 OID 737186)
-- Name: wc_wc_basin_space_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_wc_basin_space_id_fk FOREIGN KEY (wc_basin_space_id) REFERENCES public.w_c_basin_space(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2964 (class 2606 OID 786461)
-- Name: wc_wc_cabin_access_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_wc_cabin_access_id_fk FOREIGN KEY (wc_cabin_access_id) REFERENCES public.mappable_entity_access(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2973 (class 2606 OID 737191)
-- Name: wc_wc_cabin_door_disposition_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_wc_cabin_door_disposition_id_fk FOREIGN KEY (wc_cabin_door_disposition_id) REFERENCES public.w_c_cabin_disposition(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2972 (class 2606 OID 737196)
-- Name: wc_wc_cabin_localization_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_wc_cabin_localization_id_fk FOREIGN KEY (wc_cabin_localization_id) REFERENCES public.w_c_cabin_localization(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2971 (class 2606 OID 737201)
-- Name: wc_wc_cabin_w_c_basin_disposition_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_wc_cabin_w_c_basin_disposition_id_fk FOREIGN KEY (wc_cabin_w_c_basin_disposition_id) REFERENCES public.w_c_cabin_disposition(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2970 (class 2606 OID 737206)
-- Name: wc_wc_cabin_wash_basin_disposition_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_wc_cabin_wash_basin_disposition_id_fk FOREIGN KEY (wc_cabin_wash_basin_disposition_id) REFERENCES public.w_c_cabin_disposition(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2969 (class 2606 OID 737211)
-- Name: wc_wc_changingdesk_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_wc_changingdesk_id_fk FOREIGN KEY (wc_changingdesk_id) REFERENCES public.w_c_changingdesk(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2968 (class 2606 OID 737216)
-- Name: wc_wc_flushing_difficulty_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_wc_flushing_difficulty_id_fk FOREIGN KEY (wc_flushing_difficulty_id) REFERENCES public.w_c_flushing_difficulty(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2967 (class 2606 OID 737221)
-- Name: wc_wc_flushing_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_wc_flushing_id_fk FOREIGN KEY (wc_flushing_id) REFERENCES public.w_c_flushing(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 2966 (class 2606 OID 737226)
-- Name: wc_wc_switch_id_fk; Type: FK CONSTRAINT; Schema: versions; Owner: mapy_pristupnosti_db_01
--

ALTER TABLE ONLY wc
    ADD CONSTRAINT wc_wc_switch_id_fk FOREIGN KEY (wc_switch_id) REFERENCES public.w_c_switch(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- TOC entry 3163 (class 0 OID 0)
-- Dependencies: 6
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


-- Completed on 2016-02-16 09:30:14 CET

--
-- PostgreSQL database dump complete
--

