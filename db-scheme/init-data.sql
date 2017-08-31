--
-- PostgreSQL database dump
--

-- Dumped from database version 9.4.5
-- Dumped by pg_dump version 9.4.5
-- Started on 2016-02-23 09:08:28 CET

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = public, pg_catalog;

--
-- TOC entry 2798 (class 0 OID 736181)
-- Dependencies: 235
-- Data for Name: a_o_b_announcement; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO a_o_b_announcement VALUES (1, 'PhraseAOBAnnouncement', 'fráze', NULL);
INSERT INTO a_o_b_announcement VALUES (2, 'JingleAOBAnnouncement', 'trylek', NULL);
INSERT INTO a_o_b_announcement VALUES (3, 'PhraseJingleAOBAnnouncement', 'fráze i trylek', '[1,2]');


--
-- TOC entry 2847 (class 0 OID 0)
-- Dependencies: 234
-- Name: a_o_b_announcement_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('a_o_b_announcement_id_seq', 2, true);


--
-- TOC entry 2754 (class 0 OID 731147)
-- Dependencies: 180
-- Data for Name: accessibility; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO accessibility VALUES (1, 'AccessibleObjectMKPO', 'přístupný');
INSERT INTO accessibility VALUES (2, 'PartlyAccessibleObjectMKPO', 'částečně přístupný');
INSERT INTO accessibility VALUES (3, 'InAccessibleObjectMKPO', 'nepřístupný');


--
-- TOC entry 2848 (class 0 OID 0)
-- Dependencies: 179
-- Name: accessibility_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('accessibility_id_seq', 1, false);


--
-- TOC entry 2770 (class 0 OID 736069)
-- Dependencies: 207
-- Data for Name: bell_type; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO bell_type VALUES (1, 'RingonlyBellType', 'pouze zvonění');
INSERT INTO bell_type VALUES (2, 'IntercomBellType', 'interkom');
INSERT INTO bell_type VALUES (3, 'MissingBellType', 'chybí');


--
-- TOC entry 2849 (class 0 OID 0)
-- Dependencies: 206
-- Name: bell_type_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('bell_type_id_seq', 3, true);


--
-- TOC entry 2842 (class 0 OID 819678)
-- Dependencies: 299
-- Data for Name: contrast_marking_localization; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO contrast_marking_localization VALUES (1, 'BottomContrastMarkingLocalization', 've spodní výškové úrovni', NULL);
INSERT INTO contrast_marking_localization VALUES (2, 'TopContrastMarkingLocalization', 'v horní výškové úrovni', NULL);
INSERT INTO contrast_marking_localization VALUES (3, 'MissingContrastMarkingLocalization', 'chybí', NULL);
INSERT INTO contrast_marking_localization VALUES (4, 'BottomTopContrastMarkingLocalization', 'v horní i spodní výškové úrovni', '[1,2]');


--
-- TOC entry 2850 (class 0 OID 0)
-- Dependencies: 298
-- Name: contrast_marking_localization_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('contrast_marking_localization_id_seq', 1, false);


--
-- TOC entry 2774 (class 0 OID 736085)
-- Dependencies: 211
-- Data for Name: door_opening; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO door_opening VALUES (1, 'MechanicalDoorOpening', 'mechanické');
INSERT INTO door_opening VALUES (2, 'AutomaticDoorOpening', 'automatické');
INSERT INTO door_opening VALUES (3, 'SlidingDoorOpening', 'posuvné');
INSERT INTO door_opening VALUES (4, 'SwingingDoorOpening', 'kyvné');


--
-- TOC entry 2776 (class 0 OID 736093)
-- Dependencies: 213
-- Data for Name: door_opening_direction; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO door_opening_direction VALUES (2, 'InwardsDoorOpeningDirection', 'do zádveří');
INSERT INTO door_opening_direction VALUES (3, 'SidesDoorOpeningDirection', 'do stran');
INSERT INTO door_opening_direction VALUES (4, 'OnesideDoorOpeningDirection', 'do strany');
INSERT INTO door_opening_direction VALUES (1, 'OutwardsDoorOpeningDirection', 'ze zádveří');


--
-- TOC entry 2851 (class 0 OID 0)
-- Dependencies: 212
-- Name: door_opening_direction_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('door_opening_direction_id_seq', 4, true);


--
-- TOC entry 2852 (class 0 OID 0)
-- Dependencies: 210
-- Name: door_opening_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('door_opening_id_seq', 4, true);


--
-- TOC entry 2772 (class 0 OID 736077)
-- Dependencies: 209
-- Data for Name: door_type; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO door_type VALUES (1, 'SinglepanelledDoorType', 'jednokřídlé');
INSERT INTO door_type VALUES (2, 'DoublepanelledDoorType', 'dvoukřídlé');
INSERT INTO door_type VALUES (3, 'CarouselDoorType', 'karuselové');


--
-- TOC entry 2853 (class 0 OID 0)
-- Dependencies: 208
-- Name: door_type_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('door_type_id_seq', 3, true);


--
-- TOC entry 2802 (class 0 OID 736197)
-- Dependencies: 239
-- Data for Name: elevator_cage_mirror_localization; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO elevator_cage_mirror_localization VALUES (1, 'FrontwallElevatorCageMirrorLocalization', 'čelní stěna');
INSERT INTO elevator_cage_mirror_localization VALUES (2, 'SidewallElevatorCageMirrorLocalization', 'boční stěna');
INSERT INTO elevator_cage_mirror_localization VALUES (3, 'SidewallsElevatorCageMirrorLocalization', 'obě boční stěny');


