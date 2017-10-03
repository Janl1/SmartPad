@extends('layouts.app')

@section('content')

<div class="container">
	<div class="panel panel-default">
		<div class="panel-heading">Users</div>
  		<div class="panel-body">
        <table class="table">
          <thead>
            <tr>
              <th>#ID</th>
              <th>Username</th>
              <th>Email</th>
              <th>Status</th>
              <th>Pads</th>
              <th>Edit</th>
              <th>Delete</th>
            </tr>
          </thead>
          <tbody>
            @foreach(User::all() as $user)
            <tr>
              <td>{{ $user->id }}</td>
              <td>{{ $user->name }}</td>
              <td>{{ $user->email }}</td>
              <td>{{ $user->status }}</td>
              <td>{{ $user->getPads()->count() }}</td>
              <td><a onclick="editUser('{{ $user->id }}');"><i class="fa fa-edit"></i></a></td>
              <td><a href=""><i class="fa fa-trash"></i></a></td>
            </tr>
            @endforeach
          </tbody>
        </table>
  		</div>
	</div>
</div>

<!-- Modals -->
<div id="modal_info" class="sidemodal fade" role="dialog">
  <div class="sidemodal-dialog">
    <div class="sidemodal-content">
      <div class="sidemodal-header">
        <button type="button" class="close" data-dismiss="sidemodal">&times;</button>
        <h4 class="sidemodal-title">Edit User</h4>
      </div>
      <div class="sidemodal-body">
        
        <form>
          <div class="form-group">
            <input type="text" id="editUserName" placeholder="Name" class="form-control">
          </div>

          <div class="form-group">
            <input type="text" id="editUserEmail" placeholder="Email" class="form-control">
          </div>

          <div class="form-group">
            <select class="form-control" id="editUserStatus">
              <option value="NEW">NEW</option>
              <option value="USER">USER</option>
              <option value="ADMIN">ADMIN</option>
            </select>
          </div>

          <div class="form-group">
            <a id="editUserButton" onclick="editUserSave();" class="btn btn-success">Save</a>
          </div>
        </form>

      </div>
      <div class="sidemodal-footer">
        <button type="button" class="btn btn-default" data-dismiss="sidemodal">Close</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">  
  function editUserSave(id)
  {
    $.ajax({
      url: "{{ url('/acp/user/edit') }}/" + id,
      type: 'POST',
      data: {
        _token: '{{ csrf_token() }}',
        name: $('#editUserName').val(),
        email: $('#editUserEmail').val(),
        status: $('#editUserStatus').val(),
      },
    })
    .done(function() {
      swal({
        title: 'Success!',
        text: 'User was edited!',
        type: 'success',
      }).then(function () {
        $('#modal_info').sidemodal('toggle');
        location.reload();
      })
    })
    .fail(function() {
      swal({
        title: 'Error!',
        text: 'Something went wrong!',
        type: 'error',
      }).then(function () {
      })
    });
    
  }

  function editUser(id)
  {
    $.ajax({
      url: "{{ url('/acp/user/info') }}/" + id,
      type: 'GET',
    })
    .done(function(data) {
      var obj = JSON.parse(data);
      $('#editUserName').val(obj.name);
      $('#editUserEmail').val(obj.email);
      $('#editUserStatus').val(obj.status);
      $('#editUserButton').attr('onclick',"editUserSave('" + id + "');");
      $('#modal_info').sidemodal('toggle');
    });
    
  }
</script>

@endsection