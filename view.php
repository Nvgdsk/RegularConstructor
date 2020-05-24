<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</head>
<style>
    .sameH {
        height: 500px;
    }

</style>
<body>
<div class="row">
    <div class="col s8">
        <div class="card ">
            <div class="card-content">

                <span class="card-title">Ваши логи</span>
                <div class="row">
                    <div class="input-field col s12">
                        <input type="text" id="regInput" rows="100">
                        <label for="regInput">Ваше выражение</label>
                    </div>
                </div>


            </div>
        </div>
        <div class="card ">
            <div class="card-content">

                <form action="">
                    <div class="row">
                        <form class="col s12">
                            <div class="row">
                                <div class="input-field col s12">
                                    <textarea id="textInput" class="materialize-textarea"></textarea>
                                    <label for="textarea1">Вставьте ваши логи</label>
                                </div>
                            </div>
                        </form>
                    </div>
                </form>
            </div>
            <div class="card-action">
                <a href="#" class="btn green right" id="btn_Generate">Сгенерировать регулярные выражения</a>
            </div>
        </div>
        <div class="card">
            <div class="card-content">
                <span class="card-title">Регулярные выражения</span>


                <div class="row">
                    <div id="answer"></div>

                </div>

            </div>


        </div>
    </div>
    <div class="col s4">
        <div class="card sameH">
            <div class="card-content">
                <span class="card-title">Совпадения</span>


                <div class="row" style="    height: 400px;
    overflow-y: scroll;">


                    <table>


                        <tbody id="regularMatch">


                        </tbody>
                    </table>


                </div>

            </div>
        </div>
    </div>
</div>


</div>


<div class="result">
    <div class="card">

    </div>
</div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>

<script>
    $('#btn_Generate').on('click', function () {
        let text = $('#textInput').val();
        $.ajax({
            type: 'POST',
            data: {text: text, a: "generateRegular"},
            url: 'trainingAlgorithm.php?',
            success: function (data) {


                console.clear();
                console.log(data);
                $("#answer").text("");
                let arr = JSON.parse(data);
                for (i in arr) {

                    $("#answer").append("" +
                        "<div class=\"row\">\n" +
                        "          <textarea id=\"textarea1\" >" + arr[i] + "</textarea>\n" +
                        "      </div>")

                }
            }
        });
    })
    $("#regInput").on('input', function () {
        let regular = $('#regInput').val();
        let text = $('#textInput').val();
        $.ajax({
            type: 'POST',
            data: {text: text, reg: regular, a: "getMatches"},
            url: 'trainingAlgorithm.php?',
            success: function (data) {
                console.clear();
                console.log(data);
                let answer = $("#regularMatch");
                answer.text("");

                let arr = JSON.parse(data);
                for (i in arr) {
                    for (j in arr[i])
                        answer.append(" <tr><td>" + arr[i][j] + "</td></tr>");
                }
            }
        });
    });
    $("#textInput").on('input', function () {
        let regular = $('#regInput').val();
        let text = $('#textInput').val();
        $.ajax({
            type: 'POST',
            data: {text: text, reg: regular, a: "getMatches"},
            url: 'trainingAlgorithm.php?',
            success: function (data) {
                console.clear();
                console.log(data);
                let answer = $("#regularMatch");
                answer.text("");

                let arr = JSON.parse(data);
                for (i in arr) {
                    for (j in arr[i])
                        answer.append(" <tr><td>" + arr[i][j] + "</td></tr>");
                }
            }
        });
    })
</script>
</html>