$.fn.jacktree = function (arg) {
    // 使用默认参数初始化插件
    var instance_counter = 0;
    var currentOrder = 'asc';
    var currentType = 'grid';
    var currentSort = 'filename';
    var currentPath = '/';
    var DirectoryRoute = arg.DirectoryRoute;
    var ListFoldersRoute = arg.ListFoldersRoute;
    var FilesRoute = arg.FilesRoute;
    var CreateRoute = arg.CreateRoute;
    var UploadRoute = arg.UploadRoute;
    var DeleteRoute = arg.DeleteRoute;
    var MoveRoute = arg.MoveRoute;
    var CopyRoute = arg.CopyRoute;
    var RenameRoute = arg.RenameRoute;
    var FoldersRoute = arg.FoldersRoute;


    var initHtml = '<!--文件模态框（Modal）--><div class="modal fade"id="fileModel"tabindex="-1"role="dialog"aria-labelledby="myModalLabel"data-backdrop="static"aria-hidden><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header"><button type="button"class="close"data-dismiss="modal"aria-hidden="true">×</button><h4 class="modal-title"id="fileModalLabel">文件管理</h4></div><div class="modal-body"><div id="container"><div id="menu"><a tabindex="500"id="create"class="btn btn-sm btn-success"><i class="fa fa-folder"></i>    <span>新建文件夹</span></a><a tabindex="500"id="upload"class="btn btn-sm btn-warning"><i class="fa fa-upload"></i>    <span>上传</span></a><a tabindex="500"id="delete"class="btn btn-sm btn-danger"><i class="fa fa-trash-o"></i>    <span>删除</span></a><a tabindex="500"id="move"class="btn btn-sm btn-primary"><i class="fa fa-hand-o-right"></i>    <span>移动</span></a><a tabindex="500"id="copy"class="btn btn-sm btn-info"><i class="fa fa-copy"></i>    <span>复制</span></a><a tabindex="500"id="rename"class="btn btn-sm btn-warning"><i class="fa fa-tag"></i>    <span>重命名</span></a><a tabindex="500"id="refresh"class="btn btn-sm btn-default"><i class="fa fa-refresh"></i>    <span>刷新</span></a></div><div style="clear:both;"></div><div id="column-left"></div><div id="column-right"><div class="topDiv"><div class="sortDiv"><a class="asc"id="sortName">按文件名排序</a><a id="sortSize">按文件大小排序</a><a id="sortTime">按添加时间排序</a></div><div class="typeDiv"><a class="active"id="grid">网格</a><a id="list">列表</a></div></div><div class="fileList"></div></div></div></div><div class="modal-footer"><button type="button"class="btn btn-default"data-dismiss="modal">关闭</button><button type="button"class="btn btn-primary confirmBtn">提交更改</button></div></div></div></div>'

    if (this.next()[0].id !== 'fileModel') {
        this.after(initHtml);
    }

    //左侧文件请求
    $('#column-left').off('changed.jstree');
    $('#column-left')
        .on("changed.jstree", function (e, data) {
            if (!data) {
                getFiles('/');
            } else {
                if (data.instance.get_node(data.selected[0]) !== false) {
                    currentPath = data.instance.get_node(data.selected[0]).original.directory;
                    console.log('The selected node is: ' + data.instance.get_node(data.selected[0]).text);
                    getFiles(data.instance.get_node(data.selected[0]).original.directory);
                }
            }
        })
        .jstree({
            'core': {
                'data': {
                    "url": ListFoldersRoute,
                    "data": function (node) {
                        return {'id': node.id};
                    },
                    // 自定义返回结构
                    "dataFilter": function (data, type) {
                        function _changeParam($d) {
                            if (!$d) return $d;
                            var $r = [];
                            $.each($d, function (i, v) {
                                if (v.children) {
                                    $r.push({
                                        "text": v.data,
                                        "children": _changeParam(v.children),
                                        'directory': v.attributes.directory,
                                    });
                                } else {
                                    $r.push({"text": v.data, "children": "", 'directory': v.attributes.directory,});
                                }
                            });
                            return $r;
                        }

                        var result = [];
                        $.each($.parseJSON(data), function (i, v) {
                            let value = {
                                "text": v.data,
                                "children": _changeParam(v.children),
                                'directory': v.attributes.directory,
                            };
                            result.push(value);
                        });
                        return JSON.stringify(result);
                    },
                    "method": "Post",
                    "dataType": "json" // needed only if you do not supply JSON headers
                }
            }
        });

    $('#column-left').trigger('changed.jstree');

    // 排序点击
    $(document).off('click', '#column-right .sortDiv  a');
    $(document).on('click', '#column-right .sortDiv a', function () {
        var clickId = this.id;
        var oldOrder = currentOrder;
        currentOrder = oldOrder == 'asc' ? 'desc' : 'asc';
        switch (clickId) {
            case 'sortName':
                currentSort = 'filename';
                break;
            case 'sortTime':
                currentSort = 'filetime';
                break;
            case 'sortSize':
                currentSort = 'filesize';
                break;
        }
        $(this).addClass(currentOrder).removeClass(oldOrder).siblings('a').removeAttr('class');
        drawList();
    });

    // 显示类型点击
    $(document).off('click', '#column-right .typeDiv  a');
    $(document).on('click', '#column-right .typeDiv a', function () {
        if ($(this).not('.active')) {
            currentType = this.id;
            drawList();
            $(this).addClass('active').siblings('a').removeClass('active');
        }
    });

    //右侧列表选中图片 单击
    $(document).off('click', '#column-right .fileList  a');
    $(document).on('click', '#column-right .fileList  a', function () {
        if ($(this).attr('class') == 'selected') {
            $(this).removeAttr('class');
        } else {
            // $('#column-right .fileList a').removeAttr('class');
            $(this).attr('class', 'selected');
        }
    });

    //新建文件夹
    $('#create').unbind('click');
    $('#create').bind('click', function () {

        var tree = $.jstree.reference('#column-left');

        $('#dialog').remove();

        html = '<div id="dialog" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">';
        html += '<div class="modal-dialog modal-sm" role="document">';
        html += '<div class="modal-content">';
        html += '<div class="modal-header">\n' +
            '        <button type="button" class="close cancelFolderBtn" aria-label="Close"><span aria-hidden="true">&times;</span></button>\n' +
            '        <h4 class="modal-title" id="myModalLabel">新建文件夹</h4>\n' +
            '      </div>';
        html += '<div class="modal-body">\n' +
            '        <input type="text" class="form-control" name="name" id="exampleInputEmail1" placeholder="" value="">\n' +
            '    </div>';
        html += '<div class="modal-footer">\n' +
            '        <button type="button" class="btn btn-default cancelFolderBtn">取消</button>\n' +
            '        <button type="button" class="btn btn-primary createFolderBtn">提交</button>\n' +
            '      </div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';

        $('#column-right').prepend(html);

        $('#dialog').modal('show');


        // 新建文件夹关闭按钮
        $('#dialog .cancelFolderBtn').unbind('click');
        $('#dialog .cancelFolderBtn').bind('click', function () {
            $('#dialog').modal('hide');
        });

        // 新建文件夹按钮点击事件
        $('#dialog .createFolderBtn').unbind('click');
        $('#dialog .createFolderBtn').bind('click', function () {
            $.ajax({
                url: CreateRoute,
                type: 'post',
                data: '_token= ' + '{{csrf_token()}}' + '&directory=' + currentPath + '&name=' + encodeURIComponent($('#dialog input[name=\'name\']').val()),
                dataType: 'json',
                success: function (json) {
                    if (json.success) {
                        tree.refresh(true);
                        $('#dialog').modal('hide');
                        alert(json.success);
                    } else {
                        alert(json.error);
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        });
    });

    // 上传按钮
    $('#upload').unbind('click');
    $('#upload').bind('click', function () {

        $('#dialog').remove();

        html = '<div id="dialog" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">';
        html += '<div class="modal-dialog modal-lg" role="document">';
        html += '<div class="modal-content">';
        html += '<div class="modal-header">\n' +
            '        <button type="button" class="close cancelUploadBtn" aria-label="Close"><span aria-hidden="true">&times;</span></button>\n' +
            '        <h4 class="modal-title" id="myModalLabel">上传文件</h4>\n' +
            '      </div>';
        html += '<div class="modal-body">\n' +
            '       <input id="input-703" name="image" type="file" multiple=true class="file-loading">\n' +
            '    </div>';
        html += '<div class="modal-footer">\n' +
            '        <button type="button" class="btn btn-default cancelUploadBtn">取消</button>\n' +
            '        <button type="button" class="btn btn-primary createUploadBtn">完成</button>\n' +
            '      </div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';

        $('#column-right').prepend(html);

        $('#dialog #input-703').fileinput({
            uploadUrl: UploadRoute, // 服务器端上传处理程序
            uploadAsync: true,  //异步上传
            minFileCount: 1,    //最小上传文件数： 1
            maxFileCount: 5,    //最大上传文件数： 5
            initialPreviewAsData: true,      // 确定传入预览数据，而不是原生标记语言
            initialPreviewFileType: 'image', // 默认为`image`，可以在下面配置中被覆盖
            uploadExtraData: {    //上传额外数据
                directory: currentPath,
            },
            previewFileType: "image",
            browseClass: "btn btn-success",
            browseIcon: "<i class=\"glyphicon glyphicon-picture\"></i> ",
            removeClass: "btn btn-danger",
            removeLabel: "删除",
            removeIcon: "<i class=\"glyphicon glyphicon-trash\"></i> ",
            uploadClass: "btn btn-info",
            uploadLabel: "上传",
            cancelLabel: "取消",
            browseLabel: "选择文件",
            showCaption: false,
            dropZoneEnabled: false,
            uploadIcon: "<i class=\"glyphicon glyphicon-upload\"></i> "
        });

        $('#dialog').modal('show');

        $('#dialog .cancelUploadBtn').bind('click', function () {
            $('#dialog').modal('hide');
        });

        $('#dialog .createUploadBtn').bind('click', function () {
            getFiles(currentPath);
            $('#dialog').modal('hide');
        });
    });

    // 删除按钮
    $('#delete').unbind('click');
    $('#delete').bind('click', function () {
        if (confirm('删除或卸载后您将不能恢复，请确定要这么做吗?')) {

            var tree = $.jstree.reference('#column-left');

            var path = [];
            $.each($('#column-right .fileList  a.selected'), function (i, v) {
                path.push($(v).find('input[name="image"]').val())
            });

            if (path && path.length > 0) {
                $.ajax({
                    url: DeleteRoute,
                    type: 'post',
                    data: {"path": path},
                    dataType: 'json',
                    success: function (json) {
                        if (json.success) {

                            getFiles(currentPath);

                            alert(json.success);
                        }

                        if (json.error) {
                            alert(json.error);
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    }
                });
            } else {

                if (currentPath && currentPath !== '/') {
                    $.ajax({
                        url: DeleteRoute,
                        type: 'post',
                        data: {"path": currentPath},
                        dataType: 'json',
                        success: function (json) {
                            if (json.success) {
                                tree.refresh(true);
                                alert(json.success);
                            }

                            if (json.error) {
                                alert(json.error);
                            }
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                        }
                    });
                } else {
                    alert('警告：请选择一个目录或文件!');
                }
            }
        }
    });

    // 移动按钮
    $('#move').unbind('click');
    $('#move').bind('click', function () {

        $('#dialog').remove();

        html = '<div id="dialog" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">';
        html += '<div class="modal-dialog modal-sm" role="document">';
        html += '<div class="modal-content">';
        html += '<div class="modal-header">\n' +
            '        <button type="button" class="close cancelMoveBtn" aria-label="Close"><span aria-hidden="true">&times;</span></button>\n' +
            '        <h4 class="modal-title" id="myModalLabel">移动</h4>\n' +
            '      </div>';
        html += '<div class="modal-body">\n' +
            '        <select class="form-control" name="to" style="width: 100%" placeholder="" value=""></select>\n' +
            '    </div>';
        html += '<div class="modal-footer">\n' +
            '        <button type="button" class="btn btn-default cancelMoveBtn">取消</button>\n' +
            '        <button type="button" class="btn btn-primary createMoveBtn">提交</button>\n' +
            '      </div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';

        $('#column-right').prepend(html);

        $('#dialog').modal('show');

        $('#dialog .cancelMoveBtn').bind('click', function () {
            $('#dialog').modal('hide');
        });

        $('#dialog .createMoveBtn').bind('click', function () {
            getFiles(currentPath);
            $('#dialog').modal('hide');
        });

        $('#dialog select[name=\'to\']').select2();

        $('#dialog select[name=\'to\']').load(FoldersRoute);

        $('#dialog .createMoveBtn').bind('click', function () {
            var tree = $.jstree.reference('#column-left');

            var path = [];
            $.each($('#column-right .fileList  a.selected'), function (i, v) {
                path.push($(v).find('input[name="image"]').val())
            });

            if (path && path.length > 0) {
                $.ajax({
                    url: MoveRoute,
                    type: 'post',
                    // data: '_token=' + '{{csrf_token()}}' + '&from=' + encodeURIComponent(path) + '&to=' + encodeURIComponent($('#dialog select[name=\'to\']').val()),
                    data: {"from": path, "to": $('#dialog select[name=\'to\']').val()},
                    dataType: 'json',
                    success: function (json) {
                        if (json.success) {
                            $('#dialog').remove();

                            tree.refresh();

                            alert(json.success);
                        }

                        if (json.error) {
                            alert(json.error);
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    }
                });
            } else {

                if (currentPath && currentPath !== '/') {
                    $.ajax({
                        url: MoveRoute,
                        type: 'post',
                        // data: '_token=' + '{{csrf_token()}}' + '&from=' + encodeURIComponent($(tree.selected).attr('directory')) + '&to=' + encodeURIComponent($('#dialog select[name=\'to\']').val()),
                        data: {"from": currentPath, "to": $('#dialog select[name=\'to\']').val()},
                        dataType: 'json',
                        success: function (json) {
                            if (json.success) {
                                $('#dialog').remove();

                                tree.refresh();

                                alert(json.success);
                            }

                            if (json.error) {
                                alert(json.error);
                            }
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                        }
                    });
                } else {
                    alert('警告：请选择一个目录或文件!');
                }
            }
        });
    });

    // 复制按钮
    $('#copy').unbind('click');
    $('#copy').bind('click', function () {

        var tree = $.jstree.reference('#column-left');

        $('#dialog').remove();

        html = '<div id="dialog" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">';
        html += '<div class="modal-dialog modal-sm" role="document">';
        html += '<div class="modal-content">';
        html += '<div class="modal-header">\n' +
            '        <button type="button" class="close cancelCopyBtn" aria-label="Close"><span aria-hidden="true">&times;</span></button>\n' +
            '        <h4 class="modal-title" id="myModalLabel">复制</h4>\n' +
            '      </div>';
        html += '<div class="modal-body">\n' +
            '        <input type="text" class="form-control" name="name"  placeholder="" value="">\n' +
            '    </div>';
        html += '<div class="modal-footer">\n' +
            '        <button type="button" class="btn btn-default cancelCopyBtn">取消</button>\n' +
            '        <button type="button" class="btn btn-primary createCopyBtn">提交</button>\n' +
            '      </div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';

        $('#column-right').prepend(html);

        $('#dialog').modal('show');


        // 新建文件夹关闭按钮
        $('#dialog .cancelCopyBtn').unbind('click');
        $('#dialog .cancelCopyBtn').bind('click', function () {
            $('#dialog').modal('hide');
        });

        // 新建文件夹按钮点击事件
        $('#dialog .createCopyBtn').unbind('click');
        $('#dialog .createCopyBtn').bind('click', function () {

            var path = [];
            $.each($('#column-right .fileList  a.selected'), function (i, v) {
                path.push($(v).find('input[name="image"]').val())
            });

            if (path && path.length > 1) {
                alert('你只能选择一个文件.');
            }

            if (path && path.length > 0) {
                $.ajax({
                    url: CopyRoute,
                    type: 'post',
                    // data: '_token='+'{{csrf_token()}}'+'&path=' + encodeURIComponent(path) + '&name=' + encodeURIComponent($('#dialog input[name=\'name\']').val()),
                    data: {"path": path[0], "name": $('#dialog input[name=\'name\']').val()},
                    dataType: 'json',
                    success: function (json) {
                        if (json.success) {
                            $('#dialog').remove();

                            tree.refresh();

                            alert(json.success);
                        }

                        if (json.error) {
                            alert(json.error);
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    }
                });
            } else {

                if (currentPath && currentPath !== '/') {
                    $.ajax({
                        url: CopyRoute,
                        type: 'post',
                        // data: '_token='+'{{csrf_token()}}'+'&path=' + encodeURIComponent($(tree.selected).attr('directory')) + '&name=' + encodeURIComponent($('#dialog input[name=\'name\']').val()),
                        data: {"path": currentPath, "name": $('#dialog input[name=\'name\']').val()},
                        dataType: 'json',
                        success: function (json) {
                            if (json.success) {
                                $('#dialog').remove();

                                tree.refresh();

                                alert(json.success);
                            }

                            if (json.error) {
                                alert(json.error);
                            }
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                        }
                    });
                } else {
                    alert('警告：请选择一个目录或文件!');
                }

            }
        });
    });

    // 重命名按钮

    $('#rename').unbind('click');
    $('#rename').bind('click', function () {
        $('#dialog').remove();

        var tree = $.jstree.reference('#column-left');

        $('#dialog').remove();

        html = '<div id="dialog" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">';
        html += '<div class="modal-dialog modal-sm" role="document">';
        html += '<div class="modal-content">';
        html += '<div class="modal-header">\n' +
            '        <button type="button" class="close cancelRenameBtn" aria-label="Close"><span aria-hidden="true">&times;</span></button>\n' +
            '        <h4 class="modal-title" id="myModalLabel">重命名</h4>\n' +
            '      </div>';
        html += '<div class="modal-body">\n' +
            '        <input type="text" class="form-control" name="name"  placeholder="" value="">\n' +
            '    </div>';
        html += '<div class="modal-footer">\n' +
            '        <button type="button" class="btn btn-default cancelRenameBtn">取消</button>\n' +
            '        <button type="button" class="btn btn-primary createRenameBtn">提交</button>\n' +
            '      </div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';

        $('#column-right').prepend(html);

        $('#dialog').modal('show');


        // 新建文件夹关闭按钮
        $('#dialog .cancelRenameBtn').unbind('click');
        $('#dialog .cancelRenameBtn').bind('click', function () {
            $('#dialog').modal('hide');
        });


        $('#dialog .createRenameBtn').unbind('click');
        $('#dialog .createRenameBtn').bind('click', function () {

            var path = [];
            $.each($('#column-right .fileList  a.selected'), function (i, v) {
                path.push($(v).find('input[name="image"]').val())
            });

            if (path && path.length > 1) {
                alert('你只能选择一个文件.');
            }

            if (path && path.length > 0) {
                $.ajax({
                    url: RenameRoute,
                    type: 'post',
                    // data: '_token='+'{{csrf_token()}}'+'&path=' + encodeURIComponent(path) + '&name=' + encodeURIComponent($('#dialog input[name=\'name\']').val()),
                    data: {"path": path[0], "name": $('#dialog input[name=\'name\']').val()},
                    dataType: 'json',
                    success: function (json) {
                        if (json.success) {
                            $('#dialog').remove();

                            tree.refresh();

                            alert(json.success);
                        }

                        if (json.error) {
                            alert(json.error);
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    }
                });
            } else {
                if (currentPath && currentPath !== '/') {
                    $.ajax({
                        url: RenameRoute,
                        type: 'post',
                        // data: '_token=' + '{{csrf_token()}}' + '&path=' + encodeURIComponent($(tree.selected).attr('directory')) + '&name=' + encodeURIComponent($('#dialog input[name=\'name\']').val()),
                        data: {"path": currentPath, "name": $('#dialog input[name=\'name\']').val()},
                        dataType: 'json',
                        success: function (json) {
                            if (json.success) {
                                $('#dialog').remove();

                                tree.refresh();

                                alert(json.success);
                            }

                            if (json.error) {
                                alert(json.error);
                            }
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                        }
                    });
                } else {
                    alert('警告：请选择一个目录或文件!');
                }
            }
        });
    });

    // 刷新
    $('#refresh').unbind('click');
    $('#refresh').bind('click', function () {
        var tree = $.jstree.reference('#column-left');
        getFiles('/');
        tree.refresh(true, true);
    });

    // 确定选择按钮
    $('#fileModel .confirmBtn').unbind('click');
    $('#fileModel .confirmBtn').bind('click', function () {
        let result = [];
        $.each($('#column-right .fileList  a.selected'), function (i, v) {
            result.push($(v).find('input[name="image"]').val())
        });
        arg.callback(result);
        $('#fileModel').modal('hide');
    });


    //排序相关
    function sortAsc(a, b) {
        a = a.split(':')[0];
        b = b.split(':')[0];
        return a - b;
    }

    function sortDesc(a, b) {
        a = a.split(':')[0];
        b = b.split(':')[0];
        return b - a;
    }

    function sortArr(arr, field, order) {
        order = order == 'asc' ? 'asc' : 'desc';
        var refer = [], result = [], index;
        var isN = true;
        for (i = 0; i < arr.length; i++) {
            refer.push(arr[i][field] + ':' + i);
            if (isNaN(arr[i][field])) {
                isN = false;
            }
        }
        if (isN) {
            if (order == 'desc') {
                refer.sort(sortDesc);
            } else if (order == 'asc') {
                refer.sort(sortAsc);
            }
        } else {
            refer.sort();
            if (order == 'desc') refer.reverse();
        }

        for (i = 0; i < refer.length; i++) {
            index = refer[i].split(':')[1];
            result[i] = arr[index];
        }
        return result;
    }

    //获取右侧文件列表
    function getFiles(directory) {
        $.ajax({
            url: FilesRoute,
            type: 'post',
            data: '_token= ' + '{{csrf_token()}}' + '&directory=' + encodeURIComponent(directory),
            dataType: 'json',
            success: function (json) {
                if (json) {
                    jsonObj = json;
                }
                drawList();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }

    //渲染右侧图片列表
    function drawList() {
        html = '';
        if (jsonObj) {
            jsonObj = sortArr(jsonObj, currentSort, currentOrder);
            switch (currentType) {
                case 'grid':
                    for (i = 0; i < jsonObj.length; i++) {
                        html +=
                            '<a>' +
                            '<img src="' + jsonObj[i]['fileuri'] + '" alt="" title="" />' +
                            '<br />' +
                            '<span>' + jsonObj[i]['filename'] + '</span>' +
                            '<br />' + jsonObj[i]['size'] + '' +
                            '<input type="hidden" name="image" value="' + jsonObj[i]['file'] + '" />' +
                            '</a>';
                    }
                    break;

                case 'list':
                    html += '<table width="100%" border="0" cellspacing="0" cellpadding="0">';
                    html += '<thead><tr>';
                    html += '<td width="50%">文件名称</td><td width="25%">文件大小</td><td width="25%">文件图片</td>';
                    html += '</tr></thead>';
                    for (i = 0; i < jsonObj.length; i++) {
                        html += '<tr>';
                        html += '<td>' +
                            '<a>' + jsonObj[i]['filename'] + '<input type="hidden" name="image" value="' + jsonObj[i]['file'] + '" /></a></td><td>' + jsonObj[i]['size'] + '</td><td>' + jsonObj[i]['updTime'] + '</td>';
                        html += '</tr>';
                    }
                    html += '</table>';
                    break;
            }
        }
        $('#column-right .fileList').html(html);
        $('#column-right, .fileList').animate({scrollTop: 0}, 'fast');
        $('#column-right .fileList').trigger('scrollstop');
    }
};




