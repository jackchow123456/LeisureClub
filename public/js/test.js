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


    var initHtml = '<!--文件模态框（Modal）--><div class="modal fade"id="fileModel"tabindex="-1"role="dialog"aria-labelledby="myModalLabel"data-backdrop="static"aria-hidden><div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header"><button type="button"class="close"data-dismiss="modal"aria-hidden="true">×</button><h4 class="modal-title"id="fileModalLabel">模态框（Modal）标题</h4></div><div class="modal-body"><div id="container"><div id="menu"><a id="create"class="button"style="background-image: url({{asset("admin_template/images/filemanager/folder.png")}});">新建文件夹</a><a id="delete"class="button"style="background-image: url({{asset("admin_template/images/filemanager/edit-delete.png")}});">删除</a><a id="move"class="button"style="background-image: url({{asset("admin_template/images/filemanager/edit-cut.png")}});">移动</a><a id="copy"class="button"style="background-image: url({{asset("admin_template/images/filemanager/edit-copy.png")}});">复制</a><a id="rename"class="button"style="background-image: url({{asset("admin_template/images/filemanager/edit-rename.png")}});">重命名</a><a id="upload"class="button"style="background-image: url({{asset("admin_template/images/filemanager/upload.png")}});">上传</a><a id="multiUpd"class="button"style="background-image: url({{asset("admin_template/images/filemanager/upload.png")}});">批量上传</a><a id="refresh"class="button"style="background-image: url({{asset("admin_template/images/filemanager/refresh.png")}});">刷新</a><a id="insert"class="button"style="background-image: url({{asset("admin_template/images/filemanager/insert.png")}});">提交</a></div><div style="clear:both;"></div><div id="column-left"></div><div id="column-right"><div class="topDiv"><div class="sortDiv"><a class="asc"id="sortName">按文件名排序</a><a id="sortSize">按文件大小排序</a><a id="sortTime">按添加时间排序</a></div><div class="typeDiv"><a class="active"id="grid">网格</a><a id="list">列表</a></div></div><div class="fileList"></div></div></div></div><div class="modal-footer"><button type="button"class="btn btn-default"data-dismiss="modal">关闭</button><button type="button"class="btn btn-primary confirmBtn">提交更改</button></div></div><!--/.modal-content--></div><!--/.modal--></div>'

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

    // $('#column-left').trigger('changed.jstree');

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

        var tree = $.jstree.reference('#column-left');

        $('#dialog').remove();

        html = '<div id="dialog" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">';
        html += '<div class="modal-dialog modal-lg" role="document">';
        html += '<div class="modal-content">';
        html += '<div class="modal-header">\n' +
            '        <button type="button" class="close cancelFolderBtn" aria-label="Close"><span aria-hidden="true">&times;</span></button>\n' +
            '        <h4 class="modal-title" id="myModalLabel">上传文件</h4>\n' +
            '      </div>';
        html += '<div class="modal-body">\n' +
            '       <input id="input-703" name="image" type="file" multiple=true class="file-loading">\n' +
            '    </div>';
        html += '<div class="modal-footer">\n' +
            '        <button type="button" class="btn btn-default cancelFolderBtn">取消</button>\n' +
            '        <button type="button" class="btn btn-primary createFolderBtn">完成</button>\n' +
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

        // 新建文件夹关闭按钮
        $('#dialog .cancelFolderBtn').bind('click', function () {
            $('#dialog').modal('hide');
        });

        // 新建文件夹按钮点击事件
        $('#dialog .createFolderBtn').bind('click', function () {
            getFiles(currentPath);
            $('#dialog').modal('hide');
        });
    });

    // 确定选择按钮
    $('#fileModel .confirmBtn').unbind('click');
    $('#fileModel .confirmBtn').bind('click', function () {
        let result = [];
        $.each($('#column-right .fileList  a.selected'), function (i, v) {
            result.push($(v).find('input[name="image"]').val())
        })
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