--
-- TOC entry 2854 (class 0 OID 0)
-- Dependencies: 238
-- Name: elevator_cage_mirror_localization_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('elevator_cage_mirror_localization_id_seq', 3, true);


--
-- TOC entry 2800 (class 0 OID 736189)
-- Dependencies: 237
-- Data for Name: elevator_cage_seconddoor_localization; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO elevator_cage_seconddoor_localization VALUES (1, 'FrontElevatorCageSeconddoorLocalization', 'čelní stěna');
INSERT INTO elevator_cage_seconddoor_localization VALUES (2, 'SideElevatorCageSeconddoorLocalization', 'boční stěna');


--
-- TOC entry 2855 (class 0 OID 0)
-- Dependencies: 236
-- Name: elevator_cage_seconddoor_localization_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('elevator_cage_seconddoor_localization_id_seq', 2, true);


--
-- TOC entry 2796 (class 0 OID 736173)
-- Dependencies: 233
-- Data for Name: elevator_control_flat_marking; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO elevator_control_flat_marking VALUES (1, 'GraphicElevatorControlFlatMarking', 'grafické');
INSERT INTO elevator_control_flat_marking VALUES (2, 'DigitalElevatorControlFlatMarking', 'digitální');


--
-- TOC entry 2856 (class 0 OID 0)
-- Dependencies: 232
-- Name: elevator_control_flat_marking_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('elevator_control_flat_marking_id_seq', 2, true);


--
-- TOC entry 2794 (class 0 OID 736165)
-- Dependencies: 231
-- Data for Name: elevator_control_relief_marking; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO elevator_control_relief_marking VALUES (1, 'EngravedElevatorControlReliefMarking', 'ryté');
INSERT INTO elevator_control_relief_marking VALUES (2, 'ProtrudingElevatorControlReliefMarking', 'vystouplé');


--
-- TOC entry 2857 (class 0 OID 0)
-- Dependencies: 230
-- Name: elevator_control_relief_marking_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('elevator_control_relief_marking_id_seq', 2, true);


--
-- TOC entry 2792 (class 0 OID 736157)
-- Dependencies: 229
-- Data for Name: elevator_driveoff; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO elevator_driveoff VALUES (1, 'LandingsElevatorDriveoff', 'na hlavních podestách');
INSERT INTO elevator_driveoff VALUES (2, 'MezzaninesElevatorDriveoff', 'v mezipatrech');


--
-- TOC entry 2858 (class 0 OID 0)
-- Dependencies: 228
-- Name: elevator_driveoff_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('elevator_driveoff_id_seq', 2, true);


--
-- TOC entry 2788 (class 0 OID 736141)
-- Dependencies: 225
-- Data for Name: elevator_handle_localization; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO elevator_handle_localization VALUES (1, 'FrontwallHandleLocalization', 'čelní stěna');
INSERT INTO elevator_handle_localization VALUES (2, 'SidewallHandleLocalization', 'boční stěna');
INSERT INTO elevator_handle_localization VALUES (3, 'SidewallsHandleLocalization', 'obě boční stěny');


--
-- TOC entry 2859 (class 0 OID 0)
-- Dependencies: 224
-- Name: elevator_handle_localization_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('elevator_handle_localization_id_seq', 3, true);


--
-- TOC entry 2790 (class 0 OID 736149)
-- Dependencies: 227
-- Data for Name: elevator_type; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO elevator_type VALUES (1, 'PersonalElevatorType', 'osobní');
INSERT INTO elevator_type VALUES (2, 'CargoElevatorType', 'nákladní');


--
-- TOC entry 2860 (class 0 OID 0)
-- Dependencies: 226
-- Name: elevator_type_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('elevator_type_id_seq', 2, true);


--
-- TOC entry 2768 (class 0 OID 736061)
-- Dependencies: 205
-- Data for Name: entrance_accessibility; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO entrance_accessibility VALUES (1, 'NoelevationEntranceAccessibility', 'bez převýšení');
INSERT INTO entrance_accessibility VALUES (2, 'OneStepEntranceAccessibility', 'jeden schod');
INSERT INTO entrance_accessibility VALUES (3, 'MoreStepsEntranceAccessibility', 'více schodů');
INSERT INTO entrance_accessibility VALUES (5, 'RampEntranceAccessibility', 'rampa');
INSERT INTO entrance_accessibility VALUES (4, 'PlatformEntranceAccessibility', 'plošina');


--
-- TOC entry 2861 (class 0 OID 0)
-- Dependencies: 204
-- Name: entrance_accessibility_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('entrance_accessibility_id_seq', 5, true);


--
-- TOC entry 2766 (class 0 OID 736053)
-- Dependencies: 203
-- Data for Name: entrance_guidingline; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO entrance_guidingline VALUES (1, 'NaturalEntranceGuidingline', 'přirozená', NULL);
INSERT INTO entrance_guidingline VALUES (2, 'ArtificialEntranceGuidingline', 'umělá', NULL);
INSERT INTO entrance_guidingline VALUES (3, 'MissingEntranceGuidingline', 'chybí', NULL);
INSERT INTO entrance_guidingline VALUES (4, 'NaturalArtificialEntranceGuidingline', 'přirozená a umělá', '[1,2]');


