        window.onload = function()
        {
            document.getElementById("add-ssh").addEventListener("change", OnFileSelect);
        }

        function OnFileSelect(e)
        {
            var keys = document.getElementById("connect-new-user-ssh-entry");
            keys.value = "";
            //fancy way to get 'this' that works with IE 8 and below
            e = e || window.event;
            var target = e.target || e.srcElement;
            //list of files selected
            const fileList = target.files;
            var reader = new FileReader();
            reader.onload = function(event) {
                var contents = event.target.result;
                keys.value = keys.value + contents + "\n";
            };
            reader.onerror = function (event) {};
            //For each file
            for (var i = 0; i < fileList.length; i++)
            {
                reader.readAsText(fileList[i]);
            }
        }

