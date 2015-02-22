(function () {
	YMaps.jQuery(function () {
		var h = new YMaps.Map(YMaps.jQuery("#YMapsID")[0]), f;
		if (YMaps.location) {
			f = new YMaps.GeoPoint(YMaps.location.longitude, YMaps.location.latitude)
		} else {
			f = new YMaps.GeoPoint(37.64, 55.76)
		}
		h.setCenter(f, 10);
		var o = new YMaps.ToolBar(), m = new c(), j = new b();
		o.add(m);
		h.addControl(o);
		h.addControl(new YMaps.Zoom());
		h.addControl(new YMaps.TypeControl([YMaps.MapType.MAP, YMaps.MapType.SATELLITE, YMaps.MapType.HYBRID, (YMaps.MapType.PMAP.getName = function () {
			return"Народная"
		}, YMaps.MapType.PMAP)], [0, 1, 2, 3]));
		h.addControl(j);
		h.enableScrollZoom();
		var k = YMaps.jQuery("#getlonglat-map-center"), e = YMaps.jQuery("#getlonglat-map-zoom"), l = YMaps.jQuery("#getlonglat-point");
		var n = new YMaps.Placemark(h.getCenter(), {style: {iconStyle: {href: "/i/maps/icons/center.gif", size: new YMaps.Point(16, 16), offset: new YMaps.Point(-8, -8)}}, interactive: YMaps.Interactivity.NONE}), g = new YMaps.SearchControl({noPlacemark: true}), i = new YMaps.Placemark(f, {style: "default#houseIcon", draggable: true, hasBalloon: false, hasHint: true});
		i.name = "Перетащите метку";
		h.addOverlay(n);
		h.addOverlay(i);
		YMaps.Events.observe(h, [h.Events.Move, h.Events.BoundsChange], function () {
			n.setGeoPoint(h.getCenter());
			k.attr("value", h.getCenter());
			e.attr("value", h.getZoom())
		});
		k.attr("value", h.getCenter());
		e.attr("value", h.getZoom());
		l.attr("value", i.getGeoPoint());
		YMaps.Events.observe(g, g.Events.Select, function (q, p) {
			i.setGeoPoint(p.getGeoPoint())
		});
		h.addControl(g, new YMaps.ControlPosition(YMaps.ControlPosition.TOP_LEFT, new YMaps.Point(180, 5)));
		YMaps.Events.observe(i, [i.Events.Drag, i.Events.PositionChange], function (p) {
			l.attr("value", p.getGeoPoint())
		});
		YMaps.Events.observe(m, "GeolocationLoad", function (p) {
			i.setGeoPoint(p)
		})
	});
	function d(g, e) {
		var f = function () {
		};
		f.prototype = e.prototype;
		f.prototype.constructor = e;
		return g.prototype = new f()
	}

	function c(f, e) {
		this.content = YMaps.jQuery.extend({}, this.defaultContent, f);
		this.options = YMaps.jQuery.extend({}, this.defaultOptions, e);
		YMaps.ToolBarButton.call(this, this.content.normal)
	}

	var a = d(c, YMaps.ToolBarButton);
	a.defaultContent = {normal: {icon: "/i/maps/icons/wifi_icon.png", width: 20, hint: "Определение координат с помощью Geolocation API"}, active: {icon: "/i/maps/icons/loader.gif", width: 20, hint: "Идет обработка запроса..."}};
	a.defaultOptions = {notCentering: false, noPlacemark: true, enableHighAccuracy: false, timeout: 10000, maximumAge: 60000, style: {iconStyle: {href: "/i/maps/icons/man.png", size: new YMaps.Point(27, 26), offset: new YMaps.Point(-10, -24)}}};
	a.Errors = ["permission denied", "position unavailable", "timeout"];
	a.onAddToToolBar = function (e, g, f) {
		YMaps.ToolBarButton.prototype.onAddToToolBar.call(this, e, g, f);
		this.map = e.getMap();
		if (navigator.geolocation) {
			this.geolocation = navigator.geolocation;
			this.listener = YMaps.Events.observe(this, this.Events.Click, this.getPosition, this)
		} else {
			this.hide()
		}
	};
	a.onRemoveFromToolBar = function () {
		YMaps.ToolBarButton.prototype.onRemoveFromToolBar.call(this);
		this.listener.cleanup();
		if (this.placemark) {
			this.map.removeOverlay(this.placemark());
			this.placemark = null
		}
		this.listener = this.geolocation = this.content = this.options = this.map = null
	};
	a.getPosition = function () {
		var e = this;
		this.setContent(this.content.active);
		this.geolocation.getCurrentPosition(function (g) {
			var h = g.coords, f = new YMaps.GeoPoint(h.longitude, h.latitude);
			e.setContent(e.content.normal);
			if (!e.options.notCentering) {
				e.map.setCenter(f, 15)
			}
			if (!e.options.noPlacemark) {
				if (!e.placemark) {
					e.placemark = new YMaps.Placemark(f, {style: e.options.style});
					e.map.addOverlay(e.placemark)
				} else {
					e.placemark.setGeoPoint(f)
				}
				e.placemark.setBalloonContent(f.toString())
			}
			YMaps.Events.notify(e, "GeolocationLoad", f)
		}, function (f) {
			e.setContent(e.content.normal);
			alert("Во время выполнения произошла ошибка: " + e.Errors[f.code - 1])
		}, this.options)
	};
	function b() {
		var e = YMaps.jQuery(['<div class="b-getlonglat-view-information">', '<div class="b-getlonglat-element">Координаты метки:<input id="getlonglat-point" type="text" readonly="readonly"/></div>', '<div class="b-getlonglat-element">Масштаб карты:<input id="getlonglat-map-zoom" type="text" readonly="readonly"/></div>', '<div class="b-getlonglat-element">Центр карты:<input id="getlonglat-map-center" type="text" readonly="readonly"/></div>', "</div>"].join(""));
		e.css("z-index", YMaps.ZIndex.CONTROL - 3);
		e.find(".b-getlonglat-element input").bind("click", function () {
			this.select()
		});
		this.onAddToMap = function (g, h) {
			var f = h || new YMaps.ControlPosition(YMaps.ControlPosition.TOP_RIGHT, new YMaps.Size(5, 35));
			f.apply(e);
			e.appendTo(g.getContainer())
		};
		this.onRemoveFromMap = function () {
			e.remove()
		}
	}
})();