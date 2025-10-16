@extends('layouts.app')

@push('style')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4-4.1.1/jq-3.3.1/dt-1.10.20/datatables.min.css"/>
<style type="text/css">
.avatar {
    vertical-align: middle;
    width: 50px;
    height: 50px;
    border-radius: 3px;
}
</style>
@endpush

@section('content')
<div class="container">
    <h3>Violators</h3>
    <div class="row">
        <div class="col-sm-12">
            <table id="list" class="table table-hover table-borderless shadow-sm rounded">
                <thead>
                    <tr>
                        <th>Violators</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($violators as $v)
                    @if($v)
                    <tr>
                        <td>
                            @if($v->student_avatar)
                            <p class="mb-0 float-left mr-2"><img src="{{ $v->student_avatar }}" class="img-thumbnail avatar"></p>
                            @endif
                            {{ $v->student_fname }} {{ $v->student_mname }} {{ $v->student_lname }}
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4-4.1.1/jq-3.3.1/dt-1.10.20/datatables.min.js"></script>
    <script>
        $(document).ready(function() {
            $.noConflict();
            $('#list').DataTable();
        } );
        $(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });   
        });   
    </script>
@endpush