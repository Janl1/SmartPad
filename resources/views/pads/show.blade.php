@extends('layouts.app')

@section('content')

<script src="https://www.gstatic.com/firebasejs/3.3.0/firebase.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.17.0/codemirror.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.17.0/codemirror.css" />
<link rel="stylesheet" href="https://cdn.firebase.com/libs/firepad/1.4.0/firepad.css" />
<script src="https://cdn.firebase.com/libs/firepad/1.4.0/firepad.min.js"></script>
<script src="https://ultimate-community.de/firepad-userlist.js"></script>
<link href="{{ asset('css/firepad-userlist.css') }}" rel="stylesheet">
<script src="{{ asset('js/firepad-userlist.js') }}"></script>


<div class="container">
	<div id="userlist" style="position: absolute; left: 0; top: 30px; bottom: 0; height: auto; width: 175px;"></div>
  	<div id="firepad" style="position: absolute; left: 175px; top: 30px; bottom: 0; right: 0; height: auto;"></div>
</div>

<script>
	function init() {
		var config = {
			apiKey: "{{ config('smartpad.FireBase_apiKey') }}",
			databaseURL: "{{ config('smartpad.FireBase_databaseURL') }}",
		};
		firebase.initializeApp(config);
		var firepadRef = getExampleRef();
		var codeMirror = CodeMirror(document.getElementById('firepad'), { lineWrapping: true });
		var userId = "{{ Auth::user()->name }}";
		var firepad = Firepad.fromCodeMirror(firepadRef, codeMirror,
			{ richTextToolbar: true, richTextShortcuts: true, userId: userId});
		var firepadUserList = FirepadUserList.fromDiv(firepadRef.child('users'), document.getElementById('userlist'), userId, userId);
		firepad.on('ready', function() {
			if (firepad.isHistoryEmpty()) {
				firepad.setText('Welcome to SmartPad');
			}
		});
	}

	function getExampleRef() {
		var ref = firebase.database().ref();
		var hash = "{{ $pad->slug }}";

		if (hash) {
			ref = ref.child(hash);
		} else {
			ref = ref.push(); 
		}
		if (typeof console !== 'undefined') {
		}
		return ref;
	}

	$(function(){
		document.title = '{{ $pad->heading }} | SmartPad';
		init();
	})
</script>

@endsection