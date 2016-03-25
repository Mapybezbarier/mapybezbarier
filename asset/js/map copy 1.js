/**
 * @param {object} config
 * @constructor
 */
var Map = function (config) {
    this.config = $.extend({
        item: null,
        map: {},
        clusters: null,
        markers: [],
        contentSelector: ".content",
        filterContentSelector: ".filters",
        detailOpenerSelector: '.nwjs_detail_opener',
        detailCloserSelector: '.nwjs_detail_closer',
        detailContentSelector: '.detail',
        detailSelector: '.nwjs_detail',
        detailOpenClass: 'opened',
        newsSelector: '.nwjs_news',
        newsCloseSelector: '.nwjs_close_news',
        mapTypeChangeSelector: '.nwjs_change_map_type',
        mapTypeChangeActiveClass: 'active',
        mapZoomSelector: '.nwjs_map_zoom',
        infoBoxCloseSelector: '.nwjs_infobox_closer',
        infoBoxClass: 'info_box',
        infoBoxSelector: '.info_box',
        setGeolocationButtonSelector: '.nwjs_set_geolocation',
        autocompleteInputSelector: '#nwjs_search_place',
        autocompleteDefaultZoom: 17,
        autocompleteComponentRestrictions: {country: 'cz'},
        openedInfoboxClass: 'infobox_opened'
    }, config);

    this.map = null;
    this.markers = {};
    this.infoBox = null;
    this.infoBoxes = {};

    this.templates = {
        spinner: null,
        error: null,
        autocompleteerror: null
    };

    this.init(config);
};

/**
 * Inicializace tridy.
 */
Map.prototype.init = function () {
    this.initTemplates();

    if (undefined != this.config.item && this.config.item.length) {
        this.initMap();
        this.initMarkers();
    }
};

/**
 * Inicializace mapy.
 */
Map.prototype.initMap = function () {
    this.map = new google.maps.Map(this.config.item.get(0), this.config.map);

    // @see http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclustererplus/docs/reference.html
    var styleDefaultOptions = {
            textColor: 'white',
            fontWeight: 'bold'
        },
        mcOptions = {
            gridSize: 60,
            maxZoom: 13,
            styles: [$.extend({
                url: '/asset/img/markers/cluster/original_cluster_1.png',
                textSize: 18,
                height: 60,
                width: 60
            }, styleDefaultOptions), $.extend({
                url: '/asset/img/markers/cluster/original_cluster_2.png',
                textSize: 20,
                height: 70,
                width: 70
            }, styleDefaultOptions), $.extend({
                url: '/asset/img/markers/cluster/original_cluster_3.png',
                textSize: 20,
                height: 85,
                width: 85
            }, styleDefaultOptions)]
        };

    this.clusters = new MarkerClusterer(this.map, [], mcOptions);

    this.bindMapTypeChange();
    this.bindMapZoom();
    this.bindSetGeolocation();
    this.bindAutocomplete();
    this.fixInfoWindow();
    this.bindEmbeddedPopupOpen();
    this.bindNewsClose();
};

/**
 * Inicializace markeru
 */
Map.prototype.initMarkers = function () {
    for (var i = 0, count = this.config.markers.length; i < count; i++) {
        var marker = this.config.markers[i];

        if (undefined === this.markers[marker['id']]) {
            this.markers[marker['id']] = this.prepareMarker(marker);
        }
    }

    this.clusters.clearMarkers();
    this.clusters.addMarkers($.map(this.markers, function(v) { return v; }));
};

/**
 * Inicizalice sablon.
 */
Map.prototype.initTemplates = function () {
    for (var template in this.templates) {
        var $template = $('#info-box-template-' + template);

        this.templates[template] = $template.html();

        $template.remove();
    }
};

/**
 * Priprava markeru.
 * @param {object} marker
 * @returns {google.maps.Marker}
 */
