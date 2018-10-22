(function (OC, window, $) {
    'use strict';

    if (!window.OCA.DTN) {

        let _dtn = {
            transferFiles: function (event) {
                console.log('go transfer ... ' + OC.currentUser);
                let _transferUrl = OC.generateUrl('/apps/dtn/transferfiles');
                $.ajax({url: _transferUrl, type: 'GET', data: {files: 'the files'}})
                        .done(function (data, textStatus, jqXHR) {
                            console.log('success');
                            console.log(data);
                            OC.Notification.show(textStatus, {type: 'info'});
                        })
                        .fail(function (jqXHR, textStatus, errorThrown) {
                            console.log('fail');
                            console.log(data);
                            OC.Notification.show(textStatus, {type: 'error'});
                        }
                        );

            }
        };

        /**
         * Namespace for the DTN app
         * @namespace OCA.DTN
         */
        window.OCA.DTN = _dtn;

        /* Initialize the DTN transfer button */
        $(document).ready(function () {
            $('#selectedActionsList').append('<a href="" class="transfer"><span class="icon icon-transfer"></span>DTN transfer<span></span></a>');
            $('#selectedActionsList a.transfer').click(function (e) {
                e.preventDefault();
                e.stopPropagation();
                OCA.DTN.transferFiles(e);
            });
        });
    }
})(OC, window, jQuery);