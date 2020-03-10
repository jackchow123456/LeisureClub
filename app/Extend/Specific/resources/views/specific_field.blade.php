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

            <div class="specific_detail" style="display: none">
                <div style="height: 20px; border-bottom: 1px solid #eee; text-align: center;margin-top: 20px;margin-bottom: 20px;">
                  <span style="font-size: 18px; background-color: #ffffff; padding: 0 10px;">
                    规格详情
                  </span>
                </div>


                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Striped Full Width Table</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body no-padding">
                        <table class="table table-striped specific_table">
                            <thead></thead>
                            <tbody>
                            <tr>
                                <td data-field="颜色">红色</td>
                                <td data-field="pic"><input value="" type="hidden" class="form-control">
                                    <span class="Js_sku_upload">+</span>
                                </td>
                                <td data-field="price"><input value="0" type="text" class="form-control"></td>
                                <td data-field="stock"><input value="0" type="text" class="form-control"></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>

                {{--<span class="help-block">--}}
                {{--<i class="fa fa-info-circle"></i>&nbsp;如果选择输入的新规格可以自动新建.--}}
                {{--</span>--}}
            </div>
        </div>


        @include('admin::form.help-block')

    </div>


</div>
