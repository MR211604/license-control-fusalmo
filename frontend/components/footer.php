<footer>
  <div class="container">
    <p>&copy; <?php echo date("Y"); ?> FUSALMO. All rights reserved.</p>
    <ul class="social-media">
      <li><a href="https://www.facebook.com/FUSALMO" target="_blank">Facebook</a></li>
      <li><a href="https://www.instagram.com/FUSALMO" target="_blank">Instagram</a></li>
      <li><a href="https://fusalmo.org/" target="_blank">Sitio web</a></li>
    </ul>
  </div>
</footer>

<style>
  footer {
    background-color: #f8f9fa;
    padding: 20px 0;
    text-align: center;
    bottom: 0;
    width: 100%;
    left: 0;
    right: 0;
    z-index: 100;
  }

  /* Asegurar que el contenido principal no se oculte detrás del footer */
  body {
    min-height: 100vh;
    position: relative;
  }

  .social-media {
    list-style: none;
    padding: 0;
    margin: 10px 0 0 0;
  }

  .social-media li {
    display: inline;
    margin: 0 10px;
  }

  .social-media a {
    text-decoration: none;
    color: #007bff;
  }

  .social-media a:hover {
    text-decoration: underline;
  }
</style>