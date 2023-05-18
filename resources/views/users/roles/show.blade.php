@extends('adminlte::page')

@section('title', 'Roles Details | Dashboard')

@section('content_header')
    <h1>Roles Details</h1>
@stop

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <label for="name" class="form-label"> Name <span class="text-danger"> *</span></label>
                    <input type="text" name="name" class="form-control" placeholder="For e.g. Manager"
                        value={{ ucfirst($role->name) }} disabled>
                    @if ($errors->has('name'))
                        <span class="text-danger">{{ $errors->first('name') }}</span>
                    @endif
                </div>
                <label for="name" class="form-label"> Assigned Permissions <span class="text-danger"> *</span></label>
                <!--DataTable-->
                <div class="table-responsive">
                    <table id="tblData" class="table table-bordered table-striped dataTable dtr-inline">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Guard</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        $(document).ready(function() {
            var table = $('#tblData').DataTable({
                reponsive: true,
                processing: true,
                serverSide: true,
                autoWidth: false,
                bPaginate: false,
                bFilter: true,
                ajax: "{{ route('users.roles.show', $role->id) }}",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'guard_name',
                        name: 'guard_name'
                    },
                ],
                order: [
                    [0, "desc"]
                ]
            });
        });
    </script>
@stop

@section('plugins.Datatables', true)
