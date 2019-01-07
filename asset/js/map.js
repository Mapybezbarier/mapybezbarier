$(document).ready(function () {
    // gmap
    window.googleMap = bindMap();

    // na mobilu kliknuti do mapy zavre filtr
    bindClickTheMapInside();
});

$(window).resize(function () {
    // na mobilu kliknuti do mapy zavre filtr
    bindClickTheMapInside();
});

/**
 * Obluha mapy
 */
function bindMap() {
    var config = {
        item: $('#map'),
        map: mapConfig
    };

    return new MapWrapper(config);
}

/**
 * Na mobilu kliknuti do mapy zavre filtr
 */
function bindClickTheMapInside() {
    $('#map').on('click', function () {
        if (isMobile()) {
            $('.nwjs_filter').removeClass('opened');
        }
    });
}

/**
 * @param {object} config
 * @constructor
 */
var MapWrapper = function (config) {
    this.config = $.extend({
        item: null,
        object: null,
        map: {},
        markers: [],
        contentSelector: ".content",
        filterContentSelector: ".filters",
        detailOpenerSelector: '.nwjs_detail_opener',
        detailCloserSelector: '.nwjs_detail_closer',
        detailContentSelector: '.detail',
        detailSelector: '.nwjs_detail',
        detailOpenClass: 'opened',
        detailDefaultZoom: 17,
        newsSelector: '.nwjs_news',
        newsCloseSelector: '.nwjs_close_news',
        mapTypeChangeSelector: '.nwjs_change_map_type',
        mapTypeChangeActiveClass: 'active',
        mapZoomSelector: '.nwjs_map_zoom',
        infoBoxCloseSelector: '.nwjs_infobox_closer',
        infoBoxClass: 'info_box',
        infoBoxDefaultZoom: 10,
        setGeolocationButtonSelector: '.nwjs_set_geolocation',
        autocompleteInputSelector: '#nwjs_search_place',
        autocompleteDefaultZoom: 17,
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
MapWrapper.prototype.init = function () {
    this.initTemplates();

    if (this.config.item !== undefined && this.config.item.length) {
        this.initMap();
    }
};

/**
 * Inicializace mapy.
 */
MapWrapper.prototype.initMap = function () {
    var context = this;

    this._mapLayer = MapLayer;
    this._mapLayer.addInitMapCallback(function() {
        context.loadMarkers();
    });
    this._mapLayer.initMap(this);

    this.bindMapTypeChange();
    this.bindMapZoom();
    this.bindSetGeolocation();
    this._mapLayer.bindAutocomplete(this.config.autocompleteInputSelector);
    this.bindEmbeddedPopupOpen();
    this.bindNewsClose();

    this.initializeGeolocation();
};

/**
 * Inicializace markeru
 */
MapWrapper.prototype.markerClick = function (e) {
    var object_ids = this._mapLayer.markerClick(e);

    if (object_ids) {
        var context = this;
        var config = {
            url: this.config.item.data('info-box-load-url'),
            data: {
                'map-ids': object_ids
            },
            success: function (payload) {
                context.loadContentSuccessHandler(payload);
            },
            error: function () {
                context.loadContentErrorHandler()
            }
        };

        $(this.config.contentSelector).addClass(this.config.openedInfoboxClass);

        $.nette.ajax(config);
    }
};

/**
 * Nacteni markeru
 */
MapWrapper.prototype.loadMarkers = function () {
    var context = this;
    var config = {
        url: this.config.item.data('markers-load-url'),
        beforeSend: function () {
            context.config.item.spin(getDefaultSpinner({
                top: "35%"
            }));
        },
        complete: function() {
            context.config.item.spin(false);
        }
    };

    $.nette.ajax(config);
};

/**
 * Inicializace markeru
 */
MapWrapper.prototype.initMarkers = function () {
    var context = this;

    for (var i = 0, count = this.config.markers.length; i < count; i++) {
        var marker = this.config.markers[i];

        marker.active = (this.config.object && -1 !== $.inArray(this.config.object['object_id'], marker.object_ids));

        if (this._mapLayer.checkMarkerInBounds(marker)) {
            if (undefined === this.markers[marker['id']]) {
                this.markers[marker['id']] = this._mapLayer.prepareMarker(marker);
            }
        }
    }

    this._mapLayer.initMarkers();

    // volani initMarkers po pristi zmene ranges na mape
    this._mapLayer.addInitMapCallback(function() {
        context.initMarkers();
    });
};

/**
 * Inicizalice sablon
 */
MapWrapper.prototype.initTemplates = function () {
    for (var template in this.templates) {
        var $template = $('#info-box-template-' + template);

        this.templates[template] = $template.html();

        $template.remove();
    }
};

/**
 * Callback pro otevreni detailu.
 */
MapWrapper.prototype.openDetailBox = function () {
    $(this.config.detailSelector).addClass(this.config.detailOpenClass);
    this.closeNews();
};

/**
 * Callback pro zavreni detailu.
 */
MapWrapper.prototype.closeDetailBox = function () {
    $(this.config.detailSelector).removeClass(this.config.detailOpenClass);
    $(this.config.detailSelector).find(this.config.detailContentSelector).empty();
};

/**
 * Callback pro zavreni novinek.
 */
MapWrapper.prototype.closeNews = function () {
    $(this.config.newsSelector).removeClass(this.config.detailOpenClass);
};

/**
 * Zavreni aktualniho info boxu.
 */
MapWrapper.prototype.closeInfoBox = function () {
    this.closeDetailBox();

    $('body').removeClass(this.config.openedInfoboxClass);

    if (null !== this.infoBox) {
        this._mapLayer.closeInfoBox();
        this.infoBox = null;
    }
};

/**
 * Callback uspesneho nacteni obsahu info boxu.
 * @param {string} payload
 */
MapWrapper.prototype.loadContentSuccessHandler = function (payload) {
    this.infoBox.setContent(payload);

    this.bindDetailActions();
};

/**
 * Callback neuspesneho nacteni obsahu info boxu.
 */
MapWrapper.prototype.loadContentErrorHandler = function () {
    this.infoBox.setContent(this.templates.error);
};

/**
 * Bind kliknuti na odkaz zobrazeni detailu.
 */
MapWrapper.prototype.bindDetailActions = function () {
    var context = this;
    var $infoBox = $("." + this.config.infoBoxClass);

    $infoBox.on('click', this.config.detailOpenerSelector, function (event) {
        event.preventDefault();

        // na mobilu otevreni detailu zavre filtr a vyhledavani
        if (isMobile()) {
            $('.nwjs_filter').removeClass('opened');
            $('.nwjs_search_opener').parent().removeClass('opened');
        }

        context.openDetailBox();
    });

    $infoBox.on('click', this.config.infoBoxCloseSelector, function (event) {
        event.preventDefault();

        context.closeInfoBox();
    });

    $infoBox.on('click', this.config.detailCloserSelector, function (event) {
        event.preventDefault();

        context.closeDetailBox();
    });
};

/**
 * Bind kliknuti na krizek zavreni novinek
 */
MapWrapper.prototype.bindNewsClose = function () {
    var context = this;

    $(this.config.newsSelector).on('click', this.config.newsCloseSelector, function (event) {
        context.closeNews();
    });
};

/**
 * Bind zmena typu mapy
 */
MapWrapper.prototype.bindMapTypeChange = function () {
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
MapWrapper.prototype.bindMapZoom = function () {
    var context = this;

    $('body').on('click', this.config.mapZoomSelector, function () {
        var acutal_zoom = context.map.getZoom();

        switch ($(this).attr('data-type')) {
            case "in":
                context._mapLayer.setZoom(acutal_zoom + 1);
                break;
            case "out":
                context._mapLayer.setZoom(acutal_zoom - 1);
                break;
        }
    });
};

/**
 * Bind generovani odkazu na vlozenou mapu
 */
MapWrapper.prototype.bindEmbeddedPopupOpen = function () {
    var context = this;

    $('.nwjs_embedded_popup_opener').on('click', function (event) {
        event.preventDefault();

        var url = $(this).attr('href');

        var center = context._mapLayer.getCenter();
        var data = {
            'zoom': context.map.getZoom(),
            'center-lat': center.y,
            'center-lng': center.x
        };

        if (typeof window.SMap === 'undefined') {
            data.maps = 1;
        }

        loadPopupContent(url, data);
    });
};

/**
 * Bind nastaveni geolokace
 */
MapWrapper.prototype.bindSetGeolocation = function () {
    var context = this;

    $(this.config.filterContentSelector).on('click', this.config.setGeolocationButtonSelector, function (event) {
        context.initializeGeolocation();
    });
};

/** init geolokace */
MapWrapper.prototype.initializeGeolocation = function () {
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
MapWrapper.prototype.geolocationHandleSuccess = function (position) {
    if (typeof window.gm_geolocation_success_callbacks === 'undefined' || window.gm_geolocation_success_callbacks == null) {
        window.gm_geolocation_success_callbacks = [];
    }

    //nakonec pridam vychozi handler
    window.gm_geolocation_success_callbacks.push(this._mapLayer.defaultGetCurrentPositionSuccessHandler);

    for (var i = 0; i < window.gm_geolocation_success_callbacks.length; i++) {
        var callback = window.gm_geolocation_success_callbacks[i];

        if (!callback.call(this, position)) {
            break;
        }
    }
};

/**
 * handle gelokace neni podporovana prohlizecem
 */
MapWrapper.prototype.geolocationHandleNotSupported = function () {
    if (typeof window.gm_geolocation_not_supported_callbacks === 'undefined' || window.gm_geolocation_not_supported_callbacks == null) {
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
MapWrapper.prototype.geolocationHandleError = function (error) {
    if (typeof window.gm_geolocation_error_callbacks === 'undefined' || window.gm_geolocation_error_callbacks == null) {
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
MapWrapper.prototype.defaultGetCurrentPositionErrorHandler = function (error) {
    if (error.code == error.POSITION_UNAVAILABLE) {
        alert(this.templates.autocompleteerror);
    }

    return true;
};

/**
 * Nastavi mapovy objekt.
 *
 * @param {object} object
 */
MapWrapper.prototype.setObject = function (object) {
  this.config.object = object;
};

/**
 * Nastavi novou sadu markeru.
 *
 * @param {object[]} markers
 */
MapWrapper.prototype.setMarkers = function (markers) {
    var ids = markers.reduce(function(ret, marker) {
        ret[marker['id']] = true;
        return ret;
    }, {});

    var id;

    for (id in this.infoBoxes) {
        if (!ids.hasOwnProperty(id)) {
            if (this.infoBox === this.infoBoxes[id]) {
                this.closeInfoBox();
            }

            delete this.infoBoxes[id];
        }
    }

    var removeMarkers = [];

    for (id in this.markers) {
        if (!ids.hasOwnProperty(id)) {
            removeMarkers.push(this.markers[id]);

            delete this.markers[id];
        }
    }

    this._mapLayer.removeMarkers(removeMarkers);

    this.config.markers = markers;

    this.initMarkers();
};
