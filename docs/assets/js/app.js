/* ZeroPing Documentation — client application */
(function () {
  "use strict";

  var CATEGORY_ORDER = [
    "Getting Started",
    "The Basics",
    "Security & HTTP",
    "Digging Deeper",
    "Project & Community",
  ];

  var PAGE_ORDER = [
    "introduction",
    "installation",
    "quick-start",
    "project-structure",
    "routing",
    "controllers",
    "views",
    "layouts",
    "components",
    "models",
    "orm",
    "database",
    "migrations",
    "seeders",
    "middleware",
    "authentication",
    "authorization",
    "validation",
    "file-upload",
    "helpers",
    "cli-commands",
    "configuration",
    "environment-variables",
    "testing",
    "deployment",
    "release-notes",
    "examples",
  ];

  var PAGES = [];
  (function loadTemplates() {
    var nodes = document.querySelectorAll("#page-templates template");
    nodes.forEach(function (node) {
      PAGES.push({
        id: node.getAttribute("data-id"),
        title: node.getAttribute("data-title"),
        category: node.getAttribute("data-category"),
        html: node.innerHTML,
      });
    });
  })();
  var byId = {};
  PAGES.forEach(function (p) { byId[p.id] = p; });

  // Sort pages by PAGE_ORDER, unknown ids appended.
  PAGES.sort(function (a, b) {
    var ia = PAGE_ORDER.indexOf(a.id);
    var ib = PAGE_ORDER.indexOf(b.id);
    if (ia === -1) ia = 999;
    if (ib === -1) ib = 999;
    return ia - ib;
  });

  var HLJS_LIGHT = "https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github.min.css";
  var HLJS_DARK = "https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css";

  /* ---------------- Theme ---------------- */
  function setTheme(theme) {
    document.documentElement.setAttribute("data-theme", theme);
    var link = document.getElementById("hljs-theme");
    if (link) link.href = theme === "dark" ? HLJS_DARK : HLJS_LIGHT;
    try { localStorage.setItem("zp-theme", theme); } catch (e) {}
  }
  function initTheme() {
    var saved;
    try { saved = localStorage.getItem("zp-theme"); } catch (e) {}
    if (!saved) {
      saved = window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
    }
    setTheme(saved);
  }
  document.getElementById("theme-toggle").addEventListener("click", function () {
    var cur = document.documentElement.getAttribute("data-theme");
    setTheme(cur === "dark" ? "light" : "dark");
  });

  /* ---------------- Sidebar ---------------- */
  function buildSidebar() {
    var nav = document.getElementById("sidebar-nav");
    nav.innerHTML = "";
    CATEGORY_ORDER.forEach(function (cat) {
      var groupPages = PAGES.filter(function (p) { return p.category === cat; });
      if (!groupPages.length) return;
      var group = document.createElement("div");
      group.className = "nav-group";
      var title = document.createElement("div");
      title.className = "nav-group-title";
      title.textContent = cat;
      group.appendChild(title);
      groupPages.forEach(function (p) {
        var a = document.createElement("a");
        a.className = "nav-link";
        a.href = "#/" + p.id;
        a.dataset.id = p.id;
        a.textContent = p.title;
        group.appendChild(a);
      });
      nav.appendChild(group);
    });
  }

  function highlightNav(id) {
    var links = document.querySelectorAll(".nav-link");
    links.forEach(function (l) {
      l.classList.toggle("active", l.dataset.id === id);
    });
  }

  /* ---------------- Rendering ---------------- */
  function currentId() {
    var h = (location.hash || "").replace(/^#\/?/, "");
    return byId[h] ? h : "introduction";
  }

  function ensureHeadingIds(root) {
    var heads = root.querySelectorAll("h2, h3");
    heads.forEach(function (h) {
      if (!h.id) {
        h.id = h.textContent.trim().toLowerCase().replace(/[^a-z0-9]+/g, "-").replace(/^-+|-+$/g, "");
      }
      var a = document.createElement("a");
      a.className = "anchor";
      a.href = "#/" + currentId() + "#" + h.id;
      a.textContent = "#";
      h.appendChild(a);
    });
  }

  function buildToc(root) {
    var toc = document.getElementById("toc-inner");
    toc.innerHTML = "";
    var heads = root.querySelectorAll("h2, h3");
    if (!heads.length) { document.querySelector(".toc").style.display = "none"; return; }
    document.querySelector(".toc").style.display = "";
    var title = document.createElement("div");
    title.className = "toc-title";
    title.textContent = "On this page";
    toc.appendChild(title);
    heads.forEach(function (h) {
      var a = document.createElement("a");
      a.href = "#/" + currentId() + "#" + h.id;
      a.textContent = h.textContent.replace(/#$/, "").trim();
      a.className = h.tagName === "H3" ? "lvl-3" : "lvl-2";
      a.dataset.target = h.id;
      toc.appendChild(a);
    });
  }

  function enhanceCode(root) {
    if (window.hljs) {
      root.querySelectorAll("pre code").forEach(function (block) {
        try { window.hljs.highlightElement(block); } catch (e) {}
      });
    }
    root.querySelectorAll("pre").forEach(function (pre) {
      if (pre.querySelector(".copy-btn")) return;
      var btn = document.createElement("button");
      btn.className = "copy-btn";
      btn.type = "button";
      btn.textContent = "Copy";
      btn.addEventListener("click", function () {
        var code = pre.querySelector("code") ? pre.querySelector("code").innerText : pre.innerText;
        navigator.clipboard.writeText(code).then(function () {
          btn.textContent = "Copied!";
          btn.classList.add("copied");
          setTimeout(function () { btn.textContent = "Copy"; btn.classList.remove("copied"); }, 1600);
        }).catch(function () {
          btn.textContent = "Failed";
          setTimeout(function () { btn.textContent = "Copy"; }, 1600);
        });
      });
      pre.appendChild(btn);
    });
  }

  function render(id) {
    var page = byId[id];
    if (!page) return;
    var content = document.getElementById("page-content");
    document.title = page.title + " — ZeroPing Docs";
    content.innerHTML = page.html;
    content.classList.add("page");

    // Breadcrumb
    var crumb = document.getElementById("breadcrumb");
    crumb.innerHTML = '<a href="#/introduction">Docs</a><span class="sep">/</span>' +
      '<span>' + page.category + '</span><span class="sep">/</span><span>' + page.title + '</span>';

    ensureHeadingIds(content);
    buildToc(content);
    enhanceCode(content);
    highlightNav(id);
    buildPageNav(id);
    setupScrollSpy();

    window.scrollTo(0, 0);
    closeSidebar();
  }

  function buildPageNav(id) {
    var nav = document.getElementById("page-nav");
    var idx = PAGES.findIndex(function (p) { return p.id === id; });
    var prev = PAGES[idx - 1];
    var next = PAGES[idx + 1];
    var html = "";
    if (prev) {
      html += '<a class="pn-prev" href="#/' + prev.id + '"><div class="pn-dir">Previous</div><div class="pn-title">' + prev.title + "</div></a>";
    } else {
      html += "<span></span>";
    }
    if (next) {
      html += '<a class="pn-next" href="#/' + next.id + '"><div class="pn-dir">Next</div><div class="pn-title">' + next.title + "</div></a>";
    } else {
      html += "<span></span>";
    }
    nav.innerHTML = html;
  }

  /* ---------------- Scroll spy ---------------- */
  var spyHandler = null;
  function setupScrollSpy() {
    var links = Array.prototype.slice.call(document.querySelectorAll("#toc-inner a"));
    if (!links.length) return;
    if (spyHandler) window.removeEventListener("scroll", spyHandler);
    spyHandler = function () {
      var pos = window.scrollY + 100;
      var current = null;
      links.forEach(function (l) {
        var el = document.getElementById(l.dataset.target);
        if (el && el.offsetTop <= pos) current = l;
      });
      links.forEach(function (l) { l.classList.remove("active"); });
      if (current) current.classList.add("active");
    };
    window.addEventListener("scroll", spyHandler);
    spyHandler();
  }

  /* ---------------- Search ---------------- */
  function stripHtml(html) {
    var tmp = document.createElement("div");
    tmp.innerHTML = html;
    return (tmp.textContent || "").replace(/\s+/g, " ").trim();
  }
  var INDEX = PAGES.map(function (p) {
    return { id: p.id, title: p.title, category: p.category, text: stripHtml(p.html) };
  });

  function escapeHtml(s) {
    return s.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
  }
  function highlightSnippet(text, q) {
    var lower = text.toLowerCase();
    var i = lower.indexOf(q.toLowerCase());
    if (i === -1) return escapeHtml(text.slice(0, 120)) + "…";
    var start = Math.max(0, i - 40);
    var end = Math.min(text.length, i + q.length + 80);
    var snip = (start > 0 ? "…" : "") + text.slice(start, end) + (end < text.length ? "…" : "");
    var re = new RegExp("(" + q.replace(/[.*+?^${}()|[\]\\]/g, "\\$&") + ")", "ig");
    return escapeHtml(snip).replace(re, "<mark>$1</mark>");
  }

  function runSearch(q) {
    var box = document.getElementById("search-results");
    q = q.trim();
    if (!q) { box.hidden = true; box.innerHTML = ""; return; }
    var results = [];
    INDEX.forEach(function (item) {
      var hay = (item.title + " " + item.category + " " + item.text).toLowerCase();
      if (hay.indexOf(q.toLowerCase()) !== -1) {
        results.push(item);
      }
    });
    results = results.slice(0, 12);
    if (!results.length) {
      box.innerHTML = '<div class="empty">No results for “' + escapeHtml(q) + '”</div>';
    } else {
      box.innerHTML = results.map(function (r) {
        return '<a class="result" href="#/' + r.id + '" data-id="' + r.id + '">' +
          '<div class="r-cat">' + r.category + '</div>' +
          '<div class="r-title">' + r.title + '</div>' +
          '<div class="r-snippet">' + highlightSnippet(r.text, q) + '</div></a>';
      }).join("");
    }
    box.hidden = false;
  }

  function setupSearch() {
    var input = document.getElementById("search-input");
    var box = document.getElementById("search-results");
    input.addEventListener("input", function () { runSearch(input.value); });
    input.addEventListener("focus", function () { if (input.value) runSearch(input.value); });
    document.addEventListener("click", function (e) {
      if (!document.querySelector(".search").contains(e.target)) box.hidden = true;
    });
    document.addEventListener("keydown", function (e) {
      if (e.key === "/" && document.activeElement !== input) {
        e.preventDefault(); input.focus();
      }
      if (e.key === "Escape") { box.hidden = true; input.blur(); }
    });
    box.addEventListener("click", function (e) {
      var a = e.target.closest(".result");
      if (a) { box.hidden = true; input.value = ""; }
    });
  }

  /* ---------------- Mobile menu ---------------- */
  function openSidebar() {
    document.getElementById("sidebar").classList.add("open");
    document.getElementById("sidebar-backdrop").classList.add("show");
  }
  function closeSidebar() {
    document.getElementById("sidebar").classList.remove("open");
    document.getElementById("sidebar-backdrop").classList.remove("show");
  }
  document.getElementById("menu-toggle").addEventListener("click", function () {
    if (document.getElementById("sidebar").classList.contains("open")) closeSidebar();
    else openSidebar();
  });
  document.getElementById("sidebar-backdrop").addEventListener("click", closeSidebar);

  /* ---------------- Version selector ---------------- */
  document.getElementById("version-select").addEventListener("change", function (e) {
    try { localStorage.setItem("zp-version", e.target.value); } catch (err) {}
  });
  try {
    var sv = localStorage.getItem("zp-version");
    if (sv) document.getElementById("version-select").value = sv;
  } catch (e) {}

  /* ---------------- Routing ---------------- */
  window.addEventListener("hashchange", function () {
    // Support #/id#heading
    var raw = (location.hash || "").replace(/^#\/?/, "");
    var id = raw.split("#")[0];
    if (!byId[id]) id = "introduction";
    render(id);
    var frag = raw.split("#")[1];
    if (frag) {
      var el = document.getElementById(frag);
      if (el) setTimeout(function () { el.scrollIntoView({ behavior: "smooth" }); }, 50);
    }
  });

  /* ---------------- Boot ---------------- */
  initTheme();
  buildSidebar();
  setupSearch();
  render(currentId());
})();
