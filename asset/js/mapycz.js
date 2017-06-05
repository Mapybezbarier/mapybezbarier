MapLayer = {};

MapLayer.initMap = function (map) {
	this._map = map;
    this._map.map = new SMap(
        this._map.config.item.get(0),
        SMap.Coords.fromWGS84(this._map.config.map.center.lng, this._map.config.map.center.lat), 
        this._map.config.map.zoom, 
        {minZoom:7, maxZoom:18});
    window.layers = [];
    window.layers[SMap.DEF_OPHOTO] = this._map.map.addDefaultLayer(SMap.DEF_OPHOTO);
    window.layers[SMap.DEF_HYBRID] = this._map.map.addDefaultLayer(SMap.DEF_HYBRID);
    window.layers[SMap.DEF_BASE] = this._map.map.addDefaultLayer(SMap.DEF_BASE);
    window.layers[SMap.DEF_BASE].enable();

    /* vrstva pro poie */
    var layer = new SMap.Layer.Marker();
    this._map.map.addLayer(layer).enable();
    this._map.map.addControl(new SMap.Control.Mouse(SMap.MOUSE_PAN | SMap.MOUSE_WHEEL | SMap.MOUSE_ZOOM, {minDriftSpeed:1/0}));
    this._map.map.addControl(new SMap.Control.Keyboard(SMap.KB_PAN | SMap.KB_ZOOM, {focusedOnly:false}));
    this._map.map.addControl(new SMap.Control.Selection(2));
    this._map.map.setPadding("top", 10);
    this._map.map.setPadding("left", 10);
    this._map.map.setPadding("right", 10);

    this._layerMarkers = new SMap.Layer.Marker();
    this.clusters = new SMap.Marker.Clusterer(this._map.map);
    this._layerMarkers.setClusterer(this.clusters);
    this._map.map.addLayer(this._layerMarkers).enable();
    this._map.map.getSignals().addListener(this._map, "card-open", "markerClick");
};

MapLayer.setMarkers = function(marker) {
	this._layerMarkers.removeMarker(marker);
}

/** Handler pro naseptavac */
MapLayer.bindAutocomplete = function (input) {
    var $input = $(input);
    if ($input.length) {
	    this._suggest = Suggest.getInstance();
	    this._suggest.map = this._map;
	    this._suggest.setInput($input.get(0));
	    this._suggest.addListener("suggest-submit", "_suggestSubmit");
	}
};

/**
 * vychozi handler pro uspesne zjisteni pozice geolokace
 * @param {Position} position
 * @return bool
 */
MapLayer.defaultGetCurrentPositionSuccessHandler = function (position) {

    this.map.setCenterZoom(SMap.Coords.fromWGS84(position.coords.longitude, position.coords.latitude), 10, true);

    return true;
};

MapLayer.initMarkers = function() {
	this.clusters.clear();
    for (i in this._map.markers) {
        this._layerMarkers.addMarker(this._map.markers[i]);
    }
}

MapLayer.closeInfoBox = function () {
    this._map.infoBox._closeClick();
}

MapLayer.getCenter = function() {
	return this._map.map.getCenter();
}

MapLayer.markerClick = function(e) {
	this._map.infoBox = e.target;
    this._map.infoBox.getContainer().classList.add(this._map.config.infoBoxClass);
    this._map.infoBox.getBody().innerHTML = this._map.templates.spinner;

    return this._map.infoBox.object_ids;
}

/**
 * Priprava markeru.
 * @param {object} marker
 * @returns {google.maps.Marker}
 */
MapLayer.prepareMarker = function (marker) {
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
    this._map.infoBoxes[marker['id']] = card;
    mapMarker.decorate(SMap.Marker.Feature.Card, card);

    return mapMarker;
};

MapLayer.setZoom = function(zoom) {
	this._map.map.setZoom(zoom, null, true);
};


//zmena mapovych podkladu
var google = {maps: {MapTypeId: {ROADMAP: SMap.DEF_BASE, HYBRID: SMap.DEF_OPHOTO}}};

/**
 * změna mapovych podkladu
 */
