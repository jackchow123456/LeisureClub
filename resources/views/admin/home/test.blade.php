<p>我是测试界面的blade！</p>
<p>我是测试界面的blade！</p>
<p>我是测试界面的blade！</p>

<link rel="stylesheet" href="{{asset('extensions/jstree/dist/themes/default/style.min.css')}}"></link>
<link rel="stylesheet" href="{{asset('css/test.css')}}"></link>

<!-- 按钮触发模态框 -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
    &nbsp;开始演示模态框
</button>

<button type="button" class="btn btn-primary file" data-toggle="modal" data-target="#fileModel">
    &nbsp;开始演示文件管理模态框
</button>

<!-- 模态框（Modal） -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static"
     aria-hidden>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    ×
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    模态框（Modal）标题
                </h4>
            </div>
            <div class="modal-body">
                <table id="mytable"></table>

                <form class="form-inline" id="toolbar">
                    <div class="form-group">
                        <div class="date">
                            <select class="select2" style="width: 80px; display: table-cell" name="key">
                                <option value="title">标题</option>
                                <option value="user_id">用户ID</option>
                            </select>
                            <!-- /btn-group -->
                            <input type="text" class="form-control" name="value">
                        </div>
                    </div>

                    <label for="inputEmail3" class="control-label"> 创建时间 </label>

                    <div class="form-group">
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control pull-right" id="start_time" name="start_time"
                                   placeholder="开始时间">
                        </div>
                    </div>

                    -

                    <div class="form-group">
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control pull-right" id="end_time" name="end_time"
                                   placeholder="结束时间">
                        </div>
                    </div>

                    <button type="button" class="btn btn-info btn-flat searchBtn">搜索</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭
                </button>
                <button type="button" class="btn btn-primary confirmBtn">
                    提交更改
                </button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>

<script>
    $(function () {

        $("#mytable").bootstrapTable({
            locale: "zh-CN",
            url: "/api/admin/post/getList",  //请求地址
            striped: true, //是否显示行间隔色
            pageNumber: 1, //初始化加载第一页
            pagination: true,//是否分页
            toolbar: "#toolbar",
            pageSize: 10,//单页记录数
            showRefresh: true,//刷新按钮
            sidePagination: "server",
            clickToSelect: true,
            pageList: "[10, 25, 50, 100]",
            smartDisplay: false,
            queryParams: function (params) {

                var temp = {
                    key: $('#toolbar select[name="key"]').val(),
                    value: $('#toolbar input[name="value"]').val(),
                    start_time: $('#toolbar input[name="start_time"]').val(),
                    end_time: $('#toolbar input[name="end_time"]').val(),
                    page: this.pageNumber,
                    pageSize: params.limit  // 每页显示数量
                };
                return temp;
            },
            columns: [
                {
                    checkbox: true,
                }, {
                    title: 'id',
                    field: 'id',
                }, {
                    title: '标题',
                    field: 'title',
                }, {
                    title: 'userId',
                    field: 'user_id',
                }, {
                    title: '时间',
                    field: 'created_at',
                    sortable: true,
                    visible: false
                }]
        });

        $('.select2').select2({
            minimumResultsForSearch: Infinity
        });

        $('#start_time').datetimepicker({"format": "YYYY-MM-DD", "locale": "zh-CN", "allowInputToggle": true});
        $('#end_time').datetimepicker({"format": "YYYY-MM-DD", "locale": "zh-CN", "allowInputToggle": true});

        $('.searchBtn').click(function () {
            $('#mytable').bootstrapTable('refresh');
        });

        $('.confirmBtn').click(function () {
            console.log($("#mytable").bootstrapTable('getSelections'));
            $('#myModal').modal('hide');
        })
    });
</script>


<script type="text/javascript" src="{{asset('extensions/jstree/dist/jstree.min.js')}}"></script>
<script type="text/javascript" src="{{asset('js/test.js')}}"></script>

<script>
    var DirectoryRoute = '{{route('fileManager.directory')}}';
    var ListFoldersRoute = '{{route('fileManager.listFolders')}}';
    var FilesRoute = '{{route('fileManager.files')}}';
    var CreateRoute = '{{route('fileManager.create')}}';
    var UploadRoute = '{{route('fileManager.upload')}}';
    var DeleteRoute = '{{route('fileManager.delete')}}';
    var MoveRoute = '{{route('fileManager.move')}}';
    var CopyRoute = '{{route('fileManager.copy')}}';
    var RenameRoute = '{{route('fileManager.rename')}}';
    var FoldersRoute = '{{route('fileManager.folders')}}';

    $('.file').on('click', function () {
        $(this).jacktree({
            "DirectoryRoute": DirectoryRoute,
            "ListFoldersRoute": ListFoldersRoute,
            "FilesRoute": FilesRoute,
            "CreateRoute": CreateRoute,
            "UploadRoute": UploadRoute,
            "DeleteRoute": DeleteRoute,
            "MoveRoute": MoveRoute,
            "CopyRoute": CopyRoute,
            "RenameRoute": RenameRoute,
            "FoldersRoute": FoldersRoute,
            "callback": function (data) {
                console.log(data);
            }
        });
    });
</script>