--
-- TOC entry 2862 (class 0 OID 0)
-- Dependencies: 202
-- Name: entrance_guidingline_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('entrance_guidingline_id_seq', 3, true);


--
-- TOC entry 2808 (class 0 OID 736221)
-- Dependencies: 245
-- Data for Name: entryarea_entry; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO entryarea_entry VALUES (1, 'SideEntryareaEntry', 'z boku');
INSERT INTO entryarea_entry VALUES (2, 'FrontEntryareaEntry', 'čelní nástup');


--
-- TOC entry 2863 (class 0 OID 0)
-- Dependencies: 244
-- Name: entryarea_entry_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('entryarea_entry_id_seq', 2, true);


--
-- TOC entry 2838 (class 0 OID 757641)
-- Dependencies: 291
-- Data for Name: exchange_source; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO exchange_source VALUES (12, 'vozejkmap.cz', 'vozejkmap');
INSERT INTO exchange_source VALUES (1, 'mapybezbarier.cz', 'mapybezbarier');
INSERT INTO exchange_source VALUES (2, 'DPA s.r.o.', 'xml');
INSERT INTO exchange_source VALUES (10, 'DPA s.r.o.', 'json');
INSERT INTO exchange_source VALUES (11, 'DPA s.r.o.', 'csv');
INSERT INTO exchange_source VALUES (13, 'WC Kompas', 'wckompas');
INSERT INTO exchange_source VALUES (14, 'Wheelmap', 'wheelmap');


--
-- TOC entry 2826 (class 0 OID 736293)
-- Dependencies: 263
-- Data for Name: hallway_door_marking; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO hallway_door_marking VALUES (1, 'DoorIsMarking', 'ano');
INSERT INTO hallway_door_marking VALUES (2, 'DoorIsNotMarking', 'ne');
INSERT INTO hallway_door_marking VALUES (3, 'DoorIsBrailleMarking', 'ano i Braillovo písmo');


--
-- TOC entry 2864 (class 0 OID 0)
-- Dependencies: 262
-- Name: hallway_door_marking_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('hallway_door_marking_id_seq', 3, true);


--
-- TOC entry 2828 (class 0 OID 736301)
-- Dependencies: 265
-- Data for Name: handle_type; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO handle_type VALUES (1, 'FoldingHandleType', 'pevné');
INSERT INTO handle_type VALUES (2, 'FixedHandleType', 'sklopné');


--
-- TOC entry 2865 (class 0 OID 0)
-- Dependencies: 264
-- Name: handle_type_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('handle_type_id_seq', 2, true);


--
-- TOC entry 2866 (class 0 OID 0)
-- Dependencies: 290
-- Name: import_source_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('import_source_id_seq', 14, true);


--
-- TOC entry 2750 (class 0 OID 731131)
-- Dependencies: 176
-- Data for Name: license; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO license VALUES (1, 'ODbL', NULL);
INSERT INTO license VALUES (2, 'CC BY-NC-SA 4.0', NULL);


--
-- TOC entry 2867 (class 0 OID 0)
-- Dependencies: 175
-- Name: license_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('license_id_seq', 10, true);


--
-- TOC entry 2804 (class 0 OID 736205)
-- Dependencies: 241
-- Data for Name: mappable_entity_access; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO mappable_entity_access VALUES (1, 'FreelyaccessibleMappableEntityAccess', 'volně přístupná');
INSERT INTO mappable_entity_access VALUES (2, 'LockedMappableEntityAccess', 'uzamčená');


--
-- TOC entry 2868 (class 0 OID 0)
-- Dependencies: 240
-- Name: mappable_entity_access_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('mappable_entity_access_id_seq', 3, true);


--
-- TOC entry 2764 (class 0 OID 736045)
-- Dependencies: 201
-- Data for Name: object_interior_accessibility; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO object_interior_accessibility VALUES (1, 'EntireObjectInteriorAccessibility', 'celý interiér nebo jeho větší část');
INSERT INTO object_interior_accessibility VALUES (2, 'PartObjectInteriorAccessibility', 'pouze část interiéru');
INSERT INTO object_interior_accessibility VALUES (3, 'InaccessibleObjectInteriorAccessibility', 'nepřístupný interiér');


--
-- TOC entry 2869 (class 0 OID 0)
-- Dependencies: 200
-- Name: object_interior_accessibility_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('object_interior_accessibility_id_seq', 3, true);


--
-- TOC entry 2762 (class 0 OID 736027)
-- Dependencies: 199
-- Data for Name: object_stairs_type; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO object_stairs_type VALUES (1, 'DirectObjectStairsType', 'přímé', NULL);
INSERT INTO object_stairs_type VALUES (2, 'SpiralObjectStairsType', 'točité', NULL);
INSERT INTO object_stairs_type VALUES (3, 'DirectSpiralObjectStairsType', 'přímé a točité', '[1,2]');


--
-- TOC entry 2870 (class 0 OID 0)
-- Dependencies: 198
-- Name: object_stairs_type_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('object_stairs_type_id_seq', 2, true);


