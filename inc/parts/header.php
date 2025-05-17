
<?php
switch($_SESSION['role']) {
  case 'admin':
    $urlDashboard = "admin/dashboard.php";
    $role = "admin";
    break;
  case 'employee':
    $urlDashboard = "employee/dashboard.php";
    $role = "employee";
    break;
  default:
    $urlDashboard = "employer/dashboard.php";
    $role = "admin";
}
?>
<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?=genUrl($urlDashboard)?>">Ibisolutions</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
     
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
      <?php if($role == 'admin'):?>
        <li class="nav-item">
          <a class="nav-link" href="<?=genUrl('admin/user/')?>">Usuarios</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?=genUrl('admin/villa/')?>">Villas</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?=genUrl('admin/job_order/')?>">Partes de trabajo</a>
        </li>
      <?php endif;?>
        <li class="nav-item">
          <a class="nav-link" href="<?=genUrl('logout.php')?>">Salir</a>
        </li>
        
      </ul>
      
      <form class="d-flex" role="search">
        <input class="form-control me-2" type="search" placeholder="Buscar" aria-label="Search">
        <button class="btn btn-outline-success" type="submit">Buscar</button>
      </form>
    </div>
  </div>
    
</nav>