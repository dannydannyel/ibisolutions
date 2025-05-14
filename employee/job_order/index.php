<?php
/**
 * This file uses the similar structure as admin/user/index.php to generate a table with the job orders for current logged emplyee and with a color to see the job order status, not started, started or finished, but includes pagination
 * 
 */
require_once __DIR__ . "/../../inc/globals.php";
$db = require_once BASE_PATH . "/inc/database.php";
checkAuth();
$pageName = "Parte de trabajo";
$pageTitle = "Partes de trabajo";

// Check for valid employee
$id = $_SESSION['id'];
$employee = $db->getUserById($id);
$employee = $employee->fetch_assoc();
if($employee['role'] != 'employee') {
    header("Location: " . genUrl("employee/dashboard.php"));
    exit;
}

// Check for filter
$pendingOnly = false;
if(isset($_GET['f']) && $_GET['f'] == 'pending') {
    $pendingOnly = true;
}
if(isset($_GET['f']) && $_GET['f'] == 'finished') {
    $pendingOnly = false;
}

$title = $pendingOnly ? "Mis partes de trabajo pendientes" : "Mis partes de trabajo finalizados";

if(isset($_GET['r']) && $_GET['r'] == 'in') {
    $success = "Fichaje de entrada realizado con éxito";
}
if(isset($_GET['r']) && $_GET['r'] == 'out') {
    $success = "Fichaje de salida realizado con éxito";
}
// Get job orders for current employee
$resJobOrders = $db->getEmployeeTasks($id, false, true);
if($resJobOrders->num_rows == 0) {
    $resJobOrders = null;
}
else {
    $resJobOrders = $resJobOrders->fetch_all(MYSQLI_ASSOC);
}
//print_r($resJobOrders);

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
<main>
<div class="container">
<?php include_once BASE_PATH . "/inc/parts/breadcrumbs.php";?>
<h2 class="fw-light"><?=$title?></h2>
<?php if(isset($error)):?>
<div class="alert alert-danger" id="error-message"><?=$error?></div>
<?php endif;?>
<?php if(isset($success)):?>
<div class="alert alert-success" id="success-message"><?=$success?></div>
<?php endif;?>
<div class="alert alert-success" id="success-message2" style="display: none;"></div>
<div class="alert alert-danger" id="error-message2" style="display: none;"></div>

<div class="row">
    <?php if(!$pendingOnly):?>
    <a role="button" class="btn btn-success" href="<?=genUrl("employee/job_order/?f=pending")?>">Mostrar pendientes</a>
  <?php else:?>
    <a role="button" class="btn btn-secondary" href="<?=genUrl("employee/job_order/?f=finished")?>">Mostrar finalizadas</a>
  <?php endif;?>
</div>
<table class="table" id="tbl-employee-job-orders">
  <thead>
    <tr>
      <th scope="col">Villa</th>
      <th scope="col">Inicio estimado</th>
      <th scope="col">Fin estimado</th>
      <th scope="col">Fichaje entrada</th>
      <th scope="col">Fichaje salida</th>
      <th scope="col" colspan="2">Acciones</th>
    </tr>
  </thead>
  <tbody>
    <?php 
    if(!is_null($resJobOrders)):
    foreach($resJobOrders as $row):
        $isFinished = !empty($row['check_out_employee']);
        
        // Si quiero solo los pendientes y el trabajo está finalizado lo descarto
        if($pendingOnly && $isFinished) {
            continue;
        }
        // Si quiero finalizados y el trabajo no está finalizado lo descarto
        if(!$pendingOnly && !$isFinished) {
            continue;
        }
    ?>
    <tr>
      <th scope="row"><?=$row['villa']?></th>
      <td><?=$row['check_in']?></td>
      <td><?=$row['check_out']?></td>
      <td><?=$row['check_in_employee']?></td>
      <td><?=$row['check_out_employee']?></td>
      
      <td>
        <?php if(empty($row['check_in_employee'])):?>
        <button type="button" class="btn btn-success btn-sm btn-clock" data-id="<?=$row['idjob']?>" data-type="in">Marcar Entrada</button>
        <?php endif;?>
      </td>
      <td>
        <?php if(empty($row['check_out_employee']) && !empty($row['check_in_employee'])):?>
        <button type="button" class="btn btn-danger btn-sm btn-clock" data-id="<?=$row['idjob']?>" data-type="out">Marcar Salida</button>
        <?php endif;?>
      </td>
    </tr>
    <?php endforeach;
    endif;
    ?>
  </tbody>
</table><!-- //#tbl-user -->
</div>
</main>
<script>
    // Detect handler for btn-clock and throw a async fall to callbacks/check_inout_employee.php
    document.querySelectorAll(".btn-clock").forEach((el) => {
        el.addEventListener("click", (e) => {
            e.preventDefault();
            const idJob = el.dataset.id;
            const type = el.dataset.type;
            const url = "<?=genUrl('callbacks/clock_inout_employee.php')?>?id=" + idJob;
            const data = {id: idJob, type: type};
            fetch(url, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if(data.code == 0) {
                    // Display message in message-container
                    const messageContainer = document.getElementById('success-message2');
                    messageContainer.innerHTML = data.message[1];
                    messageContainer.style.display = 'block';
                    setTimeout(() => {
                        messageContainer.style.display = 'none';
                    }, 3000);
                    
                    // Reload the page to see the changes passing query string r=1
                    location.assign("<?=genUrl('employee/job_order/index.php')?>?r=" + data.message[0] + "&f=<?=$pendingOnly ? 'pending' : 'finished'?>");

                    //location.reload();
                }
                else {
                    // display error message in error-container
                    const errorContainer = document.getElementById('error-message2');
                    errorContainer.innerHTML = data.message;
                    errorContainer.style.display = 'block';
                    
                    
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const errorContainer = document.getElementById('error-message2');
                errorContainer.innerHTML = "Error: " + error;
                errorContainer.style.display = 'block';
                
            });
        });
    });
</script>
</body>
</html>