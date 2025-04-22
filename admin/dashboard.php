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
<!-- Modal Bootstrap -->
<div class="modal fade" id="employeeModal" tabindex="-1" aria-labelledby="employeeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="employeeModalLabel">Datos del empleado</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div id="employeeDetails"></div>
        <hr>
        <h6>Trabajos realizados</h6>
        <div style="max-height: 300px; overflow-y: auto;">
          <form id="jobData">
          <div class="row">
            <div class="col-md-6">
              <label for="checkIn" class="form-label">Check-In estimado</label>
              <input type="datetime-local" id="checkIn" class="form-control" disabled>
            </div>
            <div class="col-md-6">
              <label for="checkOut" class="form-label">Check-Out estimado</label>
              <input type="datetime-local" id="checkOut" class="form-control" disabled>
            </div>
          </div>

          <hr>

          <div class="row">
            <div class="col-md-6">
              <label for="checkInEmp" class="form-label">Check-In empleado</label>
              <input type="datetime-local" id="checkInEmp" class="form-control">
            </div>
            <div class="col-md-6">
              <label for="checkOutEmp" class="form-label">Check-Out empleado</label>
              <input type="datetime-local" id="checkOutEmp" class="form-control">
            </div>
          </div>

          <hr>

          <div class="mb-3">
            <label for="adminComment" class="form-label">Comentario del administrador</label>
            <textarea id="adminComment" class="form-control" rows="2"></textarea>
          </div>

          <div class="mb-3">
            <label for="employeeComment" class="form-label">Comentario del empleado</label>
            <textarea id="employeeComment" class="form-control" rows="2" disabled></textarea>
          </div>

          <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
          </div>
          </form>
        </div>
        <!-- Dentro del modal, debajo de la tabla -->
        <div id="jobExtraDetails" class="mt-3 d-none">
          <h6>Detalles del trabajo</h6>
          <div id="jobDetailContent" class="border p-2 rounded bg-light"></div>
        </div>
      </div>
    </div>
  </div>
</div>

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
      events: '../callbacks/get_job_orders.php',
      eventClick: (info) => {
        info.jsEvent.preventDefault();
       
        console.log(info.event);
        const idEmp = parseInt(info.event.extendedProps.idEmp);
        const idJob = parseInt(info.event.id);

      // Petición AJAX para datos del empleado
      fetch('../callbacks/get_job_data.php?id=' + idJob)
        .then(response => response.json())
        .then(data => {
          // Mostramos datos del empleado
          const { name, surname, dni, phone, email } = data.employee;
          document.getElementById('employeeDetails').innerHTML = `
            <p><strong>Nombre:</strong> ${name} ${surname}</p>
            <p><strong>DNI:</strong> ${dni}</p>
            <p><strong>Teléfono:</strong> ${phone}</p>
            <p><strong>Email:</strong> ${email}</p>
          `;

          // Mostramos datos de trabajo
          /*
          const jobRows = data.jobs.map((job, index) => `
          <tr>
            <td><a href="#" class="checkin-link" data-index="${index}">${job.check_in}</a></td>
            <td>${job.check_out}</td>
            <td>${job.check_in_employee ?? '-'}</td>
            <td>${job.check_out_employee ?? '-'}</td>
          </tr>
        `).join('');
        document.getElementById('jobOrdersTableBody').innerHTML = jobRows;*/
        
        // Rellenar formulario con los datos del trabajo
        const job = data.job;
        document.getElementById('checkIn').value = formatForInput(job.check_in);
        document.getElementById('checkOut').value = formatForInput(job.check_out);
        document.getElementById('checkInEmp').value = formatForInput(job.check_in_employee);
        document.getElementById('checkOutEmp').value = formatForInput(job.check_out_employee);
        document.getElementById('adminComment').value = job.comment ?? '';
        document.getElementById('employeeComment').value = job.comment_employee ?? '';

        // Agregar listeners a los check_in
        /*
        document.querySelectorAll('.checkin-link').forEach(link => {
          link.addEventListener('click', function(e) {
            e.preventDefault();
            const index = this.dataset.index;
            const job = data.jobs[index];

            // Mostrar detalles en el contenedor oculto
            document.getElementById('jobDetailContent').innerHTML = `
              <p><strong>Villa:</strong> ${job.villa} (ID: ${job.idvilla})</p>
              <p><strong>ID del trabajo:</strong> ${job.idjob}</p>
              <p><strong>Comentario:</strong> ${job.comment ?? 'Sin comentarios'}</p>
              <p><strong>Hora del comentario:</strong> ${job.comment_time ?? '-'}</p>
            `;
            document.getElementById('jobExtraDetails').classList.remove('d-none');
          });
        });*/

          // Mostrar modal
          const modal = new bootstrap.Modal(document.getElementById('employeeModal'));
          modal.show();
        });
        
      }
    });
      calendar.render();
    });

    // Función para adaptar datetime SQL al input
    function formatForInput(datetimeStr) {
      if (!datetimeStr) return '';
      return datetimeStr.replace(' ', 'T');
    }
</script>
    </body>
</html>
