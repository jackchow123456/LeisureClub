<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">


    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>

    <div class="{{$viewClass['field']}}">

        <div class="specific_warp {{$class}}">

            @include('admin::form.error')

            <input type="hidden" name="{{$name}}"/>
            <div class="box box-default box-solid">
            <div class="box-header with-border">
            <div class="input-group">
            <div class="input-group-btn">
            <button type="button" class="btn btn-primary" style="color: #FFFFFF"> 规格名</button>
            </div>
            <!-- /btn-group -->
            <select class="form-control {{$class}}" style="width: 20%;display: inline-block;">
            <option></option>
            </select>
            </div>

            <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
            </button>
            </div>
            <!-- /.box-tools -->
            </div>
            <!-- /.box-header -->
            <div class="box-body" style="display: block;">
            <div class="input-group col-sm-3 specific_value">
            <div class="input-group-btn">
            <button type="button" class="btn btn-info" style="color: #FFFFFF"> 规格值</button>
            </div>
            <!-- /btn-group -->
            <input class="form-control" type="text" value="">

            <div class="input-group-btn">
            <button type="button" class="btn btn-danger remove"><i class="fa fa-times"></i></button>
            </div>
            </div>
            <div class="input-group col-sm-2 specific_value">
            <div class="input-group-btn">
            <button type="button" class="btn btn-success add">
            <i class="fa fa-plus" aria-hidden="true"></i>
            &nbsp;添加规格值
            </button>
            </div>
            </div>
            </div>
            <!-- /.box-body -->
            </div>


            <button type="button" class="btn btn-success add_box">
                <i class="fa fa-plus-square" aria-hidden="true"></i>
                &nbsp;添加商品规格
            </button>

            <div class="specific_detail">
                <div style="height: 20px; border-bottom: 1px solid #eee; text-align: center;margin-top: 20px;margin-bottom: 20px;">
                  <span style="font-size: 18px; background-color: #ffffff; padding: 0 10px;">
                    规格详情
                  </span>
                </div>
            </div>

            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Striped Full Width Table</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body no-padding">
                    <table class="table table-striped">
                        <tbody><tr>
                            <th style="width: 10px">#</th>
                            <th>Task</th>
                            <th>Tas1</th>
                            <th>Tas2</th>
                            <th>Tas3</th>
                            <th>Tas4</th>
                            <th>Progress</th>
                            <th style="width: 40px">Label</th>
                        </tr>
                        <tr>
                            <td>1.</td>
                            <td>Update software</td>
                            <td>Update software</td>
                            <td>Update software</td>
                            <td>Update software</td>
                            <td>Update software</td>
                            <td>
                                <div class="progress progress-xs">
                                    <div class="progress-bar progress-bar-danger" style="width: 55%"></div>
                                </div>
                            </td>
                            <td><span class="badge bg-red">55%</span></td>
                        </tr>
                        <tr>
                            <td>2.</td>
                            <td>Clean database</td>
                            <td>Clean database</td>
                            <td>Clean database</td>
                            <td>Clean database</td>
                            <td>Clean database</td>
                            <td>
                                <div class="progress progress-xs">
                                    <div class="progress-bar progress-bar-yellow" style="width: 70%"></div>
                                </div>
                            </td>
                            <td><span class="badge bg-yellow">70%</span></td>
                        </tr>
                        <tr>
                            <td>3.</td>
                            <td>Cron job running</td>
                            <td>Cron job running</td>
                            <td>Cron job running</td>
                            <td>Cron job running</td>
                            <td>Cron job running</td>
                            <td>
                                <div class="progress progress-xs progress-striped active">
                                    <div class="progress-bar progress-bar-primary" style="width: 30%"></div>
                                </div>
                            </td>
                            <td><span class="badge bg-light-blue">30%</span></td>
                        </tr>
                        <tr>
                            <td>4.</td>
                            <td>Fix and squish bugs</td>
                            <td>Fix and squish bugs</td>
                            <td>Fix and squish bugs</td>
                            <td>Fix and squish bugs</td>
                            <td>Fix and squish bugs</td>
                            <td>
                                <div class="progress progress-xs progress-striped active">
                                    <div class="progress-bar progress-bar-success" style="width: 90%"></div>
                                </div>
                            </td>
                            <td><span class="badge bg-green">90%</span></td>
                        </tr>
                        </tbody></table>
                </div>
                <!-- /.box-body -->
            </div>

            {{--<span class="help-block">--}}
            {{--<i class="fa fa-info-circle"></i>&nbsp;如果选择输入的新规格可以自动新建.--}}
            {{--</span>--}}
        </div>


        @include('admin::form.help-block')

    </div>


</div>