Map.prototype.prepareMarker = function (marker) {
    var context = this,
        marker_image = new google.maps.MarkerImage(
            marker['image'],
            null, /* size is determined at runtime */
            null, /* origin is 0,0 */
            null, /* anchor is bottom center of the scaled image */
            new google.maps.Size(50, 70)
        );

    var config = {
        title: marker['title'],
        icon: marker_image
    };

    if ('undefined' != typeof marker['latitude'] && 'undefined' != typeof marker['longitude']) {
        config.position = new google.maps.LatLng(marker['latitude'], marker['longitude'])
    }

    var mapMarker = new google.maps.Marker(config);

    mapMarker.addListener('click', function () {
        context.handleClick(marker);
    });

    return mapMarker;
};

/**
 * Priprava info boxu.
 */
Map.prototype.prepareInfoBox = function (marker) {
    var pixelYOffset = 'group' !== marker['type'] && 'community' === marker['type'][0] ? -85 : -100, // vertikalne o vysku markeru
        clearanceYOffset = 'group' !== marker['type'] ? 380 : 400,
        config = {
            boxClass: this.config.infoBoxClass,
            content: this.templates.spinner,
            alignBottom: true,
            infoBoxClearance: new google.maps.Size(10, clearanceYOffset),
            pixelOffset: new google.maps.Size(-150, pixelYOffset), // horizontalne sirka / 2
            noSupress: true, // HACK pro nezobrazovani infowindow po kliku na POI ikonky @see this.fixInfoWindow()
            closeBoxURL: ""
        };

    return new InfoBox(config);
};

/**
 * Handler kliknuti na marker.
 * @param {object} marker
 */
Map.prototype.handleClick = function (marker) {
    var context = this;

    if (undefined === this.infoBoxes[marker['id']]) {
        this.infoBoxes[marker['id']] = this.prepareInfoBox(marker);
    }

    if (null === this.infoBox || this.infoBox !== this.infoBoxes[marker['id']]) {
        this.closeInfoBoxes();

        this.infoBox = this.infoBoxes[marker['id']];
        this.infoBox.open(this.map, this.markers[marker['id']]);

        $(this.config.contentSelector).addClass(this.config.openedInfoboxClass);

        var config = {
            url: this.config.item.data('info-box-load-url'),
            data: {
                'map-ids': marker['object_ids']
            },
            success: function (payload) {
                context.loadContentSuccessHandler(payload);
            },
            error: function () {
                context.loadContentErrorHandler()
            }
        };

        $.nette.ajax(config);
    }
};

/**
 * Callback pro otevreni detailu.
 */
Map.prototype.openDetailBox = function () {
    $(this.config.detailSelector).addClass(this.config.detailOpenClass);
    this.closeNews();
};

/**
 * Callback pro zavreni detailu.
 */
Map.prototype.closeDetailBox = function () {
    $(this.config.detailSelector).removeClass(this.config.detailOpenClass);
    $(this.config.detailSelector).find(this.config.detailContentSelector).empty();
};

/**
 * Callback pro zavreni novinek.
 */
Map.prototype.closeNews = function () {
    $(this.config.newsSelector).removeClass(this.config.detailOpenClass);
};

/**
 * Zavreni vsech info boxu.
 */
Map.prototype.closeInfoBoxes = function () {
    this.closeDetailBox();

    for (var id in this.infoBoxes) {
        this.infoBoxes[id].close();
    }
};

/**
 * Zavreni aktualniho info boxu.
 */
Map.prototype.closeInfoBox = function () {
    this.closeDetailBox();

    $('body').removeClass(this.config.openedInfoboxClass);

    if (null !== this.infoBox) {
        this.infoBox.close();
        this.infoBox = null;
    }
};

/**
 * Callback uspesneho nacteni obsahu info boxu.
 * @param {string} payload
 */
Map.prototype.loadContentSuccessHandler = function (payload) {
    this.infoBox.setContent(payload);

    this.bindDetailActions();
};

/**
 * Callback neuspesneho nacteni obsahu info boxu.
 */
Map.prototype.loadContentErrorHandler = function () {
    this.infoBox.setContent(this.templates.error);
};

/**
 * Bind kliknuti na odkaz zobrazeni detailu.
 */
