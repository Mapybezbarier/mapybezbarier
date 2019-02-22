ALTER TABLE public.map_object
    ADD accessibility_pensioners_id integer DEFAULT NULL
        CONSTRAINT map_object_accessibility_pensioners_id_fk
            REFERENCES accessibility ON
            UPDATE CASCADE ON
            DELETE RESTRICT,
    ADD accessibility_pram_id integer DEFAULT NULL
        CONSTRAINT map_object_accessibility_pram_id_fk
            REFERENCES accessibility ON
            UPDATE CASCADE ON
            DELETE RESTRICT;
ALTER TABLE versions.map_object
    ADD accessibility_pensioners_id integer DEFAULT NULL
        CONSTRAINT map_object_accessibility_pensioners_id_fk
            REFERENCES accessibility ON
            UPDATE CASCADE ON
            DELETE RESTRICT,
    ADD accessibility_pram_id integer DEFAULT NULL
        CONSTRAINT map_object_accessibility_pram_id_fk
            REFERENCES accessibility ON
            UPDATE CASCADE ON
            DELETE RESTRICT;
