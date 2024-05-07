<?php

$arrContextOptions=array(
  "ssl"=>array(
      "verify_peer"=>false,
      "verify_peer_name"=>false,
  ),
);

$getVars = file_get_contents('https://api.bcra.gob.ar/estadisticas/v1/PrincipalesVariables', false, stream_context_create($arrContextOptions));
$vars = json_decode($getVars);

?>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PHP - Basics</title>
</head>
<body class="bg-[#f7f7f7]">
  <script src="https://cdn.tailwindcss.com"></script>

  <main class="bg-[#fff] sm:w-[80%] m-auto shadow-lg shadow-lg sm:my-8">
    <h1 class="text-center text-black p-8 sm:text-4xl text-3xl font-bold">Buscador variables BCRA</h1>

    <form class="flex justify-center py-3 max-sm:m-auto max-sm:mb-5" action="" method="post">
      <input class="border shadow-md text-center" type="date" name="desde" id="desde" required>

      <select class="border shadow-md max-sm:w-[60%]" name="var" id="var" max=<?=date("Y-m-d")?> required>
        <option value="empty">Selecciona una variable</option>
        <option value="1">Reservas Internacionales</option>
        <option value="4">Tipo de Cambio Minorista</option>
        <option value="5">Tipo de Cambio Mayorista</option>
        <option value="6">Tasa de Política Monetaria</option>
        <option value="7">BADLAR en pesos de bancos privados</option>
        <option value="8">TM20 en pesos de bancos privados</option>
        <option value="9">Tasas de interés de las operaciones de pase activas</option>
        <option value="10">Tasas de interés de las operaciones de pase pasivas</option>
      </select>

      <button class="p-3 bg-black shadow-md text-white" type="submit">Buscar</button>
    </form>

    <?php

    // Form handling
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $desde = htmlspecialchars($_POST['desde']);
      $varId = htmlspecialchars($_POST['var']);
      $errors = false;
  
      if (empty($desde) || empty($varId) || $varId == "empty") {
        $errors = true;
        echo "<p class='text-red-600 text-2xl text-center font-bold max-sm:pb-5'>Ingresa todos los datos.</p>";
      }
      
      if (!$errors) {
        $varString = match($varId) {
          "empty" => "",
          "1" => "Reservas Internacionales",
          "4" => "Tipo de Cambio Minorista",
          "5" => "Tipo de Cambio Mayorista",
          "6" => "Tasa de Política Monetaria",
          "7" => "BADLAR en pesos de bancos privados",
          "8" => "TM20 en pesos de bancos privados",
          "9" => "Tasas de interés de las operaciones de pase activas",
          "10" => "Tasas de interés de las operaciones de pase pasivas",
        };

        $desdeFormat = date("d-m-Y", strtotime($desde));
        $fechaActual = date("Y-m-d");
        $fechaActualFormat = date("d-m-Y");
  
        $getVar = file_get_contents("https://api.bcra.gob.ar/estadisticas/v1/DatosVariable/$varId/$desde/$fechaActual", false, stream_context_create($arrContextOptions));
        $var = json_decode($getVar);
        
        echo "<hr><h3 class='sm:text-xl text-lg font-semibold text-center pb-6 pt-10 sm:px-0 px-16'>Datos de $varString desde el $desdeFormat al $fechaActualFormat.</h3>";
  
        echo "<ul class='flex flex-col mt-8 gap-10 sm:pb-24 pb-16 px-20 sm:px-0 sm:flex-row sm:flex-wrap sm:justify-center'>";

        foreach ($var->results as $req) {
          echo "
            <li class='gradient bg-[#000] text-white p-7 rounded-lg shadow-md sm:w-[40%] px-8 sm:px-16 sm:py-16 text-center max-w-96'>
                <h3 class='sm:text-lg text-md font-semibold'>$req->fecha = <span class='font-bold'>$req->valor</span></h3>
            </li>";
        }

        echo "</ul>";
      }
    }

    ?>

    <hr class="sm:my-10 my-2">
    
    <!-- Principales variables -->
    <h1 class="text-center text-black sm:p-8 p-6 sm:text-4xl text-3xl font-bold">Principales Variables</h1>

    <ul class="flex flex-col mt-8 gap-10 pb-24 px-20 sm:px-0 sm:flex-row sm:flex-wrap sm:justify-center">

      <?php
        foreach ($vars->results as $var) {
          echo "<li class='bg-[#fafafa] p-7 rounded-lg shadow-md sm:w-[40%] px-8 sm:px-20 sm:text-start text-center max-w-96'>";
          echo "<h3 class='sm:text-lg text-md'>$var->descripcion - $var->fecha.</h3>";
          echo "<h2 class='sm:text-lg text-md font-bold sm:text-start text-center mt-5 pt-5 border-t'>$var->valor</h2>";
          echo "</li>";
        }
      ?>

    </ul>
  </main>
</body>

<style>
  .gradient {
    background-image: linear-gradient(to right top, #000000, #101010, #1a1a1a, #242424, #2e2e2e);
  }
  
  /* .gradient {
    background-image: linear-gradient(to right top, #d16ba5, #c777b9, #ba83ca, #aa8fd8, #9a9ae1, #8aa7ec, #79b3f4, #69bff8, #52cffe, #41dfff, #46eefa, #5ffbf1);
    color: #000;
  } */
</style>
