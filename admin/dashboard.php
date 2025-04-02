<?php 
require_once __DIR__ . "/../inc/globals.php";
$db = require_once BASE_PATH . "/inc/database.php";
checkAuth();
?>
<!doctype html>
<html lang="en" data-bs-theme="auto">
  <head>
    
  <?php include_once BASE_PATH . "/inc/parts/head.php";?>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <link href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css' rel='stylesheet'>
  </head>
  <body>
    
    <?php include_once BASE_PATH . "/inc/parts/bsChangeMode.php";?>

<?php include_once BASE_PATH . "/inc/parts/header.php";?>
<?php include_once BASE_PATH . "/inc/parts/breadcrumbs.php";?>
<main class="container">
  
  <section class="py-5 text-center container">
    <div class="row py-lg-5">
      <div class="col-lg-6 col-md-8 mx-auto">
        <h1 class="fw-light">Ibisolutions</h1>

          <a role="button" href="<?=genUrl('admin/job_order/createupdate.php')?>" class="btn btn-primary my-2">Nuevo parte</a>
          <a role="button" href="<?=genUrl('admin/villa/')?>" class="btn btn-secondary my-2">Nueva villa</a>
          <a role="button" href="<?=genUrl('admin/user/')?>" class="btn btn-primary my-2">Nuevo usuario</a>
        
      </div>
    </div>
  </section>
  <hr>
  <h2 class="fw-light">Calendario de partes de trabajo</h2>
  <div id="calendar"></div>
</main><!-- //.container -->

<?php include_once BASE_PATH . "/inc/parts/footer.php";?> 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<!--<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/local-all.min.js'></script>-->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js'></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
      
      themeSystem: 'bootstrap5',
      
      locale: 'es',
      initialView: 'dayGridMonth',
      events: '../callbacks/get_job_orders.php'
    });
    calendar.render();
  });
</script>
    </body>
</html>
