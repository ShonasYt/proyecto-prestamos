<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prestamos Colima</title>

    <link rel="stylesheet" href="{{ URL::asset('bootstrap-5.3.8-dist/css/bootstrap.min.css') }}">
    <script src="{{ URL::asset('bootstrap-5.3.8-dist/js/bootstrap.min.js') }}"></script>
    <link rel="stylesheet" href="{{ URL::asset('DataTables/datatables.min.css') }}">
    <script src="{{ URL::asset('DataTables/datatables.min.js') }}"></script> 

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600;9..40,700&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ URL::asset('assets/style.css') }}">
</head>
<body>
    <div class="row">
        <div class="col-2">
                @component("components.sidebar")
                @endcomponent
        </div>
        <div class="col-10">
            <div class="container">
                @section("content") 
                @show
            </div>
        </div>
    </div>
</body>
</html>