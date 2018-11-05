/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

(function (OC, window, $) {
    'use strict';

    $(document).ready(function () {
        $('#dtnPluginAdminSettings input[type="text"').on('change', function (event) {
            let _key = $(event.target).attr('name');
            let _value = $(event.target).val();
            let _setUrl = OC.generateUrl('/apps/dtn/dtnsettings/admin/' + _key);
            var _success = false;
            $('#dtnPluginAdminSettings .setting.' + _key + ' .notification').removeClass('ok');
            $.ajax({url: _setUrl, type: 'POST', data: {
                    value: _value
                }}).done(function (data, textStatus, jqXHR) {
                console.log('success');
                _success = true;
                $('#dtnPluginAdminSettings .setting.' + _key + ' .notification span').text('saved');
                $('#dtnPluginAdminSettings .setting.' + _key + ' .notification').addClass('ok');
            }).fail(function (jqXHR, textStatus, errorThrown) {
                console.log('fail');
//                console.log(jqXHR);
                console.log('Failed, success: ' + _success);
                $('#dtnPluginAdminSettings .setting.' + _key + '.notification span').text('error');
            }).always(function () {
                console.log('success: ' + _success);
            });
        });
    });
})(OC, window, jQuery);