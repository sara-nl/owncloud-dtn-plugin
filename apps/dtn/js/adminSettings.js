/**
 * Copyright 2018 SURFsara (http://www.surfsara.nl)
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 * http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
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