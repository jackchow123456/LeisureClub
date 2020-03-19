(function () {

    function Specific(warp) {
        this.class = warp;
        this.warp = $(warp);
        this.attrs = {};
        this.commonStock = 0; // 统一库存
        this.commonPrice = 0; // 统一价格1
        this.init();
    }

    Specific.prototype.init = function () {
        const _this = this;

        _this.warp.find('.add_box').click(function () {
            var html = _this.buildSpecificBoxHtml();
            $(this).before(html);
            _this.bindSelect2();
        });

        $(document).on('click', _this.class + ' .add', function (event) {
            if ($(this).parents('.box').find('select:last').val() == '') {
                layer.msg('请先选择规格，再添加规格值.', {icon: 0});
                return;
            }
            if (!event.isPropagationStopped()) {
                var html = _this.buildValueHtml();
                $(this).parents('.input-group').before(html);
            }
            event.stopPropagation();
        });

        $(document).on('blur', '.specific_value input', function () {
            _this.getAttr();
        });

        // 删除规格框
        $(document).on('click', _this.class + ' .remove-box', function () {
            $(this).parents('.box:first').slideUp(500, function () {
                $(this).remove();
                _this.getAttr();
            });
        });

        // 删除规格值
        $(document).on('click', _this.class + ' .remove', function () {
            $(this).parents('.input-group').remove();
            _this.getAttr();
        });

        // 绑定input变化事件
        _this.warp.find('.specific_table tbody').on('keyup', 'input', _this.processSku.bind(_this));

        // 统一价格
        _this.warp.find('.specific_table thead').on('keyup', 'input.Js_price', function () {
            _this.commonPrice = $(this).val();
            _this.warp.find('.specific_table tbody td[data-field="price"] input').val(_this.commonPrice);
            _this.processSku()
        });

        // 统一库存
        _this.warp.find('.specific_table thead').on('keyup', 'input.Js_stock', function () {
            _this.commonStock = $(this).val();
            _this.warp.find('.specific_table tbody td[data-field="stock"] input').val(_this.commonStock);
            _this.processSku()
        });

        // SKU图片上传
        _this.warp.find('.specific_table tbody').on('click', '.Js_sku_upload', function () {
            _this.upload($(this));
        });

        // SKU图片删除
        _this.warp.find('.specific_table tbody').on('click', '.remove', function () {
            $(this).parent('li').remove();
            console.log($('.sku-pictures').viewer('destroy'));
            console.log($('.sku-pictures').viewer().data('viewer').update());
            _this.processSku();
        });

        let old_val = _this.warp.find('.Js_sku_input').val();

        if (old_val) {
            // 根据值生成DOM
            old_val = JSON.parse(old_val);
            console.log(old_val);

            $.each(old_val.attrs, function (i, v) {
                _this.warp.find('.add_box').trigger('click');
                _this.warp.find('.box.box-default.box-solid').last().find('select').append('<option value="1" selected="selected">' + i + '</option>');
                var boxBody = _this.warp.find('.box.box-default.box-solid').last().find('.box-body:last');
                $.each(v, function (it, vt) {
                    var html = _this.buildValueHtmlWithItem({'name': vt});
                    boxBody.prepend(html);
                });
            });

            _this.attrs = old_val.attrs;
            _this.buildForm(old_val.sku);

        } else {
            _this.processSku()
        }

    };


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
                    if (!e.isPropagationStopped()) {
                        var boxBody = $(e.delegateTarget).parents('.box:first').find('.box-body:last');
                        var values = boxBody.children('.input-group');
                        if (values.length > 1) {
                            layer.confirm('你确定切换规格名吗？下面的规格值将会被全部替代的哦.', function (a, b, c) {
                                boxBody.find('.input-group:last').prevAll('.input-group').remove();
                                if (data.values.length > 0) {
                                    for (item in data.values) {
                                        var html = _this.buildValueHtmlWithItem(data.values[item]);
                                        boxBody.prepend(html);
                                    }
                                }
                                layer.close(a);
                            })
                        } else {
                            if (data.values.length > 0) {
                                for (item in data.values) {
                                    var html = _this.buildValueHtmlWithItem(data.values[item]);
                                    boxBody.prepend(html);
                                }
                            }
                        }
                    }
                }
                _this.getAttr();
            }
            e.stopPropagation();
        });
    };

    Specific.prototype.upload = function (that) {
        let _this = this;
        that.jacktree({
            "target": '.specific_table',
            "callback": function (result) {
                $.each(result, function (i, v) {
                    that.parent('li').before('<li data-field-pic="' + v.image_path + '"><img data-original="' + v.image_uri + '" src="' + v.image_uri + '"><a class="remove" href="javascript:;">删除</a></li>');
                })
                $('.sku-pictures').viewer('destroy');
                $('.sku-pictures').viewer();
                _this.processSku();
            }
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
            '                        <button type="button" class="btn btn-box-tool remove-box"><i class="fa fa-times"></i>\n' +
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

    Specific.prototype.getAttr = function () {
        let attr = {}; // 所有属性
        let _this = this;
        let trs = _this.warp.find('.box.box-default.box-solid');

        trs.each(function () {
            let tr = $(this);
            let attr_name = tr.find('.box-header .select2-selection__rendered').text(); // 属性名
            let attr_val = []; // 属性值
            if (attr_name) {
                // 获取对应的属性值
                tr.children('.box-body').find('input').each(function () {
                    let ipt_val = $(this).val();
                    if (ipt_val) {
                        attr_val.push(ipt_val)
                    }
                });
            }
            if (attr_val.length) {
                attr[attr_name] = attr_val;
            }
        });

        if (JSON.stringify(_this.attrs) !== JSON.stringify(attr)) {
            _this.attrs = attr;
            _this.buildForm()
        }
    };

    // 生成具体的SKU配置表单
    Specific.prototype.buildForm = function (default_sku) {
        console.log(default_sku);
        let _this = this;
        let attr_names = Object.keys(_this.attrs);
        if (attr_names.length == 0) {
            _this.warp.find('.specific_table tbody').html(' ');
            _this.warp.find('.specific_table thead').html(' ');
        } else {
            // 渲染表头
            let thead_html = '<tr>';
            attr_names.forEach(function (attr_name) {
                thead_html += '<th>' + attr_name + '</th>'
            });
            thead_html += '<th style="width: 100px">图片</th>';
            thead_html += '<th style="width: 100px">价格 <input value="' + _this.commonPrice + '" type="text" style="width: 50px" class="Js_price"></th>';
            thead_html += '<th style="width: 100px">库存 <input value="' + _this.commonStock + '" type="text" style="width: 50px" class="Js_stock"></th>';
            thead_html += '</tr>';
            _this.warp.find('.specific_table thead').html(thead_html);
            // 求笛卡尔积
            let cartesianProductOf = (function () {
                return Array.prototype.reduce.call(arguments, function (a, b) {
                    var ret = [];
                    a.forEach(function (a) {
                        b.forEach(function (b) {
                            ret.push(a.concat([b]));
                        });
                    });
                    return ret;
                }, [[]]);
            })(...Object.values(_this.attrs));

            // 根据计算的笛卡尔积渲染tbody
            let tbody_html = '';
            cartesianProductOf.forEach(function (sku_item) {
                tbody_html += '<tr>';
                sku_item.forEach(function (attr_val, index) {
                    let attr_name = attr_names[index];
                    tbody_html += '<td data-field="' + attr_name + '">' + attr_val + '</td>';
                });
                tbody_html += '<td class="td_pic" data-field="pic">' +
                    '<div class="sku-galley mb-3">\n' +
                    '          <ul class="sku-pictures">\n' +
                    '            <li><input value="" type="hidden" class="form-control"><span class="Js_sku_upload">+</span></li>&nbsp;&nbsp;\n' +
                    '          </ul>\n' +
                    '        </div>'
                '<input value="" type="hidden" class="form-control"><span class="Js_sku_upload">+</span>' +
                '</td>';
                tbody_html += '<td data-field="price"><input value="' + _this.commonPrice + '" type="text" class="form-control"></td>';
                tbody_html += '<td data-field="stock"><input value="' + _this.commonStock + '" type="text" class="form-control"></td>';
                tbody_html += '</tr>'
            });
            _this.warp.find('.specific_table tbody').html(tbody_html);


            if (default_sku) {
                // 填充数据
                default_sku.forEach(function (item_sku, index) {

                    let tr = _this.warp.find('.specific_table tbody tr').eq(index);
                    console.log(Object.keys(item_sku));
                    Object.keys(item_sku).forEach(function (field) {
                        let input = tr.find('td[data-field="' + field + '"] input');
                        if (input.length) {
                            input.val(item_sku[field]);
                            let sku_upload = tr.find('td[data-field="' + field + '"] .Js_sku_upload');
                            if (sku_upload.length && item_sku.pic.length > 0) {

                                $.each(item_sku.pic, function (i, v) {
                                    sku_upload.parent('li').before('<li data-field-pic="' + v.path + '"><img data-original="' + v.image_uri + '" src="' + v.image_uri + '"><a class="remove" href="javascript:;">删除</a></li>');
                                });
                                $('.sku-pictures').viewer('destroy');
                                $('.sku-pictures').viewer();
                                _this.processSku();
                            }
                        }
                    })
                });
            }
        }
        $('.specific_detail').show();
        _this.processSku()
    };

    // 处理最终SKU数据，并写入input
    Specific.prototype.processSku = function () {
        let _this = this;
        let sku_json = {};
        sku_json.type = _this.warp.find('.sku_attr_select .btn.btn-success').attr('data-type');
        // 多规格
        sku_json.attrs = _this.attrs;
        let sku = [];
        _this.warp.find('.specific_table tbody tr').each(function () {
            let tr = $(this);
            let item_sku = {};
            tr.find('td[data-field]').each(function () {
                let td = $(this);
                let field = td.attr('data-field');
                let input = td.find('input');
                if (input.length) {
                    item_sku[field] = input.val();
                } else {
                    item_sku[field] = td.text();
                }
            });

            var imagePath = [];
            tr.find('td[data-field="pic"]').find('li[data-field-pic]').each(function (i, v) {
                if ($(v).data('field-pic')) {
                    imagePath.push($(v).data('field-pic'));
                }
            });
            item_sku['pic'] = imagePath;

            sku.push(item_sku);
        });
        sku_json.sku = sku;
        _this.warp.find('.Js_sku_input').val(JSON.stringify(sku_json));
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

    Specific.prototype.buildValueHtmlWithItem = function (item) {
        var html = '<div class="input-group col-sm-3 specific_value">\n' +
            '                        <div class="input-group-btn">\n' +
            '                            <button type="button" class="btn btn-info" style="color: #FFFFFF"> 规格值</button>\n' +
            '                        </div>\n' +
            '                        <!-- /btn-group -->\n' +
            '                        <input class="form-control" type="text" value="' + item.name + '">\n' +
            '\n' +
            '                        <div class="input-group-btn">\n' +
            '                            <button type="button" class="btn btn-danger remove"><i class="fa fa-times"></i></button>\n' +
            '                        </div>\n' +
            '                    </div>';
        return html;
    };


    jQuery.fn.addLoading = function () {
        $(this).parents('.box').children('.box-body').after('<div class="loading">\n' +
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

    window.JackChowSpecific = Specific;
})();

