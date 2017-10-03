@extends('layouts.app')

@section('content')

<div class="container">
	<div class="panel panel-default">
		<div class="panel-heading">Password for {{ $pad->heading }}</div>
  		<div class="panel-body">
  			<form action="{{ url('/pad/password/' . $pad->slug) }}" method="POST">
  				<div class="form-group">
  					<input type="password" class="form-control" name="password" placeholder="Password" required>
  					{{ csrf_field() }}
  				</div>

  				<div class="form-group">
  					<button type="submit" class="btn btn-info">Open pad</button>
  				</div>
  			</form>
  		</div>
	</div>
</div>

@endsection