--
-- TOC entry 2752 (class 0 OID 731139)
-- Dependencies: 178
-- Data for Name: object_type; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO object_type VALUES (2, 'TheatreObjectCategory', 'divadlo', NULL);
INSERT INTO object_type VALUES (1, 'BasilicaObjectCategory', 'bazilika', NULL);
INSERT INTO object_type VALUES (3, 'GalleryObjectCategory', 'galerie', NULL);
INSERT INTO object_type VALUES (4, 'DefensivecastleObjectCategory', 'hrad', NULL);
INSERT INTO object_type VALUES (6, 'TempleObjectCategory', 'chrám', NULL);
INSERT INTO object_type VALUES (9, 'CathedralObjectCategory', 'katedrála', NULL);
INSERT INTO object_type VALUES (10, 'MonasteryObjectCategory', 'klášter', NULL);
INSERT INTO object_type VALUES (11, 'LibraryObjectCategory', 'knihovna', NULL);
INSERT INTO object_type VALUES (12, 'ChurchObjectCategory', 'kostel', NULL);
INSERT INTO object_type VALUES (13, 'LorettaObjectCategory', 'loreta', NULL);
INSERT INTO object_type VALUES (14, 'MuseumObjectCategory', 'muzeum', NULL);
INSERT INTO object_type VALUES (17, 'PalaceObjectCategory', 'palác', NULL);
INSERT INTO object_type VALUES (18, 'MonumentObjectCategory', 'památník', NULL);
INSERT INTO object_type VALUES (19, 'TownhallObjectCategory', 'radnice', NULL);
INSERT INTO object_type VALUES (20, 'SynagogueObjectCategory', 'synagoga', NULL);
INSERT INTO object_type VALUES (21, 'FortressObjectCategory', 'tvrz', NULL);
INSERT INTO object_type VALUES (23, 'StatelyhomeObjectCategory', 'zámek', NULL);
INSERT INTO object_type VALUES (8, 'OtherObjectCategory', 'jiné', NULL);
INSERT INTO object_type VALUES (5, 'DefensivecastleStatelyhomeObjectCategory', 'hrad a zámek', '[4,23]');
INSERT INTO object_type VALUES (15, 'MuseumGalleryObjectCategory', 'muzeum a galerie', '[14,3]');
INSERT INTO object_type VALUES (16, 'MuseumLibraryObjectCategory', 'muzeum a knihovna', '[14,11]');
INSERT INTO object_type VALUES (22, 'TowerObjectCategory', 'rozhledna (vyhlídková věž)', NULL);
INSERT INTO object_type VALUES (7, 'ChapelObjectCategory', 'kaple', NULL);
INSERT INTO object_type VALUES (29, 'WaterParkObjectCategory', 'aquapark', NULL);
INSERT INTO object_type VALUES (30, 'BusStationObjectCategory', 'autobusové nádraží', NULL);
INSERT INTO object_type VALUES (31, 'BankObjectCategory', 'banka', NULL);
INSERT INTO object_type VALUES (32, 'BarObjectCategory', 'bar', NULL);
INSERT INTO object_type VALUES (33, 'BotanicGardenObjectCategory', 'botanická zahrada', NULL);
INSERT INTO object_type VALUES (34, 'PastryObjectCategory', 'cukrárna, kavárna a čajovna', NULL);
INSERT INTO object_type VALUES (35, 'GasStationObjectCategory', 'čerpací stanice', NULL);
INSERT INTO object_type VALUES (36, 'FarmObjectCategory', 'farma a statek', NULL);
INSERT INTO object_type VALUES (37, 'TaxOfficeObjectCategory', 'finanční úřad', NULL);
INSERT INTO object_type VALUES (38, 'StadiumObjectCategory', 'fotbalové hřiště a stadion', NULL);
INSERT INTO object_type VALUES (39, 'GuestHouseObjectCategory', 'penzion', NULL);
INSERT INTO object_type VALUES (40, 'HotelObjectCategory', 'hotel', NULL);
INSERT INTO object_type VALUES (41, 'PubObjectCategory', 'hospoda', NULL);
INSERT INTO object_type VALUES (42, 'FuneralHallObjectCategory', 'smuteční síň', NULL);
INSERT INTO object_type VALUES (43, 'ObservatoryObjectCategory', 'hvězdárna a planetárium', NULL);
INSERT INTO object_type VALUES (44, 'HypermarketObjectCategory', 'hypermarket', NULL);
INSERT INTO object_type VALUES (45, 'InformationCenterObjectCategory', 'informační centrum', NULL);
INSERT INTO object_type VALUES (46, 'CampObjectCategory', 'kemp', NULL);
INSERT INTO object_type VALUES (47, 'CinemaObjectCategory', 'kino', NULL);
INSERT INTO object_type VALUES (48, 'ConcertHallObjectCategory', 'koncertní síň', NULL);
INSERT INTO object_type VALUES (49, 'ContactSocialServiceObjectCategory', 'sociální kontaktní služby', NULL);
INSERT INTO object_type VALUES (50, 'SwimmingObjectCategory', 'koupaliště', NULL);
INSERT INTO object_type VALUES (51, 'IndoorSwimmingPoolObjectCategory', 'krytý plavecký bazén', NULL);
INSERT INTO object_type VALUES (52, 'SpaHouseObjectCategory', 'lázeňský dům', NULL);
INSERT INTO object_type VALUES (53, 'WellnessObjectCategory', 'wellness', NULL);
INSERT INTO object_type VALUES (54, 'PharmacyObjectCategory', 'lékárna', NULL);
INSERT INTO object_type VALUES (55, 'DoctorObjectCategory', 'lékař', NULL);
INSERT INTO object_type VALUES (56, 'MedicalEmergencyObjectCategory', 'lékařská pohotovost', NULL);
INSERT INTO object_type VALUES (57, 'DentalEmergencyObjectCategory', 'zubní pohotovost', NULL);
INSERT INTO object_type VALUES (58, 'AirportObjectCategory', 'letiště', NULL);
INSERT INTO object_type VALUES (59, 'FolkArchitectureObjectCategory', 'lidová architektura', NULL);
INSERT INTO object_type VALUES (60, 'CityHallObjectCategory', 'magistrát', NULL);
INSERT INTO object_type VALUES (61, 'KindergartenObjectCategory', 'mateřská škola', NULL);
INSERT INTO object_type VALUES (62, 'MunicipalityObjectCategory', 'městský úřad', NULL);
INSERT INTO object_type VALUES (63, 'MosqueObjectCategory', 'mešita', NULL);
INSERT INTO object_type VALUES (64, 'HospitalObjectCategory', 'nemocnice', NULL);
INSERT INTO object_type VALUES (65, 'PolyclinicObjectCategory', 'poliklinika', NULL);
INSERT INTO object_type VALUES (66, 'MunicipalOfficeObjectCategory', 'obecní úřad', NULL);
INSERT INTO object_type VALUES (67, 'StoreObjectCategory', 'obchod', NULL);
INSERT INTO object_type VALUES (68, 'DepartmentStoreObjectCategory', 'obchodní dům a nákupní centrum', NULL);
INSERT INTO object_type VALUES (69, 'OpticianShopObjectCategory', 'optika', NULL);
INSERT INTO object_type VALUES (70, 'BreweryObjectCategory', 'pivovar', NULL);
INSERT INTO object_type VALUES (71, 'ResidentialSocialServiceObjectCategory', 'sociální pobytové služby', NULL);
INSERT INTO object_type VALUES (72, 'InsuranceOfficeObjectCategory', 'pojišťovna', NULL);
INSERT INTO object_type VALUES (73, 'PoliceObjectCategory', 'policie ČR', NULL);
INSERT INTO object_type VALUES (74, 'MetropolitanPoliceObjectCategory', 'policie městská', NULL);
INSERT INTO object_type VALUES (75, 'PostOfficeObjectCategory', 'pošta', NULL);
INSERT INTO object_type VALUES (76, 'CarDealerObjectCategory', 'prodejce automobilů', NULL);
INSERT INTO object_type VALUES (77, 'HarborObjectCategory', 'přístav', NULL);
INSERT INTO object_type VALUES (78, 'RecreationalFacilityObjectCategory', 'rekreační zařízení', NULL);
INSERT INTO object_type VALUES (79, 'RestaurantObjectCategory', 'restaurace a pohostinství', NULL);
INSERT INTO object_type VALUES (80, 'FastFoodObjectCategory', 'rychlé občerstvení', NULL);
INSERT INTO object_type VALUES (81, 'ServiceObjectCategory', 'služby', NULL);
INSERT INTO object_type VALUES (82, 'SocialOfficeObjectCategory', 'sociální zabezpečení', NULL);
INSERT INTO object_type VALUES (83, 'CourtObjectCategory', 'soud', NULL);
INSERT INTO object_type VALUES (84, 'SportsFacilityObjectCategory', 'sportovní zařízení', NULL);
INSERT INTO object_type VALUES (85, 'AdministrativeOfficeObjectCategory', 'správní úřad', NULL);
INSERT INTO object_type VALUES (86, 'MetroStationObjectCategory', 'stanice metra', NULL);
INSERT INTO object_type VALUES (87, 'PublicProsecutorObjectCategory', 'státní zastupitelství', NULL);
INSERT INTO object_type VALUES (88, 'HighSchoolObjectCategory', 'střední škola', NULL);
INSERT INTO object_type VALUES (89, 'SupermarketObjectCategory', 'supermarket', NULL);
INSERT INTO object_type VALUES (90, 'WeddingHallObjectCategory', 'svatební síň', NULL);
INSERT INTO object_type VALUES (91, 'SchoolObjectCategory', 'škola', NULL);
INSERT INTO object_type VALUES (92, 'EmploymentOfficeObjectCategory', 'úřad práce', NULL);
INSERT INTO object_type VALUES (93, 'PublicToiletObjectCategory', 'veřejné WC', NULL);
INSERT INTO object_type VALUES (94, 'VeterinarySurgeryObjectCategory', 'veterinární ordinace', NULL);
INSERT INTO object_type VALUES (95, 'ViticultureObjectCategory', 'vinařství', NULL);
INSERT INTO object_type VALUES (96, 'TrainStationObjectCategory', 'vlaková stanice', NULL);
INSERT INTO object_type VALUES (97, 'CollegeObjectCategory', 'vysoká škola a univerzita', NULL);
INSERT INTO object_type VALUES (98, 'BasicSchoolObjectCategory', 'základní škola', NULL);
INSERT INTO object_type VALUES (99, 'EmbassyObjectCategory', 'zastupitelský úřad a ambasáda', NULL);
INSERT INTO object_type VALUES (100, 'HealthInsuranceCompanyObjectCategory', 'zdravotní pojišťovna', NULL);
INSERT INTO object_type VALUES (101, 'MedicalSupplyObjectCategory', 'zdravotnické potřeby', NULL);
INSERT INTO object_type VALUES (102, 'WinterStadiumObjectCategory', 'zimní stadion', NULL);
INSERT INTO object_type VALUES (103, 'ZooObjectCategory', 'zoologická zahrada', NULL);
INSERT INTO object_type VALUES (104, 'ZooObjectCategory', 'zoologická zahrada', NULL);
INSERT INTO object_type VALUES (105, 'CultureHouseObjectCategory', 'kulturní dům', NULL);
INSERT INTO object_type VALUES (106, 'AtmObjectCategory', 'bankomat', NULL);
INSERT INTO object_type VALUES (107, 'InstitutionObjectCategory', 'instituce', NULL);
INSERT INTO object_type VALUES (108, 'TransportObjectCategory', 'doprava', NULL);
INSERT INTO object_type VALUES (109, 'ElementaryArtSchoolObjectCategory', 'základní umělecká škola', NULL);
INSERT INTO object_type VALUES (104, 'CultureHouseObjectCategory', 'kulturní dům', NULL);
INSERT INTO object_type VALUES (105, 'AtmObjectCategory', 'bankomat', NULL);
INSERT INTO object_type VALUES (110, 'MedicalFacilityObjectCategory', 'zdravotnické zařízení', NULL);
INSERT INTO object_type VALUES (111, 'EducationObjectCategory', 'vzdělávání', NULL);
INSERT INTO object_type VALUES (112, 'LeisureTimeObjectCategory', 'volný čas', NULL);


