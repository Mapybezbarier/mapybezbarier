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
        infoBoxDefaultZoom: 15,
        setGeolocationButtonSelector: '.nwjs_set_geolocation',
        autocompleteInputSelector: '#nwjs_search_place',
        autocompleteRouteFromInputSelector: '#nwjs_route_from',
        autocompleteRouteToInputSelector: '#nwjs_route_to',
        autocompleteDefaultZoom: 17,
        openedInfoboxClass: 'infobox_opened'
    }, config);

    this.map = null;
    this.markers = {};
    this.infoBox = null;
    this.infoBoxes = {};

    this.routeFrom = null;
    this.routeTo = null;
    this.obstacle = '1';

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
    this._mapLayer.bindAutocompleteRoute(this.config.autocompleteRouteFromInputSelector, this.setRouteFrom.bind(this));
    this._mapLayer.bindAutocompleteRoute(this.config.autocompleteRouteToInputSelector, this.setRouteTo.bind(this));
    this.bindEmbeddedPopupOpen();
    this.bindNewsClose();

    this.bindRoute();
};

var HttpClient = function() {
    this.get = function(aUrl, aCallback) {
        var anHttpRequest = new XMLHttpRequest();
        anHttpRequest.onreadystatechange = function() {
            if (anHttpRequest.readyState == 4 && anHttpRequest.status == 200)
                aCallback(anHttpRequest.responseText);
        }

        anHttpRequest.open( "GET", aUrl, true );
        anHttpRequest.send( null );
    }
}

MapWrapper.prototype.setRouteFrom = function(place) {
    this.routeFrom = place;
};

MapWrapper.prototype.setRouteTo = function(place) {
    this.routeTo = place;
};

