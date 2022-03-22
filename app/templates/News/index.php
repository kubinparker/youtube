<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    Hello! WebSocket

    <script src="https://cdn.socket.io/socket.io-1.4.5.js"></script>
    <script>
        var connection = new WebSocket('ws://127.0.0.1:1992');
        connection.onopen = function() {
            alert("Kết nối thành công");
        }
        connection.onerror = function(error) {
            console.log('Lỗi' + JSON.stringify(error));
        }
    </script>

</body>

</html>