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
    {{--<strong>Powered by <a href="https://github.com/z-song/laravel-admin" target="_blank">jack</a></strong>--}}
    <strong><a href="http://www.beian.miit.gov.cn" target="_blank">粤ICP备19142756号-1</a></strong>
</footer>