MapWrapper.prototype.bindRoute = function () {

    var context = this;


    $('.route_toolbox_item').on('click', function (e) {
        var element = $(this);
        context.obstacle = element.attr('data-obstacle');
        element.parent().children().removeClass('active');
        element.addClass('active');
    });

    $('#nwjs_route_trigger').on('click', function () {

        if (context.routeFrom != null && context.routeTo != null) {
            var client = new HttpClient();
                       https://cws.kedros.sk/msol-web-routing/services/route-detail
            var url = 'https://cws.kedros.sk/msol-web-routing/services/route';
            url += '?routingMode=7';
            url += '&routingType=kdr';
            url += '&type=2';
            url += '&from=' + context.routeFrom.lng + ',' + context.routeFrom.lat;
            //url += '&via=';
            url += '&to=' + context.routeTo.lng + ',' + context.routeTo.lat;

            url += '&compress=0';
            url += '&zoomlevel=' + context.map.getZoom();
            url += '&wObstacle=' + context.obstacle;

            console.log(url);

            var coords = [[[14.4210240273681,50.0773258577036],[14.4208898010821,50.0768552836803]],[[14.42089,50.076854999999995],[14.420838,50.076682],[14.420639,50.076705],[14.420594999999999,50.076682999999996],[14.420378,50.076707999999996],[14.420338,50.076679],[14.420223,50.076665999999996],[14.420195,50.076595999999995],[14.420039,50.076615999999994],[14.419767,50.076651999999996],[14.419716,50.076527999999996],[14.419602,50.076346],[14.419547999999999,50.076232],[14.419538,50.076145],[14.419545999999999,50.076054],[14.419388999999999,50.075983],[14.419315999999998,50.075896],[14.41931,50.075872],[14.419293,50.075809],[14.419286999999999,50.075786],[14.419232,50.07557],[14.419193,50.07542],[14.419310999999999,50.075406],[14.41922,50.074818],[14.419146999999999,50.074439],[14.419098,50.074349999999995],[14.419074,50.074284999999996],[14.419089999999999,50.074200999999995],[14.41911,50.074017],[14.419101999999999,50.073921]],[[14.4188778657756,50.0736348966189],[14.4189038621339,50.0736671221736],[14.4189360195387,50.0737777327498],[14.4189918172247,50.0738368953505],[14.4191017133217,50.0739206758533]]];
            context._mapLayer.showRoute(coords);

            /*
            client.get(url, function (response) {
                console.log(response);
                var route = JSON.parse(response);

                if (route.resultStatus == "OK") {
                    if (route.segmentGeometry && route.segmentGeometry.length > 0) {
                        var coords = route.segmentGeometry[0].geom.coordinates;
                        context._mapLayer.showRoute(coords);
                    }

                    //route.routingID
                }
            });*/

            var instructions = {
                "routingID": null,
                "segmentGeometry": [],
                "routeObjects": [],
                "obstructionSummary": [],
                "length": 797.6097,
                "time": 695.9621,
                "delay": null,
                "startStop": "16.61016,49.194332",
                "endStop": "16.602604,49.195328",
                "viaStop": "",
                "resultStatus": "OK",
                "instructions": [
                    {
                        "instruction": 10,
                        "name": "Poštovská",
                        "length": 144.36499155071095,
                        "lon": 16.610565,
                        "lat": 49.194498
                    },
                    {
                        "instruction": 8,
                        "name": "Kobližná",
                        "length": 72.2450979987,
                        "lon": 16.609809,
                        "lat": 49.195337
                    },
                    {
                        "instruction": 1,
                        "name": "náměstí Svobody",
                        "length": 144.48620572619998,
                        "lon": 16.608827,
                        "lat": 49.195398
                    },
                    {
                        "instruction": 8,
                        "name": "Středova",
                        "length": 55.2192552276,
                        "lon": 16.607067,
                        "lat": 49.195387
                    },
                    {
                        "instruction": 3,
                        "name": "Veselá",
                        "length": 58.2107939557,
                        "lon": 16.606411,
                        "lat": 49.19514
                    },
                    {
                        "instruction": 7,
                        "name": "",
                        "length": 151.65714523146,
                        "lon": 16.60605,
                        "lat": 49.195605
                    },
                    {
                        "instruction": 7,
                        "name": "Husova",
                        "length": 13.8319789395,
                        "lon": 16.604104,
                        "lat": 49.195273
                    },
                    {
                        "instruction": 3,
                        "name": "Hlídka",
                        "length": 34.4173086023,
                        "lon": 16.60415,
                        "lat": 49.195153
                    },
                    {
                        "instruction": 1,
                        "name": "",
                        "length": 122.27870642247579,
                        "lon": 16.603775,
                        "lat": 49.19498
                    },
                    {
                        "instruction": 15,
                        "name": "",
                        "length": 0,
                        "lon": 16.602691,
                        "lat": 49.195307
                    }
                ],
                "instructionsR4A": null
            };
        }

/*
        var coords =
                [[[14.4210240273681,50.0773258577036],[14.4208898010821,50.0768552836803]],[[14.42089,50.076854999999995],[14.420838,50.076682],[14.420639,50.076705],[14.420594999999999,50.076682999999996],[14.420378,50.076707999999996],[14.420338,50.076679],[14.420223,50.076665999999996],[14.420195,50.076595999999995],[14.420039,50.076615999999994],[14.419767,50.076651999999996],[14.419716,50.076527999999996],[14.419602,50.076346],[14.419547999999999,50.076232],[14.419538,50.076145],[14.419545999999999,50.076054],[14.419388999999999,50.075983],[14.419315999999998,50.075896],[14.41931,50.075872],[14.419293,50.075809],[14.419286999999999,50.075786],[14.419232,50.07557],[14.419193,50.07542],[14.419310999999999,50.075406],[14.41922,50.074818],[14.419146999999999,50.074439],[14.419098,50.074349999999995],[14.419074,50.074284999999996],[14.419089999999999,50.074200999999995],[14.41911,50.074017],[14.419101999999999,50.073921]],[[14.4188778657756,50.0736348966189],[14.4189038621339,50.0736671221736],[14.4189360195387,50.0737777327498],[14.4189918172247,50.0738368953505],[14.4191017133217,50.0739206758533]]]
            ;

        coords.forEach(function(b){
            var ll = [];
            b.forEach(function(c) {
                ll.push({
                    lat: c[1],
                    lng: c[0]
                });
            })

            var _path = new google.maps.Polyline({
                path: ll,
                geodesic: true,
                strokeColor: '#FF0000',
                strokeOpacity: 1.0,
                strokeWeight: 2
            });

            _path.setMap(context.map);

        }.bind(this));
*/
    });

}


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
            $('.nwjs_route_opener').parent().removeClass('opened');
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
                context.map.setMapTypeId("roadmap");
                break;
            case "hybrid":
                context.map.setMapTypeId("hybrid");
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

        if (!callback.call(this._mapLayer, position)) {
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
    // Nova sada markeru a objektu muze mit i pri stejnem ID jiny obsah (napr. v zavislosti na typu filtrovani)
    // Zavri a smaz vsechny aktualni infoboxy
    this.closeInfoBox();
    this.infoBoxes = {};

    // Smaz vsechny soucasne markery
    this.markers = {};

    this.config.markers = markers;

    this.initMarkers();
};