--
-- TOC entry 2871 (class 0 OID 0)
-- Dependencies: 177
-- Name: object_type_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('object_type_id_seq', 28, true);


--
-- TOC entry 2806 (class 0 OID 736213)
-- Dependencies: 243
-- Data for Name: platform_type; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO platform_type VALUES (1, 'VerticalPlatformType', 'svislá');
INSERT INTO platform_type VALUES (2, 'InclinedPlatformType', 'šikmá');


--
-- TOC entry 2872 (class 0 OID 0)
-- Dependencies: 242
-- Name: platform_type_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('platform_type_id_seq', 2, true);


--
-- TOC entry 2786 (class 0 OID 736133)
-- Dependencies: 223
-- Data for Name: ramp_handle_localization; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO ramp_handle_localization VALUES (1, 'SidewallHandleLocalization', 'jednostranné');
INSERT INTO ramp_handle_localization VALUES (2, 'SidewallsHandleLocalization', 'oboustranné');


--
-- TOC entry 2873 (class 0 OID 0)
-- Dependencies: 222
-- Name: ramp_handle_localization_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('ramp_handle_localization_id_seq', 2, true);


--
-- TOC entry 2778 (class 0 OID 736101)
-- Dependencies: 215
-- Data for Name: ramp_skids_localization; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO ramp_skids_localization VALUES (1, 'EntranceRampSkidsLocalization', 'před vstupními dveřmi');
INSERT INTO ramp_skids_localization VALUES (2, 'LobbyRampSkidsLocalization', 'v zádveří');
INSERT INTO ramp_skids_localization VALUES (3, 'InteriorRampSkidsLocalization', 'v interiéru');


