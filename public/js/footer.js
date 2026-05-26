(function () {
  const path = window.location.pathname;
  const isPhpDir = path.includes('/php/');
  const htmlBase = isPhpDir ? '../html/' : '';

  const footerHTML =
    '<footer class="site-footer bg-white">' +
      '<div class="container">' +
        '<div class="row">' +

          '<div class="col-lg-1">' +
            '<h4>À propos de nous</h4><br>' +
            '<p class="text-center-custom">Padel, le point de rencontre des passionnés.</p>' +
          '</div>' +

          '<div class="col-lg-2">' +
            '<h4>Coordonnées</h4>' +
            '<ul>' +
              '<li>📍 Insat, Centre Urbain Nord</li>' +
              '<li>📞 28 219 290</li>' +
              '<li>✉️ Contact@Padel.tn</li>' +
            '</ul>' +
          '</div>' +

          '<div class="col-lg-3">' +
            '<h4>Liens utiles</h4><br>' +
            '<ul>' +
              '<li><a href="' + htmlBase + 'acceuil.html">Accueil</a></li>' +
              '<li><a href="' + htmlBase + 'reservation.html">Réservation</a></li>' +
              '<li><a href="' + htmlBase + 'tournois.html">Tournois</a></li>' +
              '<li><a href="' + htmlBase + 'ContactUs.html">Contact</a></li>' +
            '</ul>' +
          '</div>' +

        '</div>' +
        '<div class="text-center mt-4">' +
          '© 2026 – Conçu par' +
          '<p style="color:white;">Padel bedili 7yeti</p>' +
        '</div>' +
      '</div>' +
    '</footer>';

  var el = document.getElementById('site-footer-container');
  if (el) {
    el.outerHTML = footerHTML;
  }
})();
