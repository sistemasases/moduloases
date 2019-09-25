define(
    [],
    function() {
        var downloadFile = function(fileName, urlData) {

            var aLink = document.createElement('a');
            var evt = document.createEvent("HTMLEvents");
            evt.initEvent("click");
            aLink.download = fileName;
            aLink.href = urlData;
            document.body.appendChild(aLink);
            aLink.click();
        };
        var csv_string_to_file_for_download = function (csv_string, download_file_name) {
            var csvContent = "data:text/csv;charset=UTF-8,"  + encodeURIComponent(csv_string);
            downloadFile(download_file_name, csvContent);
        };
        return {
            csv_string_to_file_for_download: csv_string_to_file_for_download
        };
    }
);