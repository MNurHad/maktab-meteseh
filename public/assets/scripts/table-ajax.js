var TableAjax = function () {
    var dataTable;
    var the;
    var ajaxParams = {};
    var customModal;

    var buttonConfirmAction = function (title) {
        var modal = $('#modalViewDetail');

        $(document).on('click', '.delete-item', function (ev) {
            ev.preventDefault();
            var href = $(this).data('href') || $(this).attr('href');
            swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4b7cf3',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'Yes, delete it!',
                showLoaderOnConfirm: true,
                allowOutsideClick: false,
                customClass: {
                    confirmButton: 'swal-confirm-gradient',
                },
                preConfirm: function () {
                    return new Promise(function (resolve) {
                        $.post(href, {_method: 'DELETE'})
                            .done(function () {
                                dataTable.ajax.reload();
                                swal.fire(
                                    'Deleted!',
                                    title + ' has been deleted.',
                                    'success'
                                );
                            })
                            .fail(function (xhr) {
                                var message = xhr.statusText;
                                if (xhr.responseJSON && xhr.responseJSON.hasOwnProperty('message')) {
                                    message = xhr.responseJSON.message;
                                }

                                swal.fire(
                                    'Oops...',
                                    message,
                                    'error'
                                );
                            });
                    });
                }
            });
        });

        $(document).on('click', '.resend-item', function (ev) {
            ev.preventDefault();
            var href = $(this).data('href') || $(this).attr('href');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4b7cf3',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'Yes, resend it!',
                showLoaderOnConfirm: true,
                allowOutsideClick: false,
                customClass: {
                    confirmButton: 'swal-confirm-gradient',
                },
                preConfirm: function () {
                    return new Promise(function (resolve) {
                        $.post(href, {_method: 'POST'})
                            .done(function () {
                                dataTable.ajax.reload();
                                swal.fire(
                                    'Resend Email!',
                                    title + ' Resend email has been successfully.',
                                    'success'
                                );
                            })
                            .fail(function (xhr) {
                                var message = xhr.statusText;
                                if (xhr.responseJSON && xhr.responseJSON.hasOwnProperty('message')) {
                                    message = xhr.responseJSON.message;
                                }

                                swal.fire(
                                    'Oops...',
                                    message,
                                    'error'
                                );
                            });
                    });
                }
            });
        });

        $(document).on('click', '.banned-item', function (ev) {
            ev.preventDefault();
            var href = $(this).data('href') || $(this).attr('href');
            swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to updated status this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4b7cf3',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'Yes, Update Status!',
                showLoaderOnConfirm: true,
                allowOutsideClick: false,
                customClass: {
                    confirmButton: 'swal-confirm-gradient',
                },
                preConfirm: function () {
                    return new Promise(function (resolve) {
                        $.post(href, {_method: 'PUT'})
                            .done(function () {
                                dataTable.ajax.reload();
                                swal.fire(
                                    'Confirmation!',
                                    title + ' updated has been successfully.',
                                    'success'
                                );
                            })
                            .fail(function (xhr) {
                                var message = xhr.statusText;
                                if (xhr.responseJSON && xhr.responseJSON.hasOwnProperty('message')) {
                                    message = xhr.responseJSON.message;
                                }

                                swal.fire(
                                    'Oops...',
                                    message,
                                    'error'
                                );
                            });
                    });
                }
            });
        });

        $(document).on('click', '.archive-item', function (ev) {
            ev.preventDefault();
            var href = $(this).data('href') || $(this).attr('href');
            swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to archive member this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4b7cf3',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'Yes, Archive it!',
                showLoaderOnConfirm: true,
                allowOutsideClick: false,
                customClass: {
                    confirmButton: 'swal-confirm-gradient',
                },
                preConfirm: function () {
                    return new Promise(function (resolve) {
                        $.post(href, {_method: 'PUT'})
                            .done(function () {
                                dataTable.ajax.reload();
                                swal.fire(
                                    'Confirmation!',
                                    title + ' Archived has been successfully.',
                                    'success'
                                );
                            })
                            .fail(function (xhr) {
                                var message = xhr.statusText;
                                if (xhr.responseJSON && xhr.responseJSON.hasOwnProperty('message')) {
                                    message = xhr.responseJSON.message;
                                }

                                swal.fire(
                                    'Oops...',
                                    message,
                                    'error'
                                );
                            });
                    });
                }
            });
        });

        $(document).on('click', '.recom-item', function (ev) {
            ev.preventDefault();
            var href = $(this).data('href') || $(this).attr('href');
            swal.fire({
                title: 'Are you sure?',
                text: "You won't be able recommendation product it!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4b7cf3',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'Yes, Recomend!',
                showLoaderOnConfirm: true,
                allowOutsideClick: false,
                customClass: {
                    confirmButton: 'swal-confirm-gradient',
                },
                preConfirm: function () {
                    return new Promise(function (resolve) {
                        $.post(href, {_method: 'PUT'})
                            .done(function () {
                                dataTable.ajax.reload();
                                swal.fire(
                                    'Confirmation!',
                                    title + ' Recommendation has been successfully.',
                                    'success'
                                );
                            })
                            .fail(function (xhr) {
                                var message = xhr.statusText;
                                if (xhr.responseJSON && xhr.responseJSON.hasOwnProperty('message')) {
                                    message = xhr.responseJSON.message;
                                }

                                swal.fire(
                                    'Oops...',
                                    message,
                                    'error'
                                );
                            });
                    });
                }
            });
        });

        $(document).on('click', '.unrecom-item', function (ev) {
            ev.preventDefault();
            var href = $(this).data('href') || $(this).attr('href');
            swal.fire({
                title: 'Are you sure?',
                text: "You won't be able delete recommendation product it!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4b7cf3',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'Yes, delete recomend!',
                showLoaderOnConfirm: true,
                allowOutsideClick: false,
                customClass: {
                    confirmButton: 'swal-confirm-gradient',
                },
                preConfirm: function () {
                    return new Promise(function (resolve) {
                        $.post(href, {_method: 'PUT'})
                            .done(function () {
                                dataTable.ajax.reload();
                                swal.fire(
                                    'Confirmation!',
                                    title + ' Recommendation delete has been successfully.',
                                    'success'
                                );
                            })
                            .fail(function (xhr) {
                                var message = xhr.statusText;
                                if (xhr.responseJSON && xhr.responseJSON.hasOwnProperty('message')) {
                                    message = xhr.responseJSON.message;
                                }

                                swal.fire(
                                    'Oops...',
                                    message,
                                    'error'
                                );
                            });
                    });
                }
            });
        });

        $(document).on('click', '.main-item', function (ev) {
            ev.preventDefault();
            var href = $(this).data('href') || $(this).attr('href');
            swal.fire({
                title: 'Are you sure?',
                text: "You won't be able recommendation show main product it!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4b7cf3',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'Yes, Show as main!',
                showLoaderOnConfirm: true,
                allowOutsideClick: false,
                customClass: {
                    confirmButton: 'swal-confirm-gradient',
                },
                preConfirm: function () {
                    return new Promise(function (resolve) {
                        $.post(href, {_method: 'PUT'})
                            .done(function () {
                                dataTable.ajax.reload();
                                swal.fire(
                                    'Confirmation!',
                                    title + ' Recommendation is main has been successfully.',
                                    'success'
                                );
                            })
                            .fail(function (xhr) {
                                var message = xhr.statusText;
                                if (xhr.responseJSON && xhr.responseJSON.hasOwnProperty('message')) {
                                    message = xhr.responseJSON.message;
                                }

                                swal.fire(
                                    'Oops...',
                                    message,
                                    'error'
                                );
                            });
                    });
                }
            });
        });

        $(document).on('click', '.approve-item', function (ev) {
            ev.preventDefault();
            var href = $(this).data('href') || $(this).attr('href');
            swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4b7cf3',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'Yes, Approve it!',
                showLoaderOnConfirm: true,
                allowOutsideClick: false,
                customClass: {
                    confirmButton: 'swal-confirm-gradient',
                },
                preConfirm: function () {
                    return new Promise(function (resolve) {
                        $.post(href, {_method: 'PUT'})
                            .done(function () {
                                dataTable.ajax.reload();
                                swal.fire(
                                    'Confirmation!',
                                    title + ' user approved has been successfully.',
                                    'success'
                                );
                            })
                            .fail(function (xhr) {
                                var message = xhr.statusText;
                                if (xhr.responseJSON && xhr.responseJSON.hasOwnProperty('message')) {
                                    message = xhr.responseJSON.message;
                                }

                                swal.fire(
                                    'Oops...',
                                    message,
                                    'error'
                                );
                            });
                    });
                }
            });
        });

        if (modal.length) {
            modal.on('show.bs.modal', function (e) {
                var button = $(e.relatedTarget);
                var json = button.data('json');
                var title = button.data('title');
                var self = $(this);
                self.find('#modalViewDetailTitle').text(title);
                self.find('#json').jsonViewer(json, {collapsed: false, withQuotes: false, withLinks: true, rootCollapsable: true});
            });
        }
    };

    var mergeOptions = function (url, options) {
        options = $.extend(true, {
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            ajax: {
                url: url || '',
                data: function (data) {
                    $.each(ajaxParams, function (key, value) {
                        data[key] = value;
                    });
                },
                error: function (jqXhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error ' + jqXhr.status,
                        text: jqXhr.responseJSON?.message || jqXhr.statusText,
                    });
                }
            }
        }, options);

        return options;
    };

    var refreshBtn = function () {
        var btnRefresh = document.getElementById('btn_refresh');
        if (btnRefresh) {
            btnRefresh.addEventListener('click', function (ev) {
                ev.preventDefault();
                the.resetFilter();
            });
        }
    };

    return {
        initWithAction: function (selector, url, options, titleDelete, modalCallback) {
            the = this;
            customModal = modalCallback;
            options = mergeOptions(url, options);
            dataTable = $(selector).DataTable(options);
            buttonConfirmAction(titleDelete);
            refreshBtn();
        },
        initWithoutAction: function (selector, url, options) {
            options = mergeOptions(url, options);
            dataTable = $(selector).DataTable(options);
            refreshBtn();
        },
        setAjaxParam: function (name, value) {
            ajaxParams[name] = value;
        },
        getDataTable: function () {
            return dataTable;
        },
        clearAjaxParams: function (name, value) {
            ajaxParams = {};
        },
        resetFilter: function () {
            the.clearAjaxParams();
            dataTable.ajax.reload();
        },
        submitFilter: function (field, value, type, user, types, status) {
            the.clearAjaxParams();
            if (field) the.setAjaxParam('filter_field', field);
            if (value) the.setAjaxParam('filter_value', value);
            if (type) the.setAjaxParam('filter_type', type);
            if (user) the.setAjaxParam('filter_user', user);
            if (types) the.setAjaxParam('filter_types', types);
            if (status) the.setAjaxParam('filter_status', status);
            dataTable.ajax.reload();
        }
    }
}();

