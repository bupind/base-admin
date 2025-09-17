<!-- Main Footer -->
<footer class="main-footer">
    <!-- To the right -->
    <div class="pull-right hidden-xs">
        @if(config('backend.show_environment'))
            <strong>Env</strong>&nbsp;&nbsp; {!! config('app.env') !!}
        @endif

        &nbsp;&nbsp;&nbsp;&nbsp;

        @if(config('backend.show_version'))
        <strong>Version</strong>&nbsp;&nbsp; {!! \Base\Admin\Admin::VERSION !!}
        @endif

    </div>
    <!-- Default to the left -->
    <strong>Powered by <a href="https://github.com/wishbone-productions/base-admin" target="_blank">base-admin</a></strong>
</footer>
