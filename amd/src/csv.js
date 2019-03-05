define(
    [],
    function() {
        var csv_string_to_file_for_download = function (csv_string, download_file_name) {
            var csvContent = "data:text/csv;charset=utf-8," + csv_string;
            var encodedUri = encodeURI(csvContent);
            var link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", download_file_name);
            document.body.appendChild(link); // Required for FF
            link.click();
        };
        return {
            csv_string_to_file_for_download: csv_string_to_file_for_download
        };
    }
);