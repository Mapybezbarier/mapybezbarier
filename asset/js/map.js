
var google = {maps: {MapTypeId: {ROADMAP: SMap.DEF_BASE, HYBRID: SMap.DEF_OPHOTO}}};

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
        setGeolocationButtonSelector: '.nwjs_set_geolocation',
        autocompleteInputSelector: '#nwjs_search_place',
        autocompleteDefaultZoom: 17,
        openedInfoboxClass: 'infobox_opened'
    }, config);

    this.map = null;
    this.markers = {};
    this.infoBox = null;
    this.infoBoxes = {};

    /* mapy.cz */
    this._suggest = null;
    this._layerMarkers = null;

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
    this.map = new SMap(
        this.config.item.get(0),
        SMap.Coords.fromWGS84(this.config.map.center.lng, this.config.map.center.lat), 
        this.config.map.zoom, 
        {minZoom:0, maxZoom:18});
    window.layers = [];
    window.layers[SMap.DEF_OPHOTO] = this.map.addDefaultLayer(SMap.DEF_OPHOTO);
    window.layers[SMap.DEF_HYBRID] = this.map.addDefaultLayer(SMap.DEF_HYBRID);
    window.layers[SMap.DEF_BASE] = this.map.addDefaultLayer(SMap.DEF_BASE);
    window.layers[SMap.DEF_BASE].enable();

    /* vrstva pro poie */
    var layer = new SMap.Layer.Marker();
    this.map.addLayer(layer).enable();

    this.map.setMapTypeId = function(layer) { 
        var layers = window.layers;
        if (layer == SMap.DEF_BASE) {
            layers[SMap.DEF_BASE].enable();
            layers[SMap.DEF_OPHOTO].disable();
            layers[SMap.DEF_HYBRID].disable();
        } else {
            layers[SMap.DEF_OPHOTO].enable();
            layers[SMap.DEF_HYBRID].enable();
            layers[SMap.DEF_BASE].disable();
        }
    };

    this.map.addControl(new SMap.Control.Mouse(SMap.MOUSE_PAN | SMap.MOUSE_WHEEL | SMap.MOUSE_ZOOM, {minDriftSpeed:1/0}));
    this.map.addControl(new SMap.Control.Keyboard(SMap.KB_PAN | SMap.KB_ZOOM, {focusedOnly:false}));
    this.map.addControl(new SMap.Control.Selection(2));
    this.map.setPadding("top", 10);
    this.map.setPadding("left", 10);
    this.map.setPadding("right", 10);

    this._layerMarkers = new SMap.Layer.Marker();
    this.clusters = new SMap.Marker.Clusterer(this.map);
    this._layerMarkers.setClusterer(this.clusters);
    this.map.addLayer(this._layerMarkers).enable();
    this.map.getSignals().addListener(this, "card-open", "_markerClick");

    this.bindMapTypeChange();
    this.bindMapZoom();
    this.bindSetGeolocation();
    this.bindAutocomplete();
    this.bindEmbeddedPopupOpen();
    this.bindNewsClose();
};

/**
 * Inicializace markeru
 */
Map.prototype._markerClick = function (e) {
    this.infoBox = e.target;
    this.infoBox.getContainer().classList.add(this.config.infoBoxClass);
    this.infoBox.getBody().innerHTML = this.templates.spinner;

    var that = this;
    var config = {
        url: this.config.item.data('info-box-load-url'),
        data: {
            'map-ids': this.infoBox.object_ids
        },
        success: function (payload) {
            that.loadContentSuccessHandler(payload);
        },
        error: function () {
            that.loadContentErrorHandler()
        }
    };

    $(this.config.contentSelector).addClass(this.config.openedInfoboxClass);
    $.nette.ajax(config);
};

/**
 * nastaveni obsahu infowindow
 */
Map.prototype.setContentInfoBox = function(payload) {
    this.infoBox.getBody().innerHTML = payload;
    this.infoBox.anchorTo(this.infoBox.getAnchor());
    this.infoBox.makeVisible();
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

    this.clusters.clear();
    //this.clusters.addMarkers($.map(this.markers, function(v) { return v; }));
    for (i in this.markers) {
        this._layerMarkers.addMarker(this.markers[i]);
    }
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
    if ('undefined' == typeof marker['latitude'] || 'undefined' == typeof marker['longitude']) {
        return;
    }

    var options = { 
        title: marker['title'],
        url: marker['image'],
        size: [50, 70],
        anchor: {left: 25, top: 70}
    };
    var mapMarker = new SMap.Marker(SMap.Coords.fromWGS84(marker['longitude'], marker['latitude']), marker['id'], options);
    mapMarker.getContainer()[SMap.LAYER_MARKER].style.width = options.size[0] + "px";

    var card = new SMap.Card(300, {close: false});
    card.object_ids = marker['object_ids'];
    this.infoBoxes[marker['id']] = card;
    mapMarker.decorate(SMap.Marker.Feature.Card, card);

    return mapMarker;
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
 * Zavreni aktualniho info boxu.
 */
Map.prototype.closeInfoBox = function () {
    this.closeDetailBox();

    $('body').removeClass(this.config.openedInfoboxClass);

    if (null !== this.infoBox) {
        this.infoBox._closeClick();
        this.infoBox = null;
    }
};

/**
 * Callback uspesneho nacteni obsahu info boxu.
 * @param {string} payload
 */
Map.prototype.loadContentSuccessHandler = function (payload) {
    this.setContentInfoBox(payload);

    this.bindDetailActions();
};

/**
 * Callback neuspesneho nacteni obsahu info boxu.
 */
Map.prototype.loadContentErrorHandler = function () {
    this.setContentInfoBox(this.templates.error);
};

/**
 * Bind kliknuti na odkaz zobrazeni detailu.
 */
Map.prototype.bindDetailActions = function () {
    var context = this;

    $("." + this.config.infoBoxClass).on('click', this.config.detailOpenerSelector, function (event) {
        event.preventDefault();

        // na mobilu otevreni detailu zavre filtr a vyhledavani
        if (isMobile()) {
            $('.nwjs_filter').removeClass('opened');
            $('.nwjs_search_opener').parent().removeClass('opened');
        }

        context.openDetailBox();
    });

    $("." + this.config.infoBoxClass).on('click', this.config.infoBoxCloseSelector, function (event) {
        event.preventDefault();

        context.closeInfoBox();
    });

    $("." + this.config.infoBoxClass).on('click', this.config.detailCloserSelector, function (event) {
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
                context.map.setZoom(acutal_zoom + 1, null, true);
                break;
            case "out":
                context.map.setZoom(acutal_zoom - 1, null, true);
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
            'center-lat': context.map.getCenter().y,
            'center-lng': context.map.getCenter().x
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
    var context = this;

    if (navigator && navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function (position) {
                context.geolocationHandleSuccess(position);
            },
            function (error) {
                context.geolocationHandleError(error);
            }
        );
    } else {
        context.geolocationHandleNotSupported();
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

    this.map.setCenterZoom(SMap.Coords.fromWGS84(position.coords.longitude, position.coords.latitude), 10, true);

    return true;
};

/**
 * handle gelokace neni podporovana prohlizecem
 */
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

/** 
 *chyba geolokace 
 */
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
    var input = $(this.config.autocompleteInputSelector);
    this._suggest = Suggest.getInstance();
    this._suggest.map = this;
    this._suggest.setInput(input.get(0));
    this._suggest.addListener("suggest-submit", "_suggestSubmit");
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
            this._layerMarkers.removeMarker(this.markers[id]);

            delete this.markers[id];
        }
    }

    this.config.markers = markers;

    this.initMarkers();
};