--
-- TOC entry 2874 (class 0 OID 0)
-- Dependencies: 214
-- Name: ramp_skids_localization_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('ramp_skids_localization_id_seq', 3, true);


--
-- TOC entry 2780 (class 0 OID 736109)
-- Dependencies: 217
-- Data for Name: ramp_skids_mobility; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO ramp_skids_mobility VALUES (2, 'MobileRampSkidsMobility', 'mobilní');
INSERT INTO ramp_skids_mobility VALUES (1, 'FixedRampSkidsMobility', 'pevná');


--
-- TOC entry 2875 (class 0 OID 0)
-- Dependencies: 216
-- Name: ramp_skids_mobility_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('ramp_skids_mobility_id_seq', 2, true);


--
-- TOC entry 2784 (class 0 OID 736125)
-- Dependencies: 221
-- Data for Name: ramp_surface; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO ramp_surface VALUES (2, 'NonslipperyRampSurface', 'nekluzký');
INSERT INTO ramp_surface VALUES (1, 'SlipperyRampSurface', 'kluzký');


--
-- TOC entry 2876 (class 0 OID 0)
-- Dependencies: 220
-- Name: ramp_surface_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('ramp_surface_id_seq', 2, true);


--
-- TOC entry 2782 (class 0 OID 736117)
-- Dependencies: 219
-- Data for Name: ramp_type; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO ramp_type VALUES (1, 'DirectRampType', 'přímá');
INSERT INTO ramp_type VALUES (2, 'BentRampType', 'zalomená');
INSERT INTO ramp_type VALUES (3, 'SpiralRampType', 'točitá');


--
-- TOC entry 2877 (class 0 OID 0)
-- Dependencies: 218
-- Name: ramp_type_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('ramp_type_id_seq', 3, true);


--
-- TOC entry 2760 (class 0 OID 731344)
-- Dependencies: 189
-- Data for Name: rampskids_platform_relation; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO rampskids_platform_relation VALUES (1, 'mainEntrance', 'hlavní vstup');
INSERT INTO rampskids_platform_relation VALUES (2, 'sideEntrance', 'vedlejší vstup');


--
-- TOC entry 2878 (class 0 OID 0)
-- Dependencies: 188
-- Name: rampskids_platform_relation_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('rampskids_platform_relation_id_seq', 1, false);


