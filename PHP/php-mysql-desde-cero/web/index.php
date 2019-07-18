<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>MySQL con PHP</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="bg-success text-white text-center m-1">
                    <div class="card-header">Total vendidos</div>
                    <div class="card-body">
                        <h5 class="h1 card-title"><span id="idVendidos">35</span></h5>
                        <p class="card-text">Baja en las ventas vs mes anterior</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bg-warning text-white text-center m-1">
                    <div class="card-header">Total en almacen</div>
                        <div class="card-body">
                        <h5 class="h1 card-title"><span id="idAlmacen">35</span></h5>
                        <p class="card-text">Inventario mayor vs el mes pasado.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bg-info text-white text-center m-1">
                    <div class="card-header">Total Ingresos</div>
                        <div class="card-body">
                        <h5 class="h1 card-title"><span id="idIngreso">35</span></h5>
                        <p class="card-text">Disminución de ingresos vs mes anterior.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row my-3">
            <div class="col-md-12 text-center">
                <h2>Reporte de ventas</h2>
                <canvas id="idGrafica" class="grafica"></canvas>
            </div>
        </div>
        <div class="row  my-3">
            <div class="col-md-12 text-center">
                <div id="idContTabla"></div>
            </div>
        </div>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    <script src="js/index.js"></script>
</body>
</html>
