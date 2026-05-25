/**
 * Gallery — Masonry + GLightbox
 *
 * @description Transforme automatiquement tous les blocs Galerie natifs
 *              WordPress en grille Masonry + lightbox GLightbox.
 *              Spinner SVG CSS minimaliste pendant le chargement des images.
 *              Ratio natif préservé avant chargement pour éviter l'empilement.
 *
 * @requires    masonry (WP core), imagesloaded (WP core), glightbox (CDN)
 *
 * @author      Ton Nom
 * @version     1.3.0
 *
 * @note        Fourni à titre de référence.
 *              En production, injecté via wp_add_inline_script() dans enqueue.php
 */

document.addEventListener("DOMContentLoaded", function () {

  // ── Précharge toutes les images lazy des galeries ────────────────────────
  document.querySelectorAll(".wp-block-gallery img[loading='lazy']").forEach(function (img) {
    img.setAttribute("loading", "eager");
    if (img.dataset.src) {
      img.src = img.dataset.src;
    }
  });

  // ── Init galeries après stabilisation du DOM ─────────────────────────────
  setTimeout(function () {

    document.querySelectorAll(".wp-block-gallery").forEach(function (gallery, gIdx) {

      var items = Array.from(gallery.querySelectorAll("figure")).map(function (fig) {
        var img  = fig.querySelector("img");
        var link = fig.querySelector("a");
        return {
          img:     img,
          fullSrc: link ? link.href : (img ? img.src : "")
        };
      }).filter(function (item) { return item.img; });

      if (!items.length) return;

      var grid = document.createElement("div");
      grid.className = "masonry-grid";

      var sizer = document.createElement("div");
      sizer.className = "masonry-grid-sizer";
      grid.appendChild(sizer);

      var msnry;

      items.forEach(function (item) {
        var div = document.createElement("div");
        div.className = "masonry-item";

        // ── Ratio natif de l'image pour réserver la hauteur ───────────────
        var w = parseInt(item.img.getAttribute("width"));
        var h = parseInt(item.img.getAttribute("height"));
        if (w && h) {
          div.style.paddingBottom = ((h / w) * 100) + "%";
        } else {
          div.style.paddingBottom = "75%"; // fallback ratio 4:3
        }

        // ── Spinner SVG ───────────────────────────────────────────────────
        var spinner = document.createElement("div");
        spinner.className = "img-spinner";
        spinner.innerHTML =
          '<svg width="40" height="40" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">' +
            '<rect x="16" y="16" width="16" height="16" fill="none" stroke="currentColor" stroke-width="0.5"/>' +
            '<rect x="16" y="16" width="16" height="16" fill="none" stroke="currentColor" stroke-width="0.5"/>' +
          '</svg>';
        div.appendChild(spinner);

        // ── Image ─────────────────────────────────────────────────────────
        var a = document.createElement("a");
        a.className       = "glightbox";
        a.href            = item.fullSrc;
        a.dataset.gallery = "gallery-" + gIdx;

        var clonedImg = item.img.cloneNode(true);
        clonedImg.removeAttribute("loading");
        clonedImg.removeAttribute("decoding");
        clonedImg.setAttribute("loading", "eager");

        // Retire le padding et affiche l'image une fois chargée
        clonedImg.addEventListener("load", function () {
          div.style.paddingBottom = "";
          this.classList.add("loaded");
          spinner.classList.add("hidden");
          if (msnry) msnry.layout();
        });

        a.appendChild(clonedImg);
        div.appendChild(a);
        grid.appendChild(div);
      });

      // ── Remplace le contenu natif WP ──────────────────────────────────────
      gallery.innerHTML = "";
      gallery.appendChild(grid);

      // ── Init Masonry + recalculs de sécurité ──────────────────────────────
      imagesLoaded(grid, function () {
        msnry = new Masonry(grid, {
          itemSelector:       ".masonry-item",
          columnWidth:        ".masonry-grid-sizer",
          percentPosition:    true,
          gutter:             16,
          transitionDuration: "0.25s"
        });
        [300, 600, 1200, 2500].forEach(function (delay) {
          setTimeout(function () {
            if (msnry) msnry.layout();
          }, delay);
        });
      });

      // ── Recalcul sur chaque image chargée ─────────────────────────────────
      grid.querySelectorAll("img").forEach(function (img) {
        img.addEventListener("load", function () {
          if (msnry) msnry.layout();
        });
      });

    });

  }, 300);

  // ── GLightbox — init après reconstruction du DOM ─────────────────────────
  setTimeout(function () {
    GLightbox({
      selector:        ".glightbox",
      touchNavigation: true,
      loop:            true,
      openEffect:      "zoom",
      closeEffect:     "fade",
      slideEffect:     "slide",
      descPosition:    "bottom"
    });
  }, 500);

});