Map.prototype.bindDetailActions = function () {
    var context = this;

    $(this.config.infoBoxSelector).on('click', this.config.detailOpenerSelector, function (event) {
        event.preventDefault();

        // na mobilu otevreni detailu zavre filtr a vyhledavani
        if (isMobile()) {
            $('.nwjs_filter').removeClass('opened');
            $('.nwjs_search_opener').parent().removeClass('opened');
        }

        context.openDetailBox();
    });

    $(this.config.infoBoxSelector).on('click', this.config.infoBoxCloseSelector, function (event) {
        event.preventDefault();

        context.closeInfoBox();
    });

    $(this.config.infoBoxSelector).on('click', this.config.detailCloserSelector, function (event) {
        event.preventDefault();

        context.closeDetailBox();
    });
};

/**
 * Bind kliknuti na krizek zavreni novinek
 */
Map.prototype.bindNewsClose = function () {
    var context = this;

    $(this.config.newsSelector).on('click', this.config.newsCloseSelector, function (event) {
        context.closeNews();
    });
};

/**
 * Bind zmena typu mapy
 */
Map.prototype.bindMapTypeChange = function () {
    var context = this,
        activeClass = context.config.mapTypeChangeActiveClass;

    $(this.config.filterContentSelector).on('click', this.config.mapTypeChangeSelector, function () {
        var $this = $(this);

        $this.addClass(activeClass).siblings().removeClass(activeClass);

        switch ($this.data('type')) {
            case "roadmap":
                context.map.setMapTypeId(google.maps.MapTypeId.ROADMAP);
                break;
            case "hybrid":
                context.map.setMapTypeId(google.maps.MapTypeId.HYBRID);
                break;
        }
    });
};


/**
 * Bind zoom mapy
 */
Map.prototype.bindMapZoom = function () {
    var context = this;

    $('body').on('click', this.config.mapZoomSelector, function () {
        var acutal_zoom = context.map.getZoom();

        switch ($(this).attr('data-type')) {
            case "in":
                context.map.setZoom(acutal_zoom + 1);
                break;
            case "out":
                context.map.setZoom(acutal_zoom - 1);
                break;
        }
    });
};

/**
 * Bind generovani odkazu na vlozenou mapu
 */
Map.prototype.bindEmbeddedPopupOpen = function () {
    var context = this;

    $('.nwjs_embedded_popup_opener').on('click', function (event) {
        event.preventDefault();

        var url = $(this).attr('href');

        var data = {
            'zoom': context.map.getZoom(),
            'center-lat': context.map.getCenter().lat(),
            'center-lng': context.map.getCenter().lng()
        };

        loadPopupContent(url, data);
    });
};

/**
 * Bind nastaveni geolokace
 */
Map.prototype.bindSetGeolocation = function () {
    var context = this;

    $(this.config.filterContentSelector).on('click', this.config.setGeolocationButtonSelector, function (event) {
        context.initializeGeolocation();
    });
};

/** init geolokace */
Map.prototype.initializeGeolocation = function () {
    var that = this;

    if (navigator && navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function (position) {
                that.geolocationHandleSuccess(position);
            },
            function (error) {
                that.geolocationHandleError(error);
            }
        );
    } else {
        that.geolocationHandleNotSupported();
    }
};

/** zpracovani geolokace */
Map.prototype.geolocationHandleSuccess = function (position) {
    if (typeof window.gm_geolocation_success_callbacks == 'undefined' || window.gm_geolocation_success_callbacks == null) {
        window.gm_geolocation_success_callbacks = [];
    }

    //nakonec pridam vychozi handler
    window.gm_geolocation_success_callbacks.push(this.defaultGetCurrentPositionSuccessHandler);

    for (var i = 0; i < window.gm_geolocation_success_callbacks.length; i++) {
        var callback = window.gm_geolocation_success_callbacks[i];

        if (!callback.call(this, position)) {
            break;
        }
    }
};

/**
 * vychozi handler pro uspesne zjisteni pozice geolokace
 * @param {Position} position
 * @return bool
 */
Map.prototype.defaultGetCurrentPositionSuccessHandler = function (position) {
    var map = this.map;

    map.setCenter(new google.maps.LatLng(position.coords.latitude, position.coords.longitude));
    map.setZoom(10);

    return true;
};

