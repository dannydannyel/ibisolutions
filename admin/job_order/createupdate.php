<?php 
require_once __DIR__ . "/../../inc/globals.php";
$db = require_once BASE_PATH . "/inc/database.php";
checkAuth();

// Botones por defecto en modo create
$updateMode = false;
$pageTitle = "Creación de partes";
$pageName = "Crear";

$frmId = "frmCreate";
$btnText = "Crear nuevo";
$action = "create";

//Valores por defecto
$jobOrderData = (object) [
    "check_in"=> d(),
    "check_out"=> d(7),
    "idemployer" => $_SESSION['id'],
    "idemployee" => null,
    "idvilla"=> null,
    "idservice"=> null,
    "comment"=> null,
];

$userList = $db->getUserList(['employee']);
$villaList = $db->getVillaList();
$serviceList = $db->getServiceList();

//Update data detection
if(isset($_POST['action']) && $_POST['action'] == 'update') {
  
  $jobOrderData = (object)$_POST;
  $jobOrderData->idemployer = $_SESSION['id'];
}

//Insert data detection
if(isset($_POST['action']) && $_POST['action'] == 'create') {
  $jobOrderData = (object)$_POST;
  $jobOrderData->idemployer = $_SESSION['id'];
  $dateCheckIn = fd($jobOrderData->check_in);
  $dateCheckOut = fd($jobOrderData->check_out);
  $idEmployee = $jobOrderData->idemployee;
  $idVilla  = $jobOrderData->idvilla;
  $resReasigned = $db->checkReasignedService($dateCheckIn, $dateCheckOut, $idEmployee, $idVilla);
  if($resReasigned) {
    $error = "Ya tenía una tarea asignada el empleado que coincide con la franja de tiempo el mismo día";
  }
  else {
   
    $dataIns = [
      'check_in' => $dateCheckIn,
      'check_out' => $dateCheckOut,
      'idemployer' => $jobOrderData->idemployer,
      'idemployee' => $idEmployee,
      'idvilla' => $idVilla,
      'idservice' => $_POST['idservice'],
      'comment' => $_POST['comments']
    ];
    $res = $db->insertJobOrder($dataIns);
    $message = "Parte de trabajo creado correctamente";

  }
}
?>
<!doctype html>
<html lang="en" data-bs-theme="auto">
  <head>
    
  <?php include_once BASE_PATH . "/inc/parts/head.php";?>
    
  </head>
  <body>
    
    <?php include_once BASE_PATH . "/inc/parts/bsChangeMode.php";?>

<?php include_once BASE_PATH . "/inc/parts/header.php";?>
<?php include_once BASE_PATH . "/inc/parts/breadcrumbs.php";?>
<main>
  <div class="container">
  
  <section class="py-5 text-center container">
    <div class="row py-lg-5">
      <div class="col-lg-6 col-md-8 mx-auto">
        <h1 class="fw-light"><?=$pageTitle?></h1>
        <?php if(isset($error)):?>
            <div class="alert alert-danger"><?=$error?></div>
        <?php endif; ?>
        <?php if(isset($message)):?>
            <div class="alert alert-success"><?=$message?></div>
        <?php endif; ?>
          
        <form class="row g-3" id="<?=$frmId?>" method="post">
            <div class="col-md-6">
                <label for="inputCheckIn" class="form-label">Inicio</label>
                <input type="datetime-local" class="form-control" name="check_in" id="inputCheckIn" value="<?=$jobOrderData->check_in?>" required min="<?=d()?>" max="<?=d(365)?>">
            </div>
            <div class="col-md-6">
                <label for="inputCheckOut" class="form-label">Fin</label>
                <input type="datetime-local" class="form-control" name="check_out" id="inputCheckOut" value="<?=$jobOrderData->check_out?>" required min="<?=d()?>" max="<?=d(365)?>">
            </div>
            <div class="col-md-6">
                <label for="selEmployee" class="form-label">Empleado</label>
                <select id="selEmployee" class="form-select" name="idemployee" aria-label="Seleccionar propietario" required>
                    <option value="">[Empleado]</option>
                    <?php foreach($userList as $owner):
                        $selectedAttr = ($owner['id'] == $villaData->iduser) ? " selected":'';

                        ?>
                        <option value="<?=$owner['id']?>"<?=$selectedAttr?>><?=$owner['name']?></option>
                    <?php endforeach;?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="selVilla" class="form-label">Villa</label>
                <select id="selVilla" class="form-select" name="idvilla" aria-label="Seleccionar villa" required>
                    <option value="">[Villa]</option>
                    <?php foreach($villaList as $villa):
                        $selectedAttr = ($villa['id'] == $jobOrderData->idvilla) ? " selected":'';

                        ?>
                        <option value="<?=$villa['id']?>"<?=$selectedAttr?>><?=$villa['name']?></option>
                    <?php endforeach;?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="selService" class="form-label">Servicio</label>
                <select id="selService" class="form-select" name="idservice" aria-label="Seleccionar servicio" required>
                    <option value="">[Servicio]</option>
                    <?php foreach($serviceList as $service):
                        $selectedAttr = ($service['id'] == $jobOrderData->idservice) ? " selected":'';

                        ?>
                        <option value="<?=$service['id']?>"<?=$selectedAttr?>><?=$service['name']?></option>
                    <?php endforeach;?>
                </select>
            </div>
            <div class="col-md-8">
                <label for="inputComments" class="form-label">Comentarios para el trabajador</label>
                <textarea class="form-control" id="inputComments" name="comments" rows="3" max="500"></textarea>
            </div>
            <div class="col-12">
                <button type="submit" name="action" value="<?=$action?>" class="btn btn-primary"><?=$btnText?></button>
            </div>
        </form>
      </div>
    </div>
    </section>
  </div><!-- //.container -->
</main>
<?php include_once BASE_PATH . "/inc/parts/footer.php";?> 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>