--
-- TOC entry 2756 (class 0 OID 731155)
-- Dependencies: 182
-- Data for Name: role; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO role VALUES (2, 'admin', 'Admin');
INSERT INTO role VALUES (1, 'master', 'Master');
INSERT INTO role VALUES (4, 'mapper', 'Mapař');
INSERT INTO role VALUES (3, 'agency', 'Agentura');


--
-- TOC entry 2879 (class 0 OID 0)
-- Dependencies: 181
-- Name: role_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('role_id_seq', 1, false);


--
-- TOC entry 2836 (class 0 OID 736333)
-- Dependencies: 273
-- Data for Name: tap_type; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO tap_type VALUES (1, 'LevelTap', 'páková');
INSERT INTO tap_type VALUES (2, 'TouchfreeTap', 'bezdotyková');
INSERT INTO tap_type VALUES (3, 'ValveTap', 'ventil (kohoutek)');


--
-- TOC entry 2880 (class 0 OID 0)
-- Dependencies: 272
-- Name: tap_type_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('tap_type_id_seq', 3, true);


--
-- TOC entry 2758 (class 0 OID 731163)
-- Dependencies: 184
-- Data for Name: user; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO "user" VALUES (1, 'info@mapybezbarier.cz', 'master', NULL, 1, '2016-01-13 10:12:45', NULL, NULL, true, 'Master', 'Account', NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 1);


--
-- TOC entry 2881 (class 0 OID 0)
-- Dependencies: 183
-- Name: user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('user_id_seq', 57, true);


--
-- TOC entry 2820 (class 0 OID 736269)
-- Dependencies: 257
-- Data for Name: w_c_basin_space; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO w_c_basin_space VALUES (1, 'FreeWCBasinSpace', 'volný');
INSERT INTO w_c_basin_space VALUES (3, 'BlockedbyfixedWCBasinSpace', 'blokovaný pevným prvkem');
INSERT INTO w_c_basin_space VALUES (2, 'BlockedbymobileWCBasinSpace', 'blokovaný mobilním prvkem');


--
-- TOC entry 2882 (class 0 OID 0)
-- Dependencies: 256
-- Name: w_c_basin_space_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('w_c_basin_space_id_seq', 3, true);


--
-- TOC entry 2824 (class 0 OID 736285)
-- Dependencies: 261
-- Data for Name: w_c_cabin_disposition; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO w_c_cabin_disposition VALUES (1, 'TopLeftWCCabinDisposition', 'Nahore vlevo');
INSERT INTO w_c_cabin_disposition VALUES (2, 'TopRightWCCabinDisposition', 'Nahore vpravo');
INSERT INTO w_c_cabin_disposition VALUES (3, 'LeftTopWCCabinDisposition', 'Vlevo nahore');
INSERT INTO w_c_cabin_disposition VALUES (4, 'LeftBottomWCCabinDisposition', 'Vlevo dole');
INSERT INTO w_c_cabin_disposition VALUES (5, 'BottomLeftWCCabinDisposition', 'Dole vlevo');
INSERT INTO w_c_cabin_disposition VALUES (6, 'BottomRightWCCabinDisposition', 'Dole vpravo');
INSERT INTO w_c_cabin_disposition VALUES (7, 'RightTopWCCabinDisposition', 'Vpravo nahore');
INSERT INTO w_c_cabin_disposition VALUES (8, 'RightBottomWCCabinDisposition', 'Vpravo dole');


--
-- TOC entry 2883 (class 0 OID 0)
-- Dependencies: 260
-- Name: w_c_cabin_disposition_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('w_c_cabin_disposition_id_seq', 8, true);


--
-- TOC entry 2812 (class 0 OID 736237)
-- Dependencies: 249
-- Data for Name: w_c_cabin_localization; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO w_c_cabin_localization VALUES (2, 'LadiesWCCabinLocalization', 'v oddělení WC ženy', NULL);
INSERT INTO w_c_cabin_localization VALUES (3, 'GentsWCCabinLocalization', 'v oddělení WC muži', NULL);
INSERT INTO w_c_cabin_localization VALUES (4, 'GentsLadiesWCCabinLocalization', 'v oddělení WC muži i WC ženy', '[2,3]');
INSERT INTO w_c_cabin_localization VALUES (1, 'SelfcontainedWCCabinLocalization', 'samostatně', NULL);


--
-- TOC entry 2884 (class 0 OID 0)
-- Dependencies: 248
-- Name: w_c_cabin_localization_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('w_c_cabin_localization_id_seq', 3, true);


--
-- TOC entry 2810 (class 0 OID 736229)
-- Dependencies: 247
-- Data for Name: w_c_categorization; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO w_c_categorization VALUES (1, 'AccessibleWCMKPO', 'WC I');
INSERT INTO w_c_categorization VALUES (2, 'PartlyAccessibleWCMKPO', 'WC II');
INSERT INTO w_c_categorization VALUES (3, 'InAccessibleWCMKPO', 'běžné WC');


--
-- TOC entry 2885 (class 0 OID 0)
-- Dependencies: 246
-- Name: w_c_categorization_m_k_p_o_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('w_c_categorization_m_k_p_o_id_seq', 3, true);


