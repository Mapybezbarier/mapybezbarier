begin;
create sequence if not exists public.mappable_entity_wc_cabin_access_id_seq
    start with 1
    increment by 1
    no minvalue
    no maxvalue
    cache 1;

create table if not exists public.mappable_entity_wc_cabin_access (
    id  integer not null default nextval('public.mappable_entity_wc_cabin_access_id_seq'::regclass)
        constraint mappable_entity_wc_cabin_access_pk primary key,
    title    varchar(255),
    pair_key varchar(255)
);

insert into public.mappable_entity_wc_cabin_access (id, title, pair_key) values
(1, 'FreelyaccessibleMappableEntityAccess', 'volně přístupná'),
(2, 'LockedMappableEntityAccess', 'uzamčená'),
(3, 'LockedEuroKeyMappableEntityAccess', 'uzamčená euro klíčem')
on conflict do nothing;

select pg_catalog.setval('mappable_entity_wc_cabin_access_id_seq', 3, true);

alter table public.wc drop constraint wc_wc_cabin_access_id_fk;
alter table public.wc
    add constraint wc_wc_cabin_access_id_fk foreign key (wc_cabin_access_id)
        references public.mappable_entity_wc_cabin_access (id)
        on update cascade on delete restrict;

alter table versions.wc drop constraint wc_wc_cabin_access_id_fk;
alter table versions.wc
    add constraint wc_wc_cabin_access_id_fk foreign key (wc_cabin_access_id)
        references public.mappable_entity_wc_cabin_access (id)
        on update cascade on delete restrict;
commit;
