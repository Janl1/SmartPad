@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-6">
            <div class="panel panel-default">
                <div class="panel-heading">Users</div>
                <div class="panel-body">

                    <div class="list-group">
                        @foreach(User::all() as $user)
                        <a class="list-group-item">{{ $user->name }}</a>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>

        <div class="col-lg-6">

            <div class="panel panel-default">
                <div class="panel-heading">Active pads <a style="float: right;" data-toggle="sidemodal" data-target="#modal_new"><i class="fa fa-plus"></i> New</a></div>
                <div class="panel-body">

                    @if(Pad::where('status', 'ACTIVE')->count() == 0)
                    <center><strong>No pads found!</strong></center>
                    @endif

                    <div class="list-group">
                        @foreach(Pad::where('status', 'ACTIVE')->orderBy('id', 'desc')->get() as $pad)
                            <a onclick="openInfoPad('{{ $pad->slug }}');" class="list-group-item">
                                <h4 class="list-group-item-heading">{{ $pad->heading }}</h4>
                                <p class="list-group-item-text">Author: {{ User::findOrFail($pad->user_id)->name }} | {{ $pad->created_at }}</p>
                            </a>
                        @endforeach
                    </div>

                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">Archived pads</div>
                <div class="panel-body">
                    @if(Pad::where('status', 'ARCHIVED')->count() == 0)
                    <center><strong>No pads found!</strong></center>
                    @endif

                    <div class="list-group">
                        @foreach(Pad::where('status', 'ARCHIVED')->orderBy('id', 'desc')->get() as $pad)
                            <a onclick="openInfoPad('{{ $pad->slug }}');" class="list-group-item">
                                <h4 class="list-group-item-heading">{{ $pad->heading }}</h4>
                                <p class="list-group-item-text">Author: {{ User::findOrFail($pad->user_id)->name }} | {{ $pad->created_at }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modals -->
<div id="modal_new" class="sidemodal fade" role="dialog">
  <div class="sidemodal-dialog">
    <div class="sidemodal-content">
      <div class="sidemodal-header">
        <button type="button" class="close" data-dismiss="sidemodal">&times;</button>
        <h4 class="sidemodal-title">New pad</h4>
      </div>
      <div class="sidemodal-body">
        <form>
            <div class="form-group">
                <input type="text" id="newPadHeading" placeholder="Heading" class="form-control">
            </div>

            <div class="form-group">
                <input type="password" id="newPadPassword" placeholder="Password (optional)" class="form-control">
            </div>

            <div class="form-group">
                <a onclick="addNewPad();" class="btn btn-success">Add</a>
            </div>
        </form>
      </div>
      <div class="sidemodal-footer">
        <button type="button" class="btn btn-default" data-dismiss="sidemodal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Modals -->
<div id="modal_info" class="sidemodal fade" role="dialog">
  <div class="sidemodal-dialog">
    <div class="sidemodal-content">
      <div class="sidemodal-header">
        <button type="button" class="close" data-dismiss="sidemodal">&times;</button>
        <h4 class="sidemodal-title"><span id="infoPadHeading"></span></h4>
      </div>
      <div class="sidemodal-body">
        
        <strong>Heading: </strong><span id="infoPadHeading2"></span><br>
        <strong>Creation date: </strong><span id="infoPadCreatedAt"></span><br>
        <strong>Last visit: </strong><span id="infoPadUpdatedAt"></span><br>
        <strong>Total visits: </strong><span id="infoPadVisits"></span><br>
        <strong>Password: </strong><span id="infoPadPassword"></span><br><hr><br>

        <a id="linkPadOpen" class="btn btn-sm btn-info">Open pad</a> <a id="linkPadArchive" class="btn btn-sm btn-warning">Archive pad (Toggle)</a> <a id="linkPadDelete" class="btn btn-sm btn-danger">Delete pad</a>

      </div>
      <div class="sidemodal-footer">
        <button type="button" class="btn btn-default" data-dismiss="sidemodal">Close</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
    
    function archivePad(id)
    {
        swal({
          title: 'Are you sure?',
          text: "Do you want to archive the pad?",
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, archive it!'
        }).then(function () {

            $.ajax({
                url: "{{ url('/pad/archive') }}",
                type: 'POST',
                data: {
                    id: id,
                    _token: '{{ csrf_token() }}'
                },
            })
            .done(function() {
                swal({
                  title: 'Success!',
                  text: 'Pad was archived!',
                  type: 'success',
                }).then(function () {
                    $('#modal_info').sidemodal('toggle');
                    location.reload();
                })
            })
            .fail(function() {
                swal(
                  'Oops...',
                  'Something went wrong!',
                  'error'
                )
            });
        })
    }

    function deletePad(id)
    {
        swal({
          title: 'Are you sure?',
          text: "Do you want to delete the pad?",
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, delete it!'
        }).then(function () {

            $.ajax({
                url: "{{ url('/pad/delete') }}",
                type: 'POST',
                data: {
                    id: id,
                    _token: '{{ csrf_token() }}'
                },
            })
            .done(function() {
                swal({
                  title: 'Success!',
                  text: 'Pad was deleted!',
                  type: 'success',
                }).then(function () {
                    $('#modal_info').sidemodal('toggle');
                    location.reload();
                })
            })
            .fail(function() {
                swal(
                  'Oops...',
                  'Something went wrong!',
                  'error'
                )
            });
        })
    }

    function openInfoPad(id)
    {
        $.ajax({
            url: "{{ url('/pad/info/') }}" + "/" + id,
            type: 'GET',
        })
        .done(function(data) {
            var info = JSON.parse(data);
            $('#infoPadHeading').html(info.heading);
            $('#infoPadHeading2').html(info.heading);
            $('#infoPadAuthor').html(info.user_id);
            $('#infoPadCreatedAt').html(info.created_at);
            $('#infoPadUpdatedAt').html(info.updated_at);
            $('#infoPadVisits').html(info.clicks);
            if(info.password == "NULL") {
              $('#infoPadPassword').html("No");
            } else {
              $('#infoPadPassword').html("Yes");
            }
            $('#modal_info').sidemodal('toggle');

            $("#linkPadOpen").attr("href", "{{ url('/pad/open/') }}/" + info.slug);
            $("#linkPadArchive").attr('onclick', "archivePad('"+info.slug+"')");
            $("#linkPadDelete").attr('onclick', "deletePad('"+info.slug+"')");
        }).fail(function() {
            swal({
              title: 'Oops...',
              text: 'Failed loading pad info!',
              type: 'error',
            }).then(function () {
            })
        });
        
    }

    function addNewPad()
    {
        if($('#newPadHeading').val() == "") {
            swal(
              'Oops...',
              'Please enter a heading!',
              'error'
            )
            return;
        }
        var password = $('#newPadPassword').val();
        if(password == "") {
            password = "NULL";
        }

        $.ajax({
            url: "{{ url('/pad/create') }}",
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                newPadHeading: $('#newPadHeading').val(),
                newPadPassword: password
            },
        })
        .done(function() {
            swal({
              title: 'Success!',
              text: 'Pad was created!',
              type: 'success',
            }).then(function () {
                $('#modal_new').sidemodal('toggle');
                location.reload();
            })
            
        })
        .fail(function() {
            $('#modal_new').sidemodal('toggle');
            swal(
              'Oops...',
              'Something went wrong!',
              'error'
            )
        });
    }
</script>

@endsection