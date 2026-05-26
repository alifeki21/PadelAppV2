(function () {
  const path = window.location.pathname;
  const filename = path.split('/').pop() || 'acceuil.html';
  const isPhpDir = path.includes('/php/');
  const htmlBase = isPhpDir ? '../html/' : '';
  const phpBase  = isPhpDir ? ''         : '../php/';

  const navItems = [
    {
      label : 'Accueil',
      href  : htmlBase + 'acceuil.html',
      pages : ['acceuil.html']
    },
    {
      label : 'Réservation',
      href  : htmlBase + 'reservation.html',
      pages : ['reservation.html']
    },
    {
      label : 'Tournois',
      href  : htmlBase + 'tournois.html',
      pages : ['tournois.html', 'inscription_tournois.html']
    },
    {
      label : 'Contactez-nous',
      href  : htmlBase + 'ContactUs.html',
      pages : ['ContactUs.html']
    },
    {
      label : 'Connexion',
      href  : phpBase + 'login.php',
      pages : ['login.html', 'login.php']
    },
    {
      label : "S'inscrire",
      href  : phpBase + 'sign_up.php',
      pages : ['sign_up.html', 'sign_up.php']
    }
  ];

  const navLinksHTML = navItems.map(function (item) {
    const isActive = item.pages.indexOf(filename) !== -1 ? ' active' : '';
    return '<li class="nav-item"><a class="nav-link' + isActive + '" href="' + item.href + '">' + item.label + '</a></li>';
  }).join('');

  const logoSrc = '../images/logo1.png';
  const logoHref = htmlBase + 'acceuil.html';

  const headerHTML =
    '<header class="site-header">' +
      '<nav class="navbar navbar-expand-lg navbar-light bg-white">' +
        '<div class="container">' +
          '<a class="navbar-brand" href="' + logoHref + '">' +
            '<img src="' + logoSrc + '" height="45" alt="Casa del Padel">' +
          '</a>' +
          '<div class="collapse navbar-collapse justify-content-end" id="mainNavbar">' +
            '<ul class="navbar-nav">' + navLinksHTML + '</ul>' +
          '</div>' +
        '</div>' +
      '</nav>' +
    '</header>';

  var el = document.getElementById('site-header-container');
  if (el) {
    el.outerHTML = headerHTML;
  }
})();
