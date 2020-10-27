<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Card Game</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">

        <!-- Styles -->


        <style>
            body {
                font-family: 'Lato';
            }
        </style>
    </head>

    <body>

        <div class="main">

            <h1>{{$data->message}}</h1>

            <div class="heading">
                <p>Health: {{$data->health}}</p>
                <p>Score: {{$data->score}}</p>
            </div>

            <div class="mid-wrapper">


                <div class="lower">
                    @if($data->health > 0)

                        <a href="{{ route('lower') }}"><i class="far fa-arrow-alt-circle-down"></i></a>


                    @endif
                </div>

                <div class="card">

                    <div class="card-top">
                        <p>{{session('currentCard')['value']}}</p>
                    </div>

                    <div class="card-mid">
                        <img src="img/{{session('currentCard')['suit']}}.png" alt="">
                    </div>

                    <div class="card-bottom">
                        <p>{{session('currentCard')['value']}}</p>
                    </div>

                </div>

                <div class="higher">
                    @if($data->health > 0)

                        <a href="{{ route('higher') }}"><i class="far fa-arrow-alt-circle-up"></i></a>


                    @endif
                </div>


            </div>








            <div class="footer">
                <a href="{{ route('index') }}" class="btn btn-primary btn-submit btn-restart">Restart</a>
            </div>


        </div>



    </body>

    <script src="https://kit.fontawesome.com/d303127937.js" crossorigin="anonymous"></script>
</html>
