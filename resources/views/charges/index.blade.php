@extends('layouts.appinside')

@section('content')

    <!-- BEGIN PAGE BAR -->
    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <a href="{{ secure_url('home') }}">Home</a>
                <i class="fa fa-circle"></i>
            </li>
            <li>
                <span>Charges</span>
            </li>
        </ul>
    </div>
    <!-- END PAGE BAR -->
    <!-- BEGIN PAGE TITLE-->
    <h1 class="page-title"> Charges
        <small> defult</small>
    </h1>
    <!-- END PAGE TITLE-->
    <!-- END PAGE HEADER-->

    <div class="row">
        <br>

        <div class="col-md-3">
            <ul class="ver-inline-menu tabbable margin-bottom-10">

                {{-- */ $i = 1 /* --}}

                @foreach($data as $row)
            
                    <li @if($i == 1) class="active" @endif >
                        <a data-toggle="tab" href="#tab_{{ $row->id }}">
                            <i class="fa fa-arrow-right"></i>{{ $row->name }}
                        </a>
                    </li>

                    {{-- */ $i++ /* --}}

                @endforeach

            </ul>
        </div>
        <div class="col-md-9">
            <div class="tab-content">

                {{-- */ $i = 1 /* --}}
                @foreach($data as $row)

                    <div id="tab_{{ $row->id }}" class="tab-pane  @if($i == 1) active @endif ">

                        <div id="accordion3" class="panel-group">

                            @foreach($row->sub_cats as $row2)

                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion3" href="#accordion3_{{ $row2->id }}"> {{ $row2->name }} </a>
                                        </h4>
                                    </div>
                                    <div id="accordion3_{{ $row2->id }}" class="panel-collapse collapse">
                                        <div class="panel-body">

                                        <table class="table table-striped table-bordered table-hover dt-responsive my_datatable" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th class="all">Tier</th>
                                                        <th class="all">Charge model</th>
                                                        <!-- <th class="all">Percentage range start</th>
                                                        <th class="all">Percentage range end</th>
                                                        <th class="all">Percentage value</th> -->
                                                        <th class="all">Additional range per slot</th>
                                                        <th class="all">Additional charge per slot</th>
                                                        <th class="all">Additional charge type</th>
                                                        <th class="all">Fixed charge</th>
                                                        <th class="all">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    @foreach($row2->sub_cat_charge as $row3)

                                                        @if($row3->store_id == null)

                                                            <tr>
                                                                <td>
                                                                    {{ $row3->zone_genre->title }}
                                                                    <p class="text-muted nm-tb">{{ $row3->zone_genre->description }}</p>
                                                                </td>
                                                                <td>
                                                                    {{ $row3->charge_model->title }}
                                                                    <p class="text-muted nm-tb">{{ $row3->charge_model->description }}</p>
                                                                </td>
                                                                <!-- <td>{{ $row3->percentage_range_start }}</td>
                                                                <td>{{ $row3->percentage_range_end }}</td>
                                                                <td>{{ $row3->percentage_value }}</td> -->
                                                                <td>{{ $row3->additional_range_per_slot }}</td>
                                                                <td>{{ $row3->additional_charge_per_slot }}</td>
                                                                @if($row3->additional_charge_type==0)
                                                                    <td>Normal</td>
                                                                @else
                                                                    <td>Flat</td>
                                                                @endif
                                                                <td>{{ $row3->fixed_charge }}</td>
                                                                <td>
                                                                    <a href="{{ secure_url('charge') }}/{{ $row3->id }}/edit" class="label label-success"> Update </a>
                                                                </td>
                                                            </tr>

                                                        @endIf

                                                    @endforeach
                                                    
                                                </tbody>
                                            </table>

                                        </div>
                                    </div>
                                </div>

                            @endforeach

                        </div>

                    </div>

                    {{-- */ $i++ /* --}}
                @endforeach
            </div>            
        </div>

    </div>

    <script type="text/javascript">
        $(document ).ready(function() {
            // Navigation Highlight
            highlight_nav('charge-manage', 'charges');

            $('.my_datatable').DataTable({
                "responsive": true,
                "aaSorting": [],
                "bDestroy": true
            });
        });
    </script>

@endsection