var TableAjaxCustom = {
}
function customAjaxTbl() {
    var dataTable;
    var the;
    var ajaxParams = {};
    var customModal;

    var buttonConfirmAction = function (title) {
        var modal = $('#modalViewDetail');

        $(document).on('click', '.delete-item', function (ev) {
            ev.preventDefault();
            var href = $(this).data('href') || $(this).attr('href');
            swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4b7cf3',
                cancelButtonColor: '#dc3545',
                confirmButtonText: 'Yes, delete it!',
                showLoaderOnConfirm: true,
                allowOutsideClick: false,
                preConfirm: function () {
                    return new Promise(function (resolve) {
                        $.post(href, {_method: 'DELETE'})
                            .done(function () {
                                dataTable.ajax.reload();
                                swal.fire(
                                    'Deleted!',
                                    title + ' has been deleted.',
                                    'success'
                                );
                            })
                            .fail(function (xhr) {
                                var message = xhr.statusText;
                                if (xhr.responseJSON && xhr.responseJSON.hasOwnProperty('message')) {
                                    message = xhr.responseJSON.message;
                                }

                                swal.fire(
                                    'Oops...',
                                    message,
                                    'error'
                                );
                            });
                    });
                }
            });
        });

        if (modal.length) {
            modal.on('show.bs.modal', function (e) {
                var button = $(e.relatedTarget);
                var json = button.data('json');
                var title = button.data('title');
                var self = $(this);
                self.find('#modalViewDetailTitle').text(title);
                self.find('#json').jsonViewer(json, {collapsed: false, withQuotes: false, withLinks: true, rootCollapsable: true});
            });
        }
    };

    var mergeOptions = function (url, options) {
        options = $.extend(true, {
            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            ajax: {
                url: url || '',
                data: function (data) {
                    $.each(ajaxParams, function (key, value) {
                        data[key] = value;
                    });
                },
                error: function (jqXhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error ' + jqXhr.status,
                        text: jqXhr.responseJSON?.message || jqXhr.statusText,
                    });
                }
            }
        }, options);

        return options;
    };

    var refreshBtn = function (buttonRefresh = 'btn_custom_refresh') {
        var btnRefresh = document.getElementById(buttonRefresh);
        if (btnRefresh) {
            btnRefresh.addEventListener('click', function (ev) {
                ev.preventDefault();
                the.resetFilter();
            });
        }
    };

    return {
        initWithAction: function (selector, url, options, titleDelete, modalCallback) {
            the = this;
            customModal = modalCallback;
            options = mergeOptions(url, options);
            dataTable = $(selector).DataTable(options);
            buttonConfirmAction(titleDelete);
            refreshBtn();
        },
        initWithoutAction: function (selector, url, options, buttonRefresh) {
            the = this;
            options = mergeOptions(url, options);
            dataTable = $(selector).DataTable(options);
            refreshBtn(buttonRefresh);
        },
        setAjaxParam: function (name, value) {
            ajaxParams[name] = value;
        },
        getDataTable: function () {
            return dataTable;
        },
        clearAjaxParams: function (name, value) {
            ajaxParams = {};
        },
        resetFilter: function () {
            the.clearAjaxParams();
            dataTable.ajax.reload();
        },
        submitFilter: function (field, value, type, user, types, status) {
            the.clearAjaxParams();
            if (field) the.setAjaxParam('filter_field', field);
            if (value) the.setAjaxParam('filter_value', value);
            if (type) the.setAjaxParam('filter_type', type);
            if (user) the.setAjaxParam('filter_user', user);
            if (types) the.setAjaxParam('filter_types', types);
            if (status) the.setAjaxParam('filter_status', status);
            dataTable.ajax.reload();
        }
    }
};
