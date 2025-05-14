<?php 
require_once __DIR__ . "/../../inc/globals.php";
$db = require_once BASE_PATH . "inc/database.php";
checkAuth();

$resUsers = $db->getUserList();
$pageTitle = "Empleados"; // Valor por defecto

?>
<!doctype html>
<html lang="en" data-bs-theme="auto">
  <head>

  <?php include_once BASE_PATH . "/inc/parts/head.php";?>
    
  </head>
  <body>

    <?php include_once BASE_PATH . "/inc/parts/bsChangeMode.php";?>

<?php include_once BASE_PATH . "inc/parts/header.php";?>

<main>
<div class="container">

<?php include_once BASE_PATH . "/inc/parts/breadcrumbs.php";?>

  <a role="button" class="btn btn-success" href="<?=genUrl("admin/user/createupdate.php")?>">Nuevo</a>

  <table class="table" id="tbl-users">
  <thead>
    <tr>
      <th scope="col">ID</th>
      <th scope="col">Nombre</th>
      <th scope="col">Apellido</th>
      <th scope="col">DNI</th>
      <th scope="col">Teléfono</th>
      <th scope="col">Email</th>
      <th scope="col">Rol</th>
      <th scope="col" colspan="2">Acciones</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($resUsers as $row):?>
    <tr>
      <th scope="row"><?=$row['id']?></th>
      <td><?=$row['name']?></td>
      <td><?=$row['surname']?></td>
      <td><?=$row['dni']?></td>
      <td><?=$row['phone']?></td>
      <td><?=$row['email']?></td>
      <td><?=$row['role']?></td>
      <td><button type="button" class="btn btn-success btn-update" data-id="<?=$row['id']?>">Actualizar</button></td>
      <td><button type="button" class="btn btn-danger btn-delete" data-id="<?=$row['id']?>">Borrar</button></td>
    </tr>
    <?php endforeach;
    $resUsers->free_result();
    ?>
  </tbody>
</table><!-- //#tbl-user -->


  </div><!-- //.container -->

  
  
</main>

<?php include_once BASE_PATH . "/inc/parts/footer.php";?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script>
  $('.btn-update').on('click', (e) => {
    const btnClicked = $(e.currentTarget);
    const id = btnClicked.data('id');
    location.assign("<?=genUrl('admin/user/createupdate.php?id=')?>"+id);
  });
</script>

</body>
</html>
