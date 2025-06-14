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
  
  <section class="py-3 text-center container">
    <div class="row py-lg-2">
      <div class="col-lg-6 col-md-8 mx-auto">
        <h1 class="fw-light">Ibisolutions</h1>

          <a role="button" href="<?=genUrl('employee/job_order/index.php')?>" class="btn btn-primary my-2">Ver mis partes</a>
        
      </div>
    </div>
  </section>
  <hr>
  <h2 class="fw-light">Mis partes de trabajo</h2>
  <div id="calendar"></div>
</main><!-- //.container -->
<!-- Modal Bootstrap -->
<div class="modal fade" id="employeeModal" tabindex="-1" aria-labelledby="employeeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="employeeModalLabel">Datos del parte</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <h6>Datos del parte</h6>
        <div style="max-height: 300px; overflow-y: auto;">
          <form id="jobData">
            <input type="hidden" id="hidIdJob" value="">
          <div class="row">
            <div class="col-md-12">
                <b>Villa:</b> <span id="villa"></span>
            </div>
            <div class="col-md-6">
              <label for="checkIn" class="form-label">Check-In estimado</label>
              <input type="datetime-local" id="checkIn" class="form-control" disabled>
            </div>
            <div class="col-md-6">
              <label for="checkOut" class="form-label">Check-Out estimado</label>
              <input type="datetime-local" id="checkOut" class="form-control" disabled>
            </div>
            <div class="col-md-12">
              <label for="checkOut" class="form-label">Datos de la tarea</label>
              <textarea id="adminComment" class="form-control" disabled></textarea>
            </div>
          </div>

          <hr>

          <div class="row">
            <div class="col-md-6">             
                <button type="button" class="btn btn-lg btn-danger" id="checkInEmpAction" style="display:none">FICHAR ENTRADA</button>
            </div>
            <div class="col-md-6">
                <button type="button" class="btn btn-lg btn-success" id="checkOutEmpAction" style="display:none">FICHAR SALIDA</button>
            </div>
            <div class="col-md-12 pt-2">             
                <textarea id="jobSummary" class="form-control" disabled></textarea>
            </div>
          </div>

          <div id="modalError" class="alert alert-danger" style="display:none"></div>
          <div id="modalSuccess" class="alert alert-success" style="display:none"></div>
          <hr>

          
          <div class="mb-3">
            <label for="employeeComment" class="form-label">Incidencias de la tarea</label>
            <textarea id="employeeComment" class="form-control" rows="2"></textarea>
          </div>

          <div class="d-flex justify-content-end">
            <button id="btnSaveComment" type="submit" class="btn btn-primary">Guardar comentario</button>
            <button type="button" class="btn btn-secondary ms-2" data-bs-dismiss="modal" aria-label="Cerrar">Cerrar</button>
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
  function displayModalError(mess) {
    document.getElementById('modalSuccess').innerText = '';
    document.getElementById('modalSuccess').style.display = 'none';
    document.getElementById('modalError').style.display = 'block';
    document.getElementById('modalError').innerText = mess;
  }
  function displayModalSuccess(mess) {
    document.getElementById('modalError').innerText = '';
    document.getElementById('modalError').style.display = 'none';
    document.getElementById('modalSuccess').style.display = 'block';
    document.getElementById('modalSuccess').innerText = mess;
  }

  function closeAndRefreshCalendar(mess) {
    const modal = document.getElementById('employeeModal');
    
    displayModalSuccess(mess);
    modal.addEventListener('hidden.bs.modal', function () {
      calendar.refetchEvents();
    });
  }
 
  let calendar = null;
  document.addEventListener('DOMContentLoaded', () => {
      const calendarEl = document.getElementById('calendar');
      calendar = new FullCalendar.Calendar(calendarEl, {
      
      themeSystem: 'bootstrap5',
      
      locale: 'es',
      initialView: 'dayGridMonth',
      events: '../callbacks/get_job_orders_employee.php',
      eventClick: (info) => {
        info.jsEvent.preventDefault();
       
        //console.log(info.event);
        const idEmp = parseInt(info.event.extendedProps.idEmp);
        const idJob = parseInt(info.event.id);

      // Petición AJAX para datos del empleado
      fetch('../callbacks/get_job_data.php?id=' + idJob)
        .then(response => response.json())
        .then(data => {
          document.getElementById('hidIdJob').value = idJob;
            document.getElementById('checkInEmpAction').style.display = 'none';
            document.getElementById('checkInEmpAction').disabled = false;
            document.getElementById('checkOutEmpAction').style.display = 'none';
            document.getElementById('checkOutEmpAction').disabled = false;
            document.getElementById('employeeComment').disabled = false;
            document.getElementById('jobSummary').style.display = 'none';
            document.getElementById('btnSaveComment').disabled = false;
            document.getElementById('modalError').style.display = 'none';
            document.getElementById('modalSuccess').style.display = 'none';
          // Mostramos datos del empleado
          const { name, surname, dni, phone, email } = data.employee;
          

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
        document.getElementById('villa').innerText = job.villa;
        document.getElementById('checkIn').value = formatForInput(job.check_in);
        document.getElementById('checkOut').value = formatForInput(job.check_out);
        document.getElementById('adminComment').value = job.comment ?? '';
        document.getElementById('employeeComment').value = job.comment_time != '' ? job.comment_time: "";
        
        // Preparación de botones de fichaje según el caso
        if(job.check_in_employee == null) {
            document.getElementById('checkInEmpAction').style.display = 'block';
            document.getElementById('checkOutEmpAction').style.display = 'none';
        }
        else if(job.check_out_employee == null) {
            document.getElementById('checkInEmpAction').style.display = 'none';
            document.getElementById('checkOutEmpAction').style.display = 'block';
        }
        else if(job.check_in_employee != null && job.check_out_employee != null) {
            //Tarea realizada
            document.getElementById('jobSummary').style.display = 'block';
            document.getElementById('jobSummary').value = `Tarea realizada en ${job.check_in_employee} - ${job.check_out_employee}`;

            document.getElementById('checkInEmpAction').disabled = true;
            document.getElementById('checkOutEmpAction').disabled = true;
            document.getElementById('employeeComment').disabled = true;
            document.getElementById('checkInEmpAction').style.display = 'none';
            document.getElementById('checkOutEmpAction').style.display = 'none';
            document.getElementById('btnSaveComment').disabled = true;
        }
        /*
        document.getElementById('checkInEmp').value = formatForInput(job.check_in_employee);
        document.getElementById('checkOutEmp').value = formatForInput(job.check_out_employee);
        */
       
        

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

    document.getElementById('checkInEmpAction').addEventListener('click', e => {
      e.preventDefault();
      
      const idJob = document.getElementById('hidIdJob').value;
      
      clockInOutEmployee(idJob);
    });
    document.getElementById('checkOutEmpAction').addEventListener('click', e => {
      e.preventDefault();
      
      const idJob = document.getElementById('hidIdJob').value;
      
      clockInOutEmployee(idJob);
    });

    function clockInOutEmployee(idJob) {
      
      //You can set headers like this:
      const headers = new Headers();
      headers.append('Content-Type', 'application/json');

      fetch('../callbacks/clock_inout_employee.php?id=' + idJob, {
        method: 'PUT',
        headers: headers,
        body: ''
      })
      .then(response => {
        if(!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
      })
      .then(responseData => {
        console.log("Call to save comment success:", responseData);
        const code = responseData.code ?? null;
        const message = responseData.message ?? null;
        if(code === null) {
          throw new Error("Error obteniendo respuesta formateada (code)");
        }
        if(code != 0) {
          displayModalError(`Error de respuesta ${code}: ${message}`);
          return;
        }
        //El mensaje es un array para el tipo de fichaje ('in' o 'out') y un mensaje
        if(message[0] == 'in') {
          document.getElementById('checkInEmpAction').disabled = true;
        }
        else { //Out
          document.getElementById('checkOutEmpAction').disabled = true;
          document.getElementById('employeeComment').disabled = true;
          document.getElementById('btnSaveComment').disabled = true;
        }
        displayModalSuccess(message[1]);
        //Recargar eventos del calendario
        calendar.refetchEvents();
        //closeAndRefreshCalendar(message[1]);
      })
      .catch(error => {
        console.error("Error:", error);
        displayModalError(error);
      })
    }

    document.getElementById('btnSaveComment').addEventListener('click', e => {
      e.preventDefault();
      const idJob = document.getElementById('hidIdJob').value;
      const jsonData = JSON.stringify({
        comment: document.getElementById('employeeComment').value,
      });
      //You can set headers like this:
      const headers = new Headers();
      headers.append('Content-Type', 'application/json');

      fetch('../callbacks/save_comment_employee.php?id=' + idJob, {
        method: 'PUT',
        headers: headers,
        body: jsonData
      })
      .then(response => {
        if(!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
      })
      .then(responseData => {
        console.log("Call to save comment success:", responseData);
        const code = responseData.code ?? null;
        const message = responseData.message ?? null;
        if(code === null) {
          throw new Error("Error obteniendo respuesta formateada (code)");
        }
        if(code != 0) {
          displayModalError(`Error de respuesta ${code}: ${message}`);
          return;
        }
        displayModalSuccess("Actualizado el comentario correctamente");
        
      })
      .catch(error => {
        console.error("Error:", error);
        displayModalError(error);
      })
    })
</script>
    </body>
</html>
