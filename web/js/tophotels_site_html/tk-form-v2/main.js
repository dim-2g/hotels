"use strict";
var __tkv_config = {        defaultCity: 1000,        defaultCountry: 1,        defaultDeparture: '2018-10-20',        defaultNightFrom: 7,        defaultNightTo: 14    };
if (typeof mytour === "undefined") {
    var mytour = {};
}

if (typeof mytour.searchTours === "undefined") {
    mytour.searchTours = {};
}

/**
 *
 */
mytour.searchTours.main = new (function() {
    var self = this;

    /**
     *
     * @param v
     * @returns {Array}
     */
    var tkIntArray = function(v) {
        if (typeof v === 'object' && v !== null) {
            return v;
        }

        if (typeof v !== 'string' || v === '') {
            return [];
        }

        var result = [];
        var arr = v.split('_');
        for (var i in arr) {
            if (arr.hasOwnProperty(i)) {
                result.push(parseInt(arr[i]));
            }
        }

        return result;
    };

    var reqTypeCast = {
        ct: parseInt,
        co: parseInt,
        ad: parseInt,
        ch: parseInt,
        nf: parseInt,
        nt: parseInt,
        pf: parseInt,
        pt: parseInt,
        cur: parseInt,
        ti: parseInt,
        op: tkIntArray,
        al: tkIntArray,
        ac: tkIntArray,
        re: tkIntArray,
        rpl: tkIntArray,
        me: tkIntArray,
        alt: parseInt,
        alpt: parseInt,
        alpv: tkIntArray
    };

    // РєР°Р»Р±РµРєРё
    var callbacks = {
        onFilterChange: function (data) {},
        onSearchStart: function (data) {},
        onSearchResult: function (data) {},
        onSearchEnd: function (data) {},
        onSearchStatus: function (data) {},
        onSearchError: function (data) {}
    };

    this.request = {
        directions: [],
        ct: 1000,
        co: 0,
        op: [],
        ad: 2,
        ch: 0,
        df: (new Date()).addDays((__tkv_config.defaultDeparture > 0) ? __tkv_config.defaultDeparture : 14).format('Y-m-d'),
        dt: (new Date()).addDays((__tkv_config.defaultDeparture > 0) ? __tkv_config.defaultDeparture : 14).format('Y-m-d'),
        nf: __tkv_config.defaultNightFrom,
        nt: __tkv_config.defaultNightTo,
        re: [],
        rpl: [],
        al: [],
        ac: [],
        alt: null,
        alpt: null,
        alpv: null,
        alr: 0,
        rt: null,
        rv: null,
        me: [],
        pf: 0,
        pt: 500000,
        ch1: null,
        ch2: null,
        ch3: null,
        cur: 3,
        ti: -1,
        searchType: null
    };

    //
    this.load = function (params) {
        var req = self.request;

        if (typeof params === 'object') {
            for (var p in params) {
                if (params.hasOwnProperty(p)) {
                    if (params[p] !== '') {
                        req[p] = params[p];
                    }

                    if (reqTypeCast.hasOwnProperty(p)) {
                        req[p] = reqTypeCast[p](params[p]);
                    }
                }
            }
        }


        console.warn(req);

        // РёСЃРїСЂР°РІР»РµРЅРёРµ СѓСЃС‚Р°СЂРµРІС€РёС… РґР°С‚
        var now = Date.createFromIsoDate(Date.todayISO());
        var df = Date.createFromIsoDate(req.df);
        var dt = Date.createFromIsoDate(req.dt);
        var diff = dt.dayDiff(df);

        if (df.before(now)) {
            req.df = now.format('Y-m-d');
            req.dt = (new Date(now)).addDays(diff).format('Y-m-d');
        }
    };

    /**
     *
     */
    this.go = function() {
        var p = '';
        var rk, rp;
        for (rk in self.request) {
            if (!self.request.hasOwnProperty(rk)) {
                continue;
            }

            rp = self.request[rk];

            if (!self.request.hasOwnProperty(rk) || rp === null || rp === '' || rp === 'null') {
                continue;
            }

            switch (typeof rp) {
                default:
                    p += '&' + encodeURIComponent(rk) + '=' + encodeURIComponent(rp);
                    break;
                case 'object':
                    p += '&' + encodeURIComponent(rk) + '=' + encodeURIComponent(rp.join('_'));
                    break;
            }
        }

        document.location.href = '//' + document.location.hostname + '/tour-search?s' + p;
    };

    /**
     *
     */
    this.updateURL = function() {
        var p = '';
        var rk, rp;
        for (rk in self.request) {
            rp = self.request[rk];

            if (!self.request.hasOwnProperty(rk) || (rp + '' === 'NaN') || (rp + '' === 'undefined') || rp === null || rp === '' || rp === 'null') {
                continue;
            }

            switch (typeof rp) {
                default:
                    p += '&' + encodeURIComponent(rk) + '=' + encodeURIComponent(rp);
                    break;
                case 'object':
                    p += '&' + encodeURIComponent(rk) + '=' + encodeURIComponent(rp.join('_'));
                    break;
            }
        }

        window.history.pushState('', document.title, '//' + document.location.hostname + '/tour-search?s' + p + location.hash);
    };

    /**
     * Р РµРіРёСЃС‚СЂРёСЂСѓРµС‚ РєР°Р»Р±РµРє
     * @param name
     * @param callback
     */
    this.registerCallback = function(name, callback) {
        callbacks[name] = callback;
    };

    /**
     * Р’С‹Р·С‹РІР°РµС‚ СѓРєР°Р·Р°РЅРЅС‹Р№ РєР°Р»Р±РµРє
     * РџСЂРёРЅРёРјР°РµС‚ РїР°СЂР°РјРµС‚СЂ data
     * @param callback
     * @param data
     */
    this.callbackCall = function(callback, data) {
        if (typeof callbacks[callback] === 'function') {
            callbacks[callback](data);
        }
    };
})();