# How to update and use
Run the NPM command:
`npm run build`

This will generate new files in the ./build/ folder.

## Edit files
The project includes an external CSS framework. We dont want these styles to infect the entire site.

Open x.xxxxxxxx.chunk.css inside /build/static/css/

Change body {} to #root {}

Change a {} to #root {}

This needs to be done after each build.

## Include in project

Everytime we do a build a new set of files is generated in ./build/static/** folder.

The filenames change due to caching strategies.

After a build, the file paths change and we should update these everywhere they are called.

To easily get the new file paths, go to ./build/index.html and copy the body (including div#root) and CSS.

Ex. from the <head>

```html
<link href="/sites/skoven-i-skolen.dk/themes/custom/sis/map/build/static/css/main.e5a2339c.chunk.css"
      rel="stylesheet">
```

Ex. from the <body>

```html
<div id="root"></div>
<script>!function (e) {
  function t(t) {
    for (var n, i, l = t[0], f = t[1], a = t[2], c = 0, s = []; c < l.length; c++) i = l[c], Object.prototype.hasOwnProperty.call(o, i) && o[i] && s.push(o[i][0]), o[i] = 0;
    for (n in f) Object.prototype.hasOwnProperty.call(f, n) && (e[n] = f[n]);
    for (p && p(t); s.length;) s.shift()();
    return u.push.apply(u, a || []), r()
  }

  function r() {
    for (var e, t = 0; t < u.length; t++) {
      for (var r = u[t], n = !0, l = 1; l < r.length; l++) {
        var f = r[l];
        0 !== o[f] && (n = !1)
      }
      n && (u.splice(t--, 1), e = i(i.s = r[0]))
    }
    return e
  }

  var n = {}, o = {1: 0}, u = [];

  function i(t) {
    if (n[t]) return n[t].exports;
    var r = n[t] = {i: t, l: !1, exports: {}};
    return e[t].call(r.exports, r, r.exports, i), r.l = !0, r.exports
  }

  i.m = e, i.c = n, i.d = function (e, t, r) {
    i.o(e, t) || Object.defineProperty(e, t, {enumerable: !0, get: r})
  }, i.r = function (e) {
    "undefined" != typeof Symbol && Symbol.toStringTag && Object.defineProperty(e, Symbol.toStringTag, {value: "Module"}), Object.defineProperty(e, "__esModule", {value: !0})
  }, i.t = function (e, t) {
    if (1 & t && (e = i(e)), 8 & t) return e;
    if (4 & t && "object" == typeof e && e && e.__esModule) return e;
    var r = Object.create(null);
    if (i.r(r), Object.defineProperty(r, "default", {
      enumerable: !0,
      value: e
    }), 2 & t && "string" != typeof e) for (var n in e) i.d(r, n, function (t) {
      return e[t]
    }.bind(null, n));
    return r
  }, i.n = function (e) {
    var t = e && e.__esModule ? function () {
      return e.default
    } : function () {
      return e
    };
    return i.d(t, "a", t), t
  }, i.o = function (e, t) {
    return Object.prototype.hasOwnProperty.call(e, t)
  }, i.p = "/sites/skoven-i-skolen.dk/themes/custom/sis/map/build/";
  var l = this.webpackJsonpundefined = this.webpackJsonpundefined || [], f = l.push.bind(l);
  l.push = t, l = l.slice();
  for (var a = 0; a < l.length; a++) t(l[a]);
  var p = f;
  r()
}([])</script>
<script src="/sites/skoven-i-skolen.dk/themes/custom/sis/map/build/static/js/2.f0b5b4de.chunk.js"></script>
<script src="/sites/skoven-i-skolen.dk/themes/custom/sis/map/build/static/js/main.ab2cd1e8.chunk.js"></script>
```
