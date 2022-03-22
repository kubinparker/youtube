<!DOCTYPE html>
<html>

<head>

    <script type="text/javascript">
        var windowObjectReference = null; // global variable
        var access_token = null; // global variable

        function openFFPromotionPopup() {
            if (windowObjectReference == null || windowObjectReference.closed)
            /* if the pointer to the window object in memory does not exist
               or if such pointer exists but the window was closed */

            {
                windowObjectReference = window.open(
                    "https://gmo29.caters.jp/author1",
                    "PromoteFirefoxWindowName",
                    "popup,width=520,height=1000"
                );

                aaa = setInterval(function() {
                    if (windowObjectReference.hasOwnProperty('access_token')) {
                        access_token = windowObjectReference.access_token;
                        console.log(access_token);
                        clear_interval()
                    }
                }, 1000);

                function clear_interval() {
                    if (access_token) {
                        clearInterval(aaa);
                        windowObjectReference.close();
                        getCalendar(access_token);
                    }
                }

                /* then create it. The new window will be created and
                   will be brought on top of any other window. */
            } else {
                windowObjectReference.focus();
                /* else the window reference must exist and the window
                   is not closed; therefore, we can bring it back on top of any other
                   window with the focus() method. There would be no need to re-create
                   the window or to reload the referenced resource. */
            };
        }

        function getCalendar(token) {
            var oReq = new XMLHttpRequest(),
                url = 'https://graph.microsoft.com/v1.0/me/events';


            oReq.onreadystatechange = function() {
                if (oReq.readyState === 4) {
                    renderHTML(JSON.parse(oReq.response));
                }
            }

            oReq.open("GET", url);
            oReq.setRequestHeader('Authorization', 'Bearer ' + token);
            oReq.send();
        }


        function renderHTML(e) {
            console.log(e.value);
            var div = document.getElementById("list");
            var ul = document.createElement('ul');
            for (var i = 0; i < e.value.length; i++) {
                var li = document.createElement('li');
                li.innerHTML = e.value[i].subject;
                ul.appendChild(li);
            }
            div.append(ul);
        }
    </script>

</head>

<body>
    <p><a target="PromoteFirefoxWindowName" onclick="openFFPromotionPopup(); return false;" title="This link will create a new window or will re-use an already opened one">Get List Event</a></p>
    <div id='list'>
        List event </br>
    </div>
</body>

</html>