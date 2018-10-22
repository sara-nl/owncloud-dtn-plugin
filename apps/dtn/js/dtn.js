(function (OC, window, $) {
    'use strict';

    if (!window.OCA.DTN) {

        let _dtn = {
            transferFiles: function (files, title, comment, callback, modal, cssClass) {
                return $.when(this._transferFilesSettingsTemplate()).then(function ($tmpl) {
                    var dialogName = dialogId + '-' + OCdialogs.dialogsCounter + '-content';
                    var dialogId = dialogName;
                    var $dlg = $tmpl.octemplate({
                        dialog_name: dialogName,
                        title: title,
                        comment: comment
                    });

                    if (modal === undefined) {
                        modal = false;
                    }

                    $('body').append($dlg);
                    var buttonlist = [{
                            text: t('dtn', 'Transfer'),
                            click: function () {
                                if (callback !== undefined) {
                                    callback(dialogId);
                                }
//                                $('#' + dialogId).ocdialog('close');
                            }
                        },
                        {
                            text: t('core', 'Cancel'),
                            click: function () {
                                $(dialogId).ocdialog('close');
                                /* wait for response notification */
                            },
                            defaultButton: true
                        }];

                    $('#' + dialogId).ocdialog({
                        closeOnEscape: true,
                        modal: modal,
                        buttons: buttonlist
                    });

                    if (cssClass != undefined) {
                        $('#' + dialogId).addClass(cssClass);
                    }

                    OCdialogs.dialogsCounter++;
                })
                        .fail(function (status, error) {
                            // If the method is called while navigating away from
                            // the page, we still want to deliver the message.
                            if (status === 0) {
                                alert(title + ': ' + content);
                            } else {
                                alert(t('core', 'Error loading message template: {error}', {error: error}));
                            }
                        });
            },
            _transferFilesSettingsTemplate: function () {
                var defer = $.Deferred();
                if (!this.$transferFilesSettingsTemplate) {
                    var self = this;
                    $.get(OC.filePath('dtn', 'templates', 'transferFilesSettings.html'), function (tmpl) {
                        self.$transferFilesSettingsTemplate = $(tmpl);
                        defer.resolve(self.$transferFilesSettingsTemplate);
                    })
                            .fail(function (jqXHR, textStatus, errorThrown) {
                                defer.reject(jqXHR.status, errorThrown);
                            });
                } else {
                    defer.resolve(this.$transferFilesSettingsTemplate);
                }
                return defer.promise();
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
                console.log('Wanna transfer ? ' + OC.currentUser);
                /* find the files selected */
                let _files = $('#filestable .selected');
                let _filePaths = [];
                _files.each(function (i) {
                    if ($(this).attr('data-type') === 'file')
                        _filePaths.push($(this).attr('data-path') + '/' + $(this).attr('data-file'));
                });
                if (_filePaths.length > 0) {
                    let _comment = _filePaths.length < _files.length ? "ignoring directory selection" : "";
                    OCA.DTN.transferFiles([], 'DTN transfer', _comment,
                            function (dialogId) {
                                let _transferUrl = OC.generateUrl('/apps/dtn/transferfiles');
                                let _receiverDTNUID = $('#' + dialogId).find('input#receiverDTNUID').val();
                                $.ajax({url: _transferUrl, type: 'GET', data: {
                                        files: _filePaths,
                                        receiverDTNUID: _receiverDTNUID
                                    }})
                                        .done(function (data, textStatus, jqXHR) {
                                            console.log('success');
                                            console.log(data);
//                                        OC.Notification.show(textStatus, {type: 'info'});
                                        })
                                        .fail(function (jqXHR, textStatus, errorThrown) {
                                            console.log('fail');
                                            console.log(jqXHR);
//                                        OC.Notification.show(textStatus, {type: 'error'});
                                        }
                                        );
                            },
                            true, 'dtn-transfer-settings-dialog');
                }
            });
            /* File select handler to check file type selecttions for transfer button; we can not (yet) handle directories */
//            $('#filestable').load(function() {
////            $('input[type="checkbox"]').change(function() {
//                console.log('load');
////            });
//            });
        });
    }
})(OC, window, jQuery);