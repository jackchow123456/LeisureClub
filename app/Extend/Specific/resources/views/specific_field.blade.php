<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">
    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>
    <div class="{{$viewClass['field']}}">
        <div class="specific_warp {{$class}}">
            <input type="hidden" class="Js_sku_input" name="{{$name}}" value="{{old($column, $value)}}">
            @include('admin::form.error')
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
                        {{--<h3 class="box-title">Striped Full Width Table</h3>--}}
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body no-padding">
                        <table class="table table-striped specific_table">
                            <thead></thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>
        </div>
        @include('admin::form.help-block')
    </div>
</div>
