<!-- Main Footer -->
<footer class="main-footer">
    <!-- To the right -->
    <div class="pull-right hidden-xs">
        @if(config('admin.show_environment'))
            <strong>Env</strong>&nbsp;&nbsp; {!! config('app.env') !!}
        @endif

        &nbsp;&nbsp;&nbsp;&nbsp;

        @if(config('admin.show_version'))
            <strong>Version</strong>&nbsp;&nbsp; {!! \Encore\Admin\Admin::VERSION !!}
        @endif

    </div>
    <!-- Default to the left -->
    <strong>Powered by <a href="https://github.com/z-song/laravel-admin" target="_blank">jack</a></strong>
</footer>

<!--文件模态框（Modal）-->
<div class="modal fade" id="fileModel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" data-backdrop="static"
     aria-hidden>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="fileModalLabel">模态框（Modal）标题</h4></div>
            <div class="modal-body">
                <div id="container">
                    <div id="menu">
                        <a tabindex="500" id="create" class="btn btn-sm btn-success">
                            <i class="fa fa-folder"></i>
                            <span>新建文件夹</span>
                        </a>

                        <a tabindex="500" id="move" class="btn btn-sm btn-warning">
                            <i class="fa fa-upload"></i>
                            <span>上传</span>
                        </a>

                        <a tabindex="500" id="delete" class="btn btn-sm btn-danger">
                            <i class="fa fa-trash-o"></i>
                            <span>删除</span>
                        </a>

                        <a tabindex="500" id="move" class="btn btn-sm btn-primary">
                            <i class="fa fa-hand-o-right"></i>
                            <span>移动</span>
                        </a>

                        <a tabindex="500" id="copy" class="btn btn-sm btn-info">
                            <i class="fa fa-copy"></i>
                            <span>复制</span>
                        </a>


                        <a tabindex="500" id="rename" class="btn btn-sm btn-warning">
                            <i class="fa fa-tag"></i>
                            <span>重命名</span>
                        </a>

                        <a tabindex="500" id="refresh" class="btn btn-sm btn-default">
                            <i class="fa fa-refresh"></i>
                            <span>刷新</span>
                        </a>
                    </div>
                    <div style="clear:both;"></div>
                    <div id="column-left"></div>
                    <div id="column-right">
                        <div class="topDiv">
                            <div class="sortDiv"><a class="asc" id="sortName">按文件名排序</a><a id="sortSize">按文件大小排序</a><a
                                        id="sortTime">按添加时间排序</a></div>
                            <div class="typeDiv"><a class="active" id="grid">网格</a><a id="list">列表</a></div>
                        </div>
                        <div class="fileList"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary confirmBtn">提交更改</button>
            </div>
        </div>
    </div>
</div>