SMap.prototype.setMapTypeId = function(layer) { 
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

/**
* nastaveni obsahu vizitky
*/
SMap.Card.prototype.setContent = function(payload) {
	this.getBody().innerHTML = payload;
    this.anchorTo(this.getAnchor());
    this.makeVisible();
}

/**
 * vytvoreni velikosti clusteru dle vlastnich pravidel, nastaveni bile
 * @param {function} [options.radius=25+10*(count-min+1)/(max-min+1)] Výpočet poloměru; vstupem je počet, minimum a maximum
 */
SMap.Marker.Cluster.prototype.$constructor = function(id, options) {
    var markerOptions = {
        url: JAK.mel("div", {className:"cluster"}),
        anchor: {left:0, top:0}
    };
    this.$super(null, id, markerOptions);

    this._clusterOptions = {
        color: "#fff",
        radius: function(count, min, max) { return (count < 10? 30 : (count < 100? 35 : 42.5)); }
    }
    for (var p in options) { this._clusterOptions[p] = options[p]; }

    this._dom.content = JAK.mel("span");
    this._dom.circle = JAK.mel("div", {}, {color:this._clusterOptions.color});

    this._dom.container[SMap.LAYER_MARKER].appendChild(this._dom.circle);
    this._dom.circle.appendChild(this._dom.content);
    this._dom.circle.appendChild(JAK.mel("img", {src:SMap.CONFIG.img + "/marker/cluster.png"}, {backgroundColor:this._clusterOptions.color}));

    this._markers = [];
    this._markerCoords = [];
}

/**
 * ruzne styly dle poctu markeru v clustru
 */
SMap.Marker.Cluster.prototype._update = function() {
    var minX = Infinity;
    var minY = Infinity;
    var maxX = -Infinity;
    var maxY = -Infinity;
    var count = this._markers.length;
    this._dom.content.innerHTML = count;
    this._dom.circle.title = count;

    var scale = 1000;
    if (count < 10) {
        scale = 10;
    } else if (count < 100) {
        scale = 100;
    }
    this._dom.circle.setAttribute("data-scale", scale);

    for (var i=0;i<count;i++) {
        var item = this._markerCoords[i];
        minX = Math.min(minX, item.x);
        minY = Math.min(minY, item.y);
        maxX = Math.max(maxX, item.x);
        maxY = Math.max(maxY, item.y);
    }

    var x = (minX+maxX)/2;
    var y = (minY+maxY)/2;
    this.setCoords(new SMap.Coords(x, y));
}

/**
 * @overview Suggest NG - naseptavac nove generace
 * @version 4.9
 * @author zara, mosner
 */

/**
 * @class Trida, poskytujici "naseptavac" z dane adresy pro zadany inputbox
 * @signal suggest-submit Pouzito
 * @signal suggest-keyboard Nastala nejaka forma interakce s uzivatelem
 * @signal suggest-touch Nastala nejaka forma interakce s uzivatelem
 * @signal suggest-mouse Nastala nejaka forma interakce s uzivatelem
 */
JAK.Suggest = JAK.ClassMaker.makeClass({
	NAME: "JAK.Suggest",
	VERSION: "4.9",
	IMPLEMENT: JAK.ISignals
});

/**
 * @param {string} id ID inputboxu, na ktery bude suggest navesen
 * @param {string} url adresa, na kterou se provadi zadost; musi byt v domene dokumentu!
 * @param {object} [options] Hash s dodatecnymi opsnami
 * @param {string} [options.dict=""] nazev slovniku
 * @param {int} [options.count=10] pocet vysledku
 * @param {bool} [options.prefix=false] ma-li se pouzit prefixove hledani
 * @param {bool} [options.highlight=false] ma-li se ve vysledcich zvyraznit hledany (pod)retezec
 * @param {object} [options.parentElement=id.form] rodic
 * @param {int} [options.timeout=80] jak dlouho po zastaveni psani poslat request
 * @param {int} [options.valueCheckDelay=100] interval kontroly vstupního inputu v případě, že nepodporuje událost oninput
 * @param {bool} [options.autoSubmit=true] Odeslat formular po aktivaci?
 * @param {object} [options.itemMap={slovnik: JAK.Suggest.Dictionary, item: JAK.Suggest.Term} druhy položek v našeptávači
 */
JAK.Suggest.prototype.$constructor = function(id, url, options) {
	this._ec = []; /* event cache */
	this._dom = {};
	this._url = url;

	/* Výchozí nastavení předvoleb */
	this._options = {
		dict: "",
		count: 10,
		prefix: false,
		highlight: false,
		parentElement: id && JAK.gel(id).form,
		timeout: 80,
		valueCheckDelay: 100,
		autoSubmit: true,
		format: JAK.Request.XML,
		itemMap: {
			slovnik: JAK.Suggest.Dictionary,
			miniapps: JAK.Suggest.Miniapps,
			item: JAK.Suggest.Term
		}
	}

	/* Předvolby předané scriptu (přepíšeme ty výchozí) */
	this.setOptions(options);

	this._items = [];
	this._navigableItems = [];

	this._query = "";			/* aktualne polozeny a poslany dotaz */
	this._rq = null;			/* poslany pozadavek, aby se dal abortnout */
	this._timeout = false;		/* zpozdeni poslani pozadavku pri psani */
	this._activeItem = null;	/* prave modra polozka */
	this._opened = false;		/* jsme videt? */
	this._request = this._request.bind(this); /* metoda na poslani pozadavku; zbindovana, aby se dala volat v timeoutu */
	this._stepUpDown = false;	/* pomocná proměnná pro prohlížeče nepodporující oninput (IE8 a nižší), zabraňuje poslání requestu při procházení položek */
	this._touched = false;

	/* hover lock: po zobrazeni suggestu nesmi nastat hover/mouseover na polozce; tato promenna to zakaze (a povoli az po skutecne posunu mysi) */
	this._hoverLock = false;

	this._build(id);
};

JAK.Suggest.prototype.$destructor = function() {
	this._clear();
	JAK.Events.removeListeners(this._ec);
};

/**
 * Za behu zmeni opsny
 */
JAK.Suggest.prototype.setOptions = function(options) {
	for (var p in options) { this._options[p] = options[p]; }
};

/**
 * Vratí nastavení
 */
JAK.Suggest.prototype.getOption = function(option) {
	return this._options[option];
};

/**
 * @returns {string} vrací query, ktere bylo hledano
 */
JAK.Suggest.prototype.getQuery = function() {
	return this._query;
};

/**
 * @returns {node} vrací hledací políčko
 */
JAK.Suggest.prototype.getInput = function() {
	return this._dom.input;
};

/**
 * @returns {node} vrací kontejner našeptávaných slov
 */
JAK.Suggest.prototype.getContainer = function() {
	return this._dom.container;
};

/**
 * @returns {bool} vrací zda je zobrazen našeptávač či nikoliv
 */
JAK.Suggest.prototype.isOpen = function() {
	return this._opened;
};

/**
 * Zavola se pri "aktivaci" vybrane polozky (kliknuti, enter). Defaultne submitne formular.
 */
JAK.Suggest.prototype.action = function(e) {
	if (this._timeout) {
		clearTimeout(this._timeout);
		this._timeout = false;
	}

	var bound = function() {
		this._hide();
		this._dom.input.blur();
	};
	bound = bound.bind(this);

	if ("ontouchend" in window) {
		/* pockame se schovavanim; kdyby nekdo delal doubletouch, aby nekliknul na to pod nami */
		var t = setTimeout(bound, 400);
	} else {
		this._hide();
	}

	var parent = this._options.parentElement;
	this.makeEvent("suggest-submit");

	if (this._options.autoSubmit && parent && parent.submit) { parent.submit(); }
};

/**
 * Vrati prave vybranou polozku (instance JAK.Suggest.Item/JAK.Suggest.Term)
 */
JAK.Suggest.prototype.getActive = function() {
	return this._activeItem;
};

/**
 * Vrátí index pravě vybrané položky (instance JAK.Suggest.Item/JAK.Suggest.Term)
 */
JAK.Suggest.prototype.getActiveIndex = function() {
	if (!this._activeItem) { return -1; }
	return this._navigableItems.indexOf(this._activeItem);
};

/**
 * Vytvoření html elementů
 */
JAK.Suggest.prototype._build = function(id) {
	var input = JAK.gel(id);

	var container = JAK.mel("div", {className:"suggest"}, {display:"none"});
	this._options.parentElement.appendChild(container);

	var content = JAK.mel("div", {className:"content"});
	container.appendChild(content);

	this._dom.input = input;
	this._dom.container = container;
	this._dom.content = content;

	this._options.parentElement.appendChild(container);

	this._ec.push(JAK.Events.addListener(input, "keydown", this, "_keydown"));
	this._ec.push(JAK.Events.addListener(input, "focus", this, "_focus"));

	/* Pokud nemáme k dispozici událost oninput, musíme kontrolovat pole v intervalu */
	if ("oninput" in input) {
		this._ec.push(JAK.Events.addListener(input, "input", this, "_valueCheck"));
	} else {
		setInterval(this._valueCheck.bind(this), this._options.valueCheckDelay); /* IE8 a nižší */
	}

	if ("autocorrect" in input) {
		/* This is a nonstandard attribute supported by Safari
		that is used to control whether autocorrection should be
		enabled when the user is entering/editing the text
		value of the <input>. */
		input.setAttribute("autocorrect", "off");
	}
	input.autocomplete = "off";

	var touchBlur = function(e, elm) {
		if (JAK.DOM.getStyle(this._dom.container, "position") == "absolute") {
			this._blur(e, elm);
		} else {
			return;
		}
	}

	this._ec.push(JAK.Events.addListener(container, "mousedown", function(e) {
		JAK.Events.stopEvent(e);
		JAK.Events.cancelDef(e);
	})); /* aby nedobublal az nahoru, kde to zavre suggest */
	this._ec.push(JAK.Events.addListener(container, "touchstart", JAK.Events.stopEvent));
	this._ec.push(JAK.Events.addListener(container, "mousemove", this, "_unlock")); /* viz _hoverLock; povoli hoverovani polozek */
	this._ec.push(JAK.Events.addListener(document, "mousedown", this, "_blur")); /* schovat */
	this._ec.push(JAK.Events.addListener(document, "touchstart", touchBlur.bind(this))); /* schovat pouze tehdy když našeptávač floatuje */
};

/**
 * Je-li políčko pro dotaz prázdné, skryjeme suggest
 */
JAK.Suggest.prototype._valueCheck = function(e, elm) {
	if (!this._dom.input.value) { 
		this._hide();
		this._clear();
		return;
	}

	/* zašleme request, pokud došlo ke změně vyhledávacího pole, například po smazání části dotazu myší (výběrem) */
	if (this._dom.input.value != this._query && !this._stepUpDown) { this._startRequest(); }
};

JAK.Suggest.prototype._show = function() {
	if (this._opened) { return; }
	this._hoverLock = true;
	this._opened = true;
	this._dom.container.style.display = "";
	this._updateWidth();
};

/**
 * Skryje našeptávač
 */
JAK.Suggest.prototype._hide = function() {
	if (this._rq) { this._rq.abort(); }
	if (this._timeout) { clearTimeout(this._timeout); }
	if ( !this._opened ) { return; }
	this._dom.container.style.display = "none";
	this._opened = false;
};

JAK.Suggest.prototype._updateWidth = function() {
	this._dom.container.style.width = this._dom.input.offsetWidth + "px";
}

JAK.Suggest.prototype._clear = function() {
	JAK.DOM.clear(this._dom.content);
	while (this._items.length) { this._items.shift().$destructor(); }
	this._navigableItems = [];
	this._activeItem = null;
};

JAK.Suggest.prototype._startRequest = function() {
	if (this._timeout) { clearTimeout(this._timeout); }
	this._timeout = setTimeout(this._request, this._options.timeout);
};

/**
 * Posle XMLHttpRequest
 */
JAK.Suggest.prototype._request = function() {
	this._timeout = false;
	if (this._dom.input.value.trim().length == 0) { return; }
	this._query = this._dom.input.value;
	var url = this._buildUrl(this._query);
	
	/* pokud request jiz bezi, zabijeme ho */
	if (this._rq) { this._rq.abort(); }

	this._rq = new JAK.Request(this._options.format);
	this._rq.setCallback(this, "_response");
	this._rq.send(url);
};

JAK.Suggest.prototype._response = function(data, status) {
	this._rq = null;
	this._timeout = false;
	this._clear();

	this._buildItems(data);

	if (this._items.length) {
		this._show();
	} else {
		this._hide();
	}
};

JAK.Suggest.prototype._buildItems = function(xmlDoc) {
	if (!xmlDoc) { return; }

	var appName, itemCtor;

	var result = xmlDoc.documentElement;
	var items = result.getElementsByTagName("item");
	var remote = result.getElementsByTagName("remote")[0];
	if (remote) {
		var miniApps = remote.getElementsByTagName("suggest");
	} else {
		var miniApps = [];
	}

	for (var i=0;i<items.length;i++) {
		var item = items[i];
		if (item.parentNode.parentNode.nodeName.toLowerCase() == "remote") {
			appName = item.parentNode.getAttribute('name');
			/* Dočasné řešení jak odmazat slovnik z vysledku pokud je vice miniaplikaci. V budoucnu predelano do backendu */
			if (miniApps.length >= 2 && appName == "slovnik") { break; }
			itemCtor = (this._options.itemMap[appName] || JAK.Suggest.Item);
			this._buildItem(itemCtor, item);
		} else {
			this._buildItem(this._options.itemMap.item, item);
		}
	}

	for (var i=0;i<this._items.length;i++) {
		this._dom.content.appendChild(this._items[i].getContainer());
	}
}

JAK.Suggest.prototype._buildItem = function(constructor, node) {
	var item = new constructor(this, node);
	this._items.push(item);
	if (item.isNavigable()) { this._navigableItems.push(item); }
};

JAK.Suggest.prototype._buildUrl = function(query) {
	var url = this._url;
	if (url.charAt(url.length-1) != "/") { url += "/"; }
	url += this._options.dict;
	var arr = [];
	arr.push("phrase="+encodeURIComponent(query));
	arr.push("result=xml");
	if (this._options.prefix) { arr.push("prefix=1"); }
	if (this._options.highlight) { arr.push("highlight=1"); }
	if (this._options.count) { arr.push("count="+this._options.count); }

	url += "?"+arr.join("&");
	return url;
};

JAK.Suggest.prototype._highlight = function(item) {
	this._activeItem = item;
	for (var i=0;i<this._navigableItems.length;i++) {
		var it = this._navigableItems[i];
		var node = it.getContainer();
		if (it == item) {
			JAK.DOM.addClass(node, "active");
		} else {
			JAK.DOM.removeClass(node, "active");
		}
	}
};

/**
 * Handler pro keydown na hledacím inputu
 */
 JAK.Suggest.prototype._keydown = function(e, elm) {
	var code = e.keyCode;
	this.makeEvent("suggest-keyboard", {code: code});

	switch (code) {
		case 13: /* enter */
			JAK.Events.cancelDef(e);
			if (this._activeItem) {
				this._activeItem.action(e);
			} else {
				this.action(e);
			}
		break;

		case 40: /* sipka dolu */
			if (this._navigableItems.length) { this._stepDown(); this.stepUpDown = true; }
		break;

		case 38: /* sipka nahoru */
			JAK.Events.cancelDef(e); /* aby se kurzor nepřesunul */
			if (this._navigableItems.length) { this._stepUp(); this.stepUpDown = true; }
		break;

		case 9: /* tab */
		case 27: /* esc */
			this._hide();
		break;

		default:
			/* nevime co ty kody znaci */
			if (((code < 33) || (code > 39)) && [27,44,45,16,17,18,19,20].indexOf(code) == -1) {
				this._startRequest();
			}
		break;
	}
	this.stepUpDown = false;
};

/**
 * Handler pro focus na hledacím inputu
 */
JAK.Suggest.prototype._focus = function(e, elm) {
	if (this._items.length) {
		this._show();
	} else {
		this._valueCheck(e, elm); /* focus na již vyplněný input zavolá request na suggest */
	}
};

JAK.Suggest.prototype._stepUp = function() {
	var index = this._navigableItems.indexOf(this._activeItem);
	var cnt = this._navigableItems.length;
	if (index == 0) {
		this._highlight();
		this._setCaretToEnd();
		this._dom.input.value = this.getQuery();
		return;
	} else if (index == -1) {
		index = cnt - 1
	} else {
		index = (index == -1 || index == 0 ? 0 : index-1);
	}
	this._highlight(this._navigableItems[index]);
	this._activeItem.highlightNavigateAction();
};

JAK.Suggest.prototype._stepDown = function() {
	var index = this._navigableItems.indexOf(this._activeItem);
	var cnt = this._navigableItems.length;
	if (index == cnt - 1) {
		this._highlight();
		this._setCaretToEnd();
		this._dom.input.value = this.getQuery();
		return;
	} else if (index == -1) {
		index = 0;
	} else {
		index++;
	}
	this._highlight(this._navigableItems[index]); 
	this._activeItem.highlightNavigateAction();
};

JAK.Suggest.prototype._setCaretToEnd = function() {
	var input = this._dom.input;
	var chars = input.value.length;
	if (input.setSelectionRange) {
		input.focus();
		input.setSelectionRange(chars, chars);
	} else if (input.createTextRange) {
		var range = input.createTextRange();
		range.collapse(true);
		range.moveEnd('character',chars);
		range.moveStart('character',chars);
		range.select();
	}
};

/**
 * Uzivatel hnul mysi, cili je povolen hover polozek (po prvnim zobrazeni byl zamceny, viz _hoverLock) */
JAK.Suggest.prototype._unlock = function() {
	this._hoverLock = false;
};

/**
 * Blur inputu. Pokud mame suggest, tak nic nedelame; pokud nemame a bezi request, zrusime ho.
 */
JAK.Suggest.prototype._blur = function(e, elm) {
	if (JAK.Events.getTarget(e) == this._dom.input) { return; } /* klikneme-li do hledacího pole, nebudeme provádět blur */
	this._hide();
	if (this._timeout) {
		clearTimeout(this._timeout);
		this._timeout = false;
	}
	if (this._rq) {
		this._rq.abort();
		this._rq = null;
	}
};

/* --- --- */

/**
 * @class Tupa naseptana polozka
 */
JAK.Suggest.Item = JAK.ClassMaker.makeClass({
	NAME: "JAK.Suggest.Item",
	VERSION: "2.0",
	IMPLEMENT: JAK.ISignals
});

JAK.Suggest.Item.prototype.$constructor = function(owner, node) {
	this._owner = owner;
	this._node = node;

	this._dom = {
		container: JAK.mel("p", {className:"item"})
	};
	this._ec = [];
	this._queryUrl = null;

	this._value = node.getAttribute("value");

	this._build();
	this._addEvents();
};

JAK.Suggest.Item.prototype.isNavigable = function() { return true; };

JAK.Suggest.Item.prototype.$destructor = function() {
	JAK.Events.removeListeners(this._ec);
};

JAK.Suggest.Item.prototype.getContainer = function() {
	return this._dom.container;
};

/**
 * Hodnota teto polozky
 * @returns {string}
 */
JAK.Suggest.Item.prototype.getValue = function() {
	return this._value;
};

/**
 * XML uzel patrici teto polozce
 * @returns {node}
 */
JAK.Suggest.Item.prototype.getNode = function() {
	return this._node;
};

JAK.Suggest.Item.prototype._build = function() {
	this._dom.container.innerHTML = "<span>" + this._dom.container.innerHTML + "</span>";
};

JAK.Suggest.Item.prototype.action = function() {
	if (this._queryUrl) { window.location = this._queryUrl; }
};

JAK.Suggest.Item.prototype._addEvents = function() {
	if ("ontouchend" in window) {
		this._ec.push(JAK.Events.addListener(this._dom.container, "touchstart", this, "_touchstart"));
		this._ec.push(JAK.Events.addListener(this._dom.container, "touchmove", this, "_touchmove"));
		this._ec.push(JAK.Events.addListener(this._dom.container, "touchend", this, "_touchend"));
	}
	this._ec.push(JAK.Events.addListener(this._dom.container, "click", this, "_click"));
	this._ec.push(JAK.Events.addListener(this._dom.container, "mousemove", this, "_move"));
	this._ec.push(JAK.Events.addListener(this._dom.container, "mouseover", this, "_over"));
	this._ec.push(JAK.Events.addListener(this._dom.container, "mouseout", this, "_out"));
}

JAK.Suggest.Item.prototype._touchstart = function(e, elm) {
	this._touched = true;
};

JAK.Suggest.Item.prototype._touchmove = function(e, elm) {
	this._touched = false;
};

JAK.Suggest.Item.prototype._touchend = function(e, elm) {
	if (this._touched) {
		this._owner.makeEvent("suggest-touch", {action: "click"});
		this._owner._highlight(this);
		this.action(e);
	}
};

JAK.Suggest.Item.prototype._click = function(e, elm) {
	if (this._touched) {
		this._touched = false;
		return;
	}

	this._owner.makeEvent("suggest-mouse", {action: "click"});
	this.action(e);
};

JAK.Suggest.Item.prototype._over = function(e, elm) {
	if (this._owner._hoverLock) { return; }
	this._owner._highlight(this);
};

JAK.Suggest.Item.prototype._move = function(e, elm) {
	if (e.currentTarget.classList.contains("active")) { return; }
	this._over(e, elm);
};

JAK.Suggest.Item.prototype._out = function(e, elm) {
	this._owner._highlight();
};

/**
 * Highlight klavesnici
 */
JAK.Suggest.Item.prototype.highlightNavigateAction = function() {
	this._owner.getInput().value = this._owner.getQuery();
};

/* --- --- */

/**
 * @class Interaktivni naseptany termin
 * @augments JAK.Suggest.Item
 */
JAK.Suggest.Term = JAK.ClassMaker.makeClass({
	NAME: "JAK.Suggest.Term",
	VERSION: "2.0",
	EXTEND: JAK.Suggest.Item
});

JAK.Suggest.Term.prototype.action = function(e) {
	this._owner.getInput().value = this._value;
	this._owner.action(e);
};

JAK.Suggest.Term.prototype.highlightNavigateAction = function() {
	this._owner.getInput().value = this.getValue();
};

JAK.Suggest.Term.prototype._build = function() {
	this._dom.container.innerHTML = this._highlight(this._value);

	var span = this._buildRelevance();
	this._dom.container.insertBefore(span, this._dom.container.firstChild);
	this._dom.container.innerHTML = "<span>" + this._dom.container.innerHTML + "</span>";
};

JAK.Suggest.Term.prototype._highlight = function(what) {
	var value = what;
	var start = this._node.getAttribute("highlightStart");
	var end = this._node.getAttribute("highlightEnd");

	if (start && end) {
		start = parseInt(start);
		end = parseInt(end);
		value = value.substring(0, start) + "<strong>" + value.substring(start, end) + "</strong>" + value.substring(end);
	}

	return value;
};

JAK.Suggest.Term.prototype._buildRelevance = function() {
	var span = JAK.mel("span", {className:"relevance"});
	span.innerHTML = this._node.getAttribute("relevance");
	return span;
};

/**
 * @class Slovnikova polozka
 * @augments JAK.Suggest.Item
 */
JAK.Suggest.Dictionary = JAK.ClassMaker.makeClass({
	NAME:'JAK.Suggest.Dictionary',
	VERSION: "1.1",
	EXTEND: JAK.Suggest.Item
});

/**
 * @constant
 * Url adresa k adresáři vlaječek, které musí být ve formátu ico-flag-<lang>.png
 */
JAK.Suggest.Dictionary.FLAGS_URL = "http://slovnik.seznam.cz/img/flags/";

/**
 * @constant
 * Url adresa ke slovníku
 */
JAK.Suggest.Dictionary.URL = "http://slovnik.seznam.cz/";

JAK.Suggest.Dictionary.prototype._build = function() {
	var translation = this._node.getElementsByTagName("string")[0].firstChild.nodeValue,
		flagUrl = JAK.Suggest.Dictionary.FLAGS_URL + "ico-flag-" + translation.split("_")[0] + ".png",
		queryUrl = JAK.Suggest.Dictionary.URL + translation.replace("_", "-") + "/word/?q="+this._owner.getQuery()+"&thru=sug&type="+translation,
		a = JAK.mel("a", {href: queryUrl});

	this._queryUrl = queryUrl;
	
	a.innerHTML = "<img src='"+ flagUrl +"' /><strong>" + this._owner.getQuery() + "</strong> – " + this._value + " …";

	JAK.DOM.addClass(this._dom.container, "dictionary miniapps");
	this._dom.container.appendChild(a);
};

/**
 * @class Položka miniappky
 * @augments JAK.Suggest.Item
 */
JAK.Suggest.Miniapps = JAK.ClassMaker.makeClass({
	NAME:'JAK.Suggest.Miniapps',
	VERSION: "1.0",
	EXTEND: JAK.Suggest.Item
});

/**
 * @constant
 * Url k jednotlivým nápovědám miniaplikací
 */
JAK.Suggest.Miniapps.APPS = {
	converter: "http://napoveda.seznam.cz/cz/hledani-fulltext-miniaplikace.html?thru=sug#prevod_jednotek",
	calculator: "http://napoveda.seznam.cz/cz/hledani-fulltext-miniaplikace.html?thru=sug#kalkulacka",
	exchange: "http://napoveda.seznam.cz/cz/hledani-fulltext-miniaplikace.html?thru=sug#prevod_men"
};

JAK.Suggest.Miniapps.prototype._build = function() {
	var type = this._node.getElementsByTagName("string");
	if (!type.length) { return; }

	type = type[0].textContent || type[0].firstChild.nodeValue; /* IE9+ */
	this._queryUrl = JAK.Suggest.Miniapps.APPS[type];
	if (!this._queryUrl) { return; }

	var span = JAK.mel("span", {innerHTML: this._value});
	JAK.DOM.addClass(this._dom.container, "help miniapps");
	this._dom.container.title = "Dozvědět se více o této funkci?";
	this._dom.container.appendChild(span);
};

Suggest = JAK.ClassMaker.makeSingleton({
	NAME: "Suggest",
	VERSION: "1.0",
	EXTEND: JAK.Suggest
});

Suggest.prototype.$constructor = function() {
	var suggestOptions = {
		dict: "mapy",
		highlight: true,
		count: 5,
		parentElement: null,
		autoSubmit: false,
		format: JAK.Request.TEXT,
	};

	this._ecInput = [];
	this.$super(null, "https://mapy.cz/suggest", suggestOptions);
};

Suggest.prototype.$destructor = function() {
	this.$super();
	if (this._dom.container.parentNode) {
		this._dom.container.parentNode.removeChild(this._dom.container);
	}
};

Suggest.prototype.action = function(e) {
	this._dom.input.blur();
	this.$super();

	if (e.keyCode == 13) {
		this._show();
		this._dom.input.focus();
	}
};

Suggest.prototype.setInput = function(node) {
	this._options.parentElement = node.parentNode;
	if (this._dom.container) {
		this._dom.container.parentNode.removeChild(this._dom.container);
	}
	this._dom.container = null;
	if(!this._dom.container) {
		this._build(node);
		this._items = [];
		this._query = null;
	}

	var input = node;
	this._dom.container.style.width = input.offsetWidth + "px";
	this._dom.input = input;

	JAK.Events.removeListeners(this._ecInput);
	this._ecInput.push(JAK.Events.addListener(input, "keydown", this, "_keydown"));
	this._ecInput.push(JAK.Events.addListener(input, "focus", this, "_focus"));
	this._ecInput.push(JAK.Events.addListener(input, "blur", this, "_blur")); /* schovat */
};

Suggest.prototype._updateWidth = function() {
	this.$super();
	var pos = JAK.DOM.getPosition(this._dom.input, this._dom.input.form);
	var bottom  = parseInt(JAK.DOM.getStyle(this._dom.input, "borderBottomLeftRadius")) || 0;
	this._dom.container.style.left = pos.left + "px";
	this._dom.container.style.top = (pos.top + this._dom.input.offsetHeight - bottom) + "px";
}

Suggest.prototype._build = function(node) {
	if(!node) { return; }
	var input = node;

	var container = JAK.mel("div", {className:"suggest"}, {display:"none"});
	this._options.parentElement.appendChild(container);

	var content = JAK.mel("div", {className:"content"});
	container.appendChild(content);

	this._dom.input = input;
	this._dom.container = container;
	this._dom.content = content;

	this._options.parentElement.appendChild(container);

	this._ec.push(JAK.Events.addListener(container, "mousedown", JAK.Events.stopEvent)); /* aby nedobublal az nahoru, kde to zavre suggest */
	this._ec.push(JAK.Events.addListener(container, "mousedown", JAK.Events.cancelDef)); /* aby nenastal blur na inputu */
	this._ec.push(JAK.Events.addListener(document, "mousemove", this, "_unlock")); /* viz _hoverLock; povoli hoverovani polozek */
	this._ec.push(JAK.Events.addListener(document, "mousedown", this, "_blur")); /* schovat */
};

Suggest.prototype._buildUrl = function(query) {
	var url = this._url;
	if (url.charAt(url.length-1) != "/") { url += "/"; }
	url += this._options.dict;
	var arr = ["count=" + this._options.count];
	this._setMapParams(arr);
	arr.push("phrase="+encodeURIComponent("Česká republika " + query));
	url += "?"+arr.join("&");
	return url;
}

Suggest.prototype._setMapParams = function(arr) {
	var coords = this.map.map.getCenter().toWGS84();

	var lon = coords[0];
	var lat = coords[1];
	arr.push("lon=" + lon);
	arr.push("lat=" + lat);
	var zoom = this.map.map.getZoom();
	arr.push("zoom=" + zoom)
	return arr;
}

Suggest.prototype._buildItems = function(data) {
	var data = JSON.parse(data);
	
	for (var i=0;i<data.result.length;i++) {
		var item = data.result[i];
		this._buildItem(Suggest.Term, item);
	}

	for (var i=0;i<this._items.length;i++) {
		this._dom.content.appendChild(this._items[i].getContainer());
	}
}

Suggest.prototype._focus = function() {}/* produkt si nepreje pri focusu zobrazovat */

/**
 * @class Polozka naseptavace s source a id
 * @augments JAK.Suggest.Term
 */
Suggest.Term = JAK.ClassMaker.makeClass({
	NAME: "Suggest.Term",
	VERSION: "1.0",
	EXTEND: JAK.Suggest.Term
});

Suggest.Term.prototype.$constructor = function(owner, data) {
	data.getAttribute = function() {};
	this.$super(owner, data);
	this._value = this._node.userData.suggestFirstRow;
	this._startTouchElm = null;
}

Suggest.Term.prototype._build = function() {
	var data = this._node.userData;
	var span = JAK.mel("span", {className:"image"});
	this._dom.container.appendChild(span);

	var span = JAK.mel("span", {className:"text"});
	this._dom.container.appendChild(span);

	span.appendChild(JAK.mel("strong", {innerHTML:data.suggestFirstRow}));
	span.appendChild(JAK.mel("br"));
	span.appendChild(JAK.mel("em", {innerHTML:data.suggestSecondRow}));
	if (data.suggestThirdRow) {
		span.appendChild(JAK.mel("br"));
		span.appendChild(JAK.mel("em", {innerHTML:data.suggestThirdRow}));
	}
	span.appendChild(JAK.mel("span"));
};

Suggest.Term.prototype.getData = function() {
	return this._node.userData;
}

/* nasledujici upravy jsou kvuli skrolovani stranky na mobilnim zarizeni, aby byl videt cely suggest */
Suggest.Term.prototype._touchstart = function(e, elm) {
	/* odebrani zastaveni eventy */
	this._owner.makeEvent("suggest-touch", {action: "start"});
	this._touchEvent = {
		clientX: e.touches[0].clientX,
		clientWidth: Math.round(this._dom.container.offsetWidth * 0.1),
		clientClicked: true,
		timestampStart: e.timeStamp /* pridani timestampu startu udalosti */
	};
	this._owner._highlight(this);
}

Suggest.Term.prototype._touchmove = function(e, elm) {
	this._touchEvent.clientXEnd = e.touches[0].clientX; /* pozice pohybu prstu pri touchmove */

	this.$super(e,elm);
}

Suggest.Term.prototype._touchend = function(e, elm) {
	this._owner.makeEvent("suggest-touch", {action: "end"});

	/* pokud je pozice po posunu jina nez o 5px predpokladame ze se jedna o scroll */
	if ((Math.abs(this._touchEvent.clientX - this._touchEvent.clientXEnd) < 5 || !this._touchEvent.clientXEnd)) {
		/* pokud ale bylo podrzeno dle, pocitame s tim ze uzivatel chtel vyvolat kontextovou nabidku.
		   Nebo se vratil po skrolovani na stejnou pozici. Davam limit 150ms. */
		if ((e.timeStamp - this._touchEvent.timestampStart) < 150) {
			if (this._touchEvent.clientClicked) {
				this._owner.makeEvent("suggest-touch", {action: "click"});
				this.action();
			} else {
				this._owner.makeEvent("suggest-touch", {action: "swipe"});
				this._owner._startRequest();
			}
		}
	}
}

/**
 * udalost pri vyberu polozky z naseptavace
 */
Suggest.prototype._suggestSubmit = function(e) {
    if (this._dom.button != e.target && this._dom.input != e.target.getInput()) { return; }
    JAK.Events.cancelDef(e);
    var item = null;
    if (this._dom.button != e.target) { 
        item = e.target.getActive(); 
    }
    if (!item) { return; }
    var data = item.getData();

    var zooms = {
        "ward": 13,
        "quar": 13,
        "muni": 12,
        "dist": 9,
        "area": 8,
        "regi": 8,
    };
    var zoom = zooms[data.source] || this.map.config.autocompleteDefaultZoom;
    this.map.closeInfoBox();

    this.map.map.setCenterZoom(SMap.Coords.fromWGS84(data.longitude, data.latitude), zoom, true);
}