/** handle gelokace neni podporovana prohlizecem */
Map.prototype.geolocationHandleNotSupported = function () {
    if (typeof window.gm_geolocation_not_supported_callbacks == 'undefined' || window.gm_geolocation_not_supported_callbacks == null) {
        window.gm_geolocation_not_supported_callbacks = [];
    }

    for (var i = 0; i < window.gm_geolocation_not_supported_callbacks.length; i++) {
        var callback = window.gm_geolocation_not_supported_callbacks[i];

        if (!callback.call(this)) {
            break;
        }
    }
};

/** chyba geolokace */
Map.prototype.geolocationHandleError = function (error) {
    if (typeof window.gm_geolocation_error_callbacks == 'undefined' || window.gm_geolocation_error_callbacks == null) {
        window.gm_geolocation_error_callbacks = [];
    }

    //nakonec pridam vychozi handler
    window.gm_geolocation_error_callbacks.push(this.defaultGetCurrentPositionErrorHandler);

    for (var i = 0; i < window.gm_geolocation_error_callbacks.length; i++) {
        var callback = window.gm_geolocation_error_callbacks[i];

        if (!callback.call(this, error)) {
            break;
        }
    }
};

/**
 * vychozi handler pro neuspesne zjisteni pozice geolokace
 * @param PositionError $error
 * @return bool
 */
Map.prototype.defaultGetCurrentPositionErrorHandler = function (error) {
    if (error.code == error.POSITION_UNAVAILABLE) {
        alert(this.templates.autocompleteerror);
    }

    return true;
};

/** Handler pro naseptavac */
Map.prototype.bindAutocomplete = function () {
    var $input = $(this.config.autocompleteInputSelector);

    if ($input.length) {
        var context = this;

        // napojeni naseptavace na input
        var autocomplete = new google.maps.places.Autocomplete($input.get(0), {
            componentRestrictions: this.config.autocompleteComponentRestrictions
        });

        autocomplete.bindTo('bounds', this.map);
        autocomplete.addListener('place_changed', function () {
            var place = autocomplete.getPlace();

            context.closeInfoBoxes();

            if (!place.geometry) {
                throw("Autocomplete's returned place contains no geometry");
            }

            // If the place has a geometry, then present it on a map.
            if (place.geometry.viewport) {
                context.map.fitBounds(place.geometry.viewport);
            } else {
                context.map.setCenter(place.geometry.location);
                context.map.setZoom(context.config.autocompleteDefaultZoom);
            }
        });
    }
};

/**
 * HACK pro nezobrazovani infowindow po kliku na POI ikonky
 * http://jsfiddle.net/mrak/dHWVM/
 * http://stackoverflow.com/questions/7950030/can-i-remove-just-the-popup-bubbles-of-pois-in-google-maps-api-v3#answer-19710396
 */
Map.prototype.fixInfoWindow = function () {
    //Here we redefine set() method.
    //If it is called for map option, we hide InfoWindow, if "noSupress" option isnt true.
    //As Google doesn't know about this option, its InfoWindows will not be opened.
    var set = google.maps.InfoWindow.prototype.set;

    google.maps.InfoWindow.prototype.set = function (key, val) {
        if (key === 'map') {
            if (!this.get('noSupress')) {
                return;
            }
        }

        set.apply(this, arguments);
    }
};

/**
 * Nastavi novou sadu markeru.
 *
 * @param {object[]} markers
 */
Map.prototype.setMarkers = function (markers) {
    var ids = $.map(markers, function (marker) {
        return marker['id'];
    });

    var id, index;

    for (id in this.infoBoxes) {
        index = $.inArray(id, ids);

        if (-1 === index) {
            if (this.infoBox === this.infoBoxes[id]) {
                this.closeInfoBox();
            }

            delete this.infoBoxes[id];
        }
    }

    for (id in this.markers) {
        index = $.inArray(id, ids);

        if (-1 === index) {
            this.markers[id].setMap(null);

            delete this.markers[id];
        }
    }

    this.config.markers = markers;

    this.initMarkers();
};
