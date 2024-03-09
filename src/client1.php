<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Client</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #c7d0f8;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }
        #counter-container {
            text-align: center;
            margin: 20px;
        }
        #count { font-size: 2em; margin: 0.5em; }
        button {
            width: 150px; /* Ajusta este valor según prefieras */
        }
        #alertPlaceholder {
            position: fixed;
            top: 10px;
            right: 10px;
        }
    </style>
</head>
<body>
<div id="alertPlaceholder"></div>

<div class="counter-wrapper">
    <h3>Cliente # 1</h3>
    <input type="text" id="count" class="form-control" readonly value="0">
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        var wsUri = "ws://147.182.226.77:8081/";
        var websocket = new WebSocket(wsUri);

        websocket.onmessage = function(ev) {
            var msg = JSON.parse(ev.data);
            if(msg.type === 'user') {
                $('#count').val(msg.message);
            } else if(msg.type === 'system') {
                showAlert(msg.message);
            }
        };

        websocket.onerror = function(ev) {
            console.log('Error occurred: ', ev.data);
        };
    });

    function showAlert(message) {
        var alertPlaceholder = $('#alertPlaceholder');
        var wrapper = $('<div class="alert alert-success alert-dismissible" role="alert"></div>');
        wrapper.append(message);
        wrapper.append('<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>');
        alertPlaceholder.append(wrapper);
    }
</script>
</body>
</html>
