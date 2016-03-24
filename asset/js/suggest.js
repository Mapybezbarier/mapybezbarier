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
	this.$super(null, "http://mapy.cz/suggest", suggestOptions);
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
	//arr.push("category=address_cz,area_cz,country_cz,district_cz,municipality_cz,quarter_cz,region_cz,street_cz,ward_cz");
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
