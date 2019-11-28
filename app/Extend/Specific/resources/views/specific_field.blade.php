<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">


    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>

    <div class="{{$viewClass['field']}}">

        <div class="specific_warp {{$class}}">

            @include('admin::form.error')

            <input type="hidden" name="{{$name}}"/>
            {{--<div class="box box-default box-solid">--}}
            {{--<div class="box-header with-border">--}}
            {{--<div class="input-group">--}}
            {{--<div class="input-group-btn">--}}
            {{--<button type="button" class="btn btn-primary" style="color: #FFFFFF"> 规格名</button>--}}
            {{--</div>--}}
            {{--<!-- /btn-group -->--}}
            {{--<select class="form-control {{$class}}" style="width: 20%;display: inline-block;">--}}
            {{--<option></option>--}}
            {{--</select>--}}
            {{--</div>--}}

            {{--<div class="box-tools pull-right">--}}
            {{--<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>--}}
            {{--</button>--}}
            {{--</div>--}}
            {{--<!-- /.box-tools -->--}}
            {{--</div>--}}
            {{--<!-- /.box-header -->--}}
            {{--<div class="box-body" style="display: block;">--}}
            {{--<div class="input-group col-sm-3 specific_value">--}}
            {{--<div class="input-group-btn">--}}
            {{--<button type="button" class="btn btn-info" style="color: #FFFFFF"> 规格值</button>--}}
            {{--</div>--}}
            {{--<!-- /btn-group -->--}}
            {{--<input class="form-control" type="text" value="">--}}

            {{--<div class="input-group-btn">--}}
            {{--<button type="button" class="btn btn-danger remove"><i class="fa fa-times"></i></button>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--<div class="input-group col-sm-2 specific_value">--}}
            {{--<div class="input-group-btn">--}}
            {{--<button type="button" class="btn btn-success add">--}}
            {{--<i class="fa fa-plus" aria-hidden="true"></i>--}}
            {{--&nbsp;添加规格值--}}
            {{--</button>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--</div>--}}
            {{--<!-- /.box-body -->--}}
            {{--</div>--}}


            <button type="button" class="btn btn-success add_box">
                <i class="fa fa-plus-square" aria-hidden="true"></i>
                &nbsp;添加商品规格
            </button>


            {{--<span class="help-block">--}}
            {{--<i class="fa fa-info-circle"></i>&nbsp;如果选择输入的新规格可以自动新建.--}}
            {{--</span>--}}
        </div>



        @include('admin::form.help-block')

    </div>


</div>