--
-- TOC entry 2822 (class 0 OID 736277)
-- Dependencies: 259
-- Data for Name: w_c_changingdesk; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO w_c_changingdesk VALUES (1, 'FoldingWCChangingdesk', 'sklopný');
INSERT INTO w_c_changingdesk VALUES (2, 'MobileWCChangingdesk', 'mobilní');


--
-- TOC entry 2886 (class 0 OID 0)
-- Dependencies: 258
-- Name: w_c_changingdesk_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('w_c_changingdesk_id_seq', 2, true);


--
-- TOC entry 2832 (class 0 OID 736317)
-- Dependencies: 269
-- Data for Name: w_c_door_handle_position; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO w_c_door_handle_position VALUES (1, 'InsideHandlePosition', 'uvnitř', NULL);
INSERT INTO w_c_door_handle_position VALUES (2, 'OutsideHandlePosition', 'vně', NULL);
INSERT INTO w_c_door_handle_position VALUES (3, 'MissingHandlePosition', 'chybí', NULL);
INSERT INTO w_c_door_handle_position VALUES (4, 'InsideOutsideHandlePosition', 'uvnitř i vně', '[1,2]');


--
-- TOC entry 2887 (class 0 OID 0)
-- Dependencies: 268
-- Name: w_c_door_handle_position_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('w_c_door_handle_position_id_seq', 3, true);


--
-- TOC entry 2830 (class 0 OID 736309)
-- Dependencies: 267
-- Data for Name: w_c_door_opening_direction; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO w_c_door_opening_direction VALUES (3, 'SidesDoorOpeningDirection', 'posuvné');
INSERT INTO w_c_door_opening_direction VALUES (2, 'InwardsDoorOpeningDirection', 'do kabiny');
INSERT INTO w_c_door_opening_direction VALUES (1, 'OutwardsDoorOpeningDirection', 'z kabiny');


--
-- TOC entry 2888 (class 0 OID 0)
-- Dependencies: 266
-- Name: w_c_door_opening_direction_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('w_c_door_opening_direction_id_seq', 3, true);


--
-- TOC entry 2816 (class 0 OID 736253)
-- Dependencies: 253
-- Data for Name: w_c_flushing; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO w_c_flushing VALUES (1, 'AutomaticWCFlushing', 'automatické');
INSERT INTO w_c_flushing VALUES (2, 'MechanicalWCFlushing', 'mechanické');


--
-- TOC entry 2818 (class 0 OID 736261)
-- Dependencies: 255
-- Data for Name: w_c_flushing_difficulty; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO w_c_flushing_difficulty VALUES (2, 'DifficultWCFlushingDifficulty', 'obtížné');
INSERT INTO w_c_flushing_difficulty VALUES (1, 'OKWCFlushingDifficulty', 'snadné');
INSERT INTO w_c_flushing_difficulty VALUES (3, 'BrokenWCFlushingDifficulty', 'rozbité');


--
-- TOC entry 2889 (class 0 OID 0)
-- Dependencies: 254
-- Name: w_c_flushing_difficulty_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('w_c_flushing_difficulty_id_seq', 2, true);


--
-- TOC entry 2890 (class 0 OID 0)
-- Dependencies: 252
-- Name: w_c_flushing_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('w_c_flushing_id_seq', 2, true);


--
-- TOC entry 2814 (class 0 OID 736245)
-- Dependencies: 251
-- Data for Name: w_c_switch; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO w_c_switch VALUES (1, 'YesWCSwitch', 'ano');
INSERT INTO w_c_switch VALUES (2, 'MissingWCSwitch', 'chybí');
INSERT INTO w_c_switch VALUES (3, 'AutomaticWCSwitch', 'automat');


--
-- TOC entry 2891 (class 0 OID 0)
-- Dependencies: 250
-- Name: w_c_switch_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('w_c_switch_id_seq', 3, true);


--
-- TOC entry 2840 (class 0 OID 806900)
-- Dependencies: 293
-- Data for Name: washbasin_handle_type; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO washbasin_handle_type VALUES (1, 'VerticalHandleOrientation', 'svislé');
INSERT INTO washbasin_handle_type VALUES (2, 'HorizontalHandleOrientation', 'vodorovné');


--
-- TOC entry 2892 (class 0 OID 0)
-- Dependencies: 292
-- Name: washbasin_handle_type_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('washbasin_handle_type_id_seq', 2, true);


--
-- TOC entry 2834 (class 0 OID 736325)
-- Dependencies: 271
-- Data for Name: washbasin_underpass; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO washbasin_underpass VALUES (1, 'SufficientWashbasinUnderpass', 'dostatečný');
INSERT INTO washbasin_underpass VALUES (2, 'InsufficientWashbasinUnderpass', 'nedostatečný');


--
-- TOC entry 2893 (class 0 OID 0)
-- Dependencies: 270
-- Name: washbasin_underpass_id_seq; Type: SEQUENCE SET; Schema: public; Owner: mapy_pristupnosti_db_01
--

SELECT pg_catalog.setval('washbasin_underpass_id_seq', 2, true);


--
-- TOC entry 2600 (class 0 OID 16585)
-- Dependencies: 222
-- Data for Name: lang; Type: TABLE DATA; Schema: public; Owner: mapy_pristupnosti_db_01
--

INSERT INTO lang VALUES ('cs', 'Česky', NULL);
INSERT INTO lang VALUES ('en', 'English', NULL);


-- Completed on 2016-02-23 09:08:29 CET

--
-- PostgreSQL database dump complete
--
