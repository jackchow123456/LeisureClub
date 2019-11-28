(function () {


    function Specific(warp) {
        this.class = warp;
        this.warp = $(warp);
        this.attrs = {};
        this.commonStock = 0; // 统一库存
        this.commonPrice = 0; // 统一价格
        this.init();
    }

    Specific.prototype.bindSelect2 = function () {
        const _this = this;

        const select2 = _this.warp.find('.select2').select2({
            language: "zh-CN",
            tags: true,
            placeholder: '请选择你的规格',
            createTag: function (params) {
                var term = $.trim(params.term);

                if (term === '') {
                    return null;
                }

                return {
                    id: '-' + term,
                    text: term,
                    newTag: true // add additional parameters
                }
            },
            ajax: {
                url: "/api/admin/store/goods/getGoodsAttr",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        name: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {
                    // parse the results into the format expected by Select2
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data, except to indicate that infinite
                    // scrolling can be used
                    params.page = params.page || 1;
                    var selected = $.map(_this.warp.find('select.select2'), function (d) {
                        return $(d).val();
                    });
                    console.log(selected);
                    return {
                        results: $.map(data.data, function (d) {
                            d.id = d.id;
                            d.text = d.name;
                            d.disabled = (selected.indexOf(d.id.toString()) === -1) ? false : true;
                            return d;
                        }),
                        pagination: {
                            more: (params.page * 30) < data.meta.total
                        }
                    };
                },
                cache: true
            }
        });

        select2.on('select2:select', function (e) {
            var data = e.params.data;

            if (!e.isPropagationStopped()) {
                if (data.newTag == true) {
                    layer.msg('正在创建新的规格...', {scrollbar: false, shade: 0.3, time: 80000});

                    $.ajax({
                        url: '/api/admin/store/goods/createGoodsAttr',
                        method: 'POST',
                        data: {'name': data.text},
                        success: function (result) {
                            $(e.delegateTarget).find("option[value='" + data.id + "']").remove();
                            var newOption = new Option(result.data.name, result.data.id, true, true);
                            // Append it to the select
                            $(e.delegateTarget).append(newOption).trigger('change');
                            layer.msg('创建成功.', {scrollbar: false, shade: 0.3, time: 3000, icon: 1});
                        },
                        error: function (xhr) {
                            if ($(e.delegateTarget).val() instanceof Array) {
                                var vaild_data = $.map($(e.delegateTarget).val(), function (d) {
                                    if (data.id == d) {
                                        return;
                                    }
                                    return d;
                                });
                                $(e.delegateTarget).val(vaild_data).trigger("change");
                            } else {
                                $(e.delegateTarget).val('').trigger("change");
                            }
                            layer.msg('创建失败,网络异常.', {
                                scrollbar: false,
                                shade: 0.3,
                                time: 3000,
                                icon: 2
                            }, function (index) {
                                layer.closeAll();
                            });
                        }
                    });
                } else {

                }
            }

            e.stopPropagation();
        });


        // select2.on('select2:opening', function (e) {
        //     $.map(_this.warp.find('select.select2'), function (d) {
        //         let val = $(d).val();
        //         console.log($(e.delegateTarget))
        //         console.log($(e.delegateTarget).find("option[value='" + val + "']"))
        //         $(e.delegateTarget).find("option[value='" + val + "']").prop('disabled', true);
        //     });
        // });
    };

    Specific.prototype.init = function () {
        const _this = this;

        _this.warp.find('.add_box').click(function () {
            var html = _this.buildSpecificBoxHtml();
            $(this).before(html);
            _this.bindSelect2();
        });


        $(document).on('click', _this.class + ' .add', function () {
            var html = _this.buildValueHtml();
            $(this).parents('.input-group').before(html);
            _this.bindSelect2();
        });

        $(document).on('click', _this.class + ' .remove', function () {
            $(this).parents('.input-group').remove();
        });
    };

    Specific.prototype.buildSpecificBoxHtml = function () {
        var html = '<div class="box box-default box-solid">\n' +
            '                <div class="box-header with-border">\n' +
            '                    <div class="input-group">\n' +
            '                        <div class="input-group-btn">\n' +
            '                            <button type="button" class="btn btn-primary" style="color: #FFFFFF"> 规格名</button>\n' +
            '                        </div>\n' +
            '                        <!-- /btn-group -->\n' +
            '                        <select class="form-control select2"  style="width: 20%;display: inline-block;">\n' +
            '                            <option></option>\n' +
            '                        </select>\n' +
            '                    </div>\n' +
            '\n' +
            '                    <div class="box-tools pull-right">\n' +
            '                        <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>\n' +
            '                        </button>\n' +
            '                    </div>\n' +
            '                    <!-- /.box-tools -->\n' +
            '                </div>\n' +
            '                <!-- /.box-header -->\n' +
            '                <div class="box-body" style="display: block;">\n' +

            '                    <div class="input-group col-sm-2 specific_value">\n' +
            '                        <div class="input-group-btn">\n' +
            '                            <button type="button" class="btn btn-success add">\n' +
            '                                <i class="fa fa-plus" aria-hidden="true"></i>\n' +
            '                                &nbsp;添加规格值\n' +
            '                            </button>\n' +
            '                        </div>\n' +
            '                    </div>\n' +
            '                </div>\n' +
            '                <!-- /.box-body -->\n' +
            '            </div>';

        return html;
    };


    Specific.prototype.buildValueHtml = function () {
        var html = '<div class="input-group col-sm-3 specific_value">\n' +
            '                        <div class="input-group-btn">\n' +
            '                            <button type="button" class="btn btn-info" style="color: #FFFFFF"> 规格值</button>\n' +
            '                        </div>\n' +
            '                        <!-- /btn-group -->\n' +
            '                        <input class="form-control" type="text" value="">\n' +
            '\n' +
            '                        <div class="input-group-btn">\n' +
            '                            <button type="button" class="btn btn-danger remove"><i class="fa fa-times"></i></button>\n' +
            '                        </div>\n' +
            '                    </div>';
        return html;
    };


    window.JackChowSpecific = Specific;

    jQuery.fn.addLoading = function () {
        $(this).after('<div class="loading">\n' +
            '            <div class="spinner">\n' +
            '                <div class="rect1"></div>\n' +
            '                <div class="rect2"></div>\n' +
            '                <div class="rect3"></div>\n' +
            '                <div class="rect4"></div>\n' +
            '                <div class="rect5"></div>\n' +
            '            </div>\n' +
            '        </div>');
    };

    jQuery.fn.removeLoading = function () {
        $(this).parent().find('.loading').remove();
    };

})();

