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
