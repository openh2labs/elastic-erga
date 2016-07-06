<html>
<body>
{!! Form::open(['method' => 'get', 'route' => 'search']) !!}

{!! Form::input('search', 'query', Input::get('query', ''))!!}
{!! Form::submit('Filter results') !!}

{!! Form:: close() !!}
<pre>
<?php
print_r($posts);
?>
    </pre>
@foreach($posts as $post)
    <div>
        <h2>{{{ $post->title }}}</h2>
        <div>{{{ $post->content }}}</div>
        <div><small>{{{ $post->tags }}}</small></div>
    </div>
@endforeach

<!--
{!! Form::open(['url' => '/']) !!}
{!! Form::text('name', '', ['placeholder' => 'Name']) !!}
{!! Form::submit('Register') !!}
{!! Form::close() !!}

{!! Form::open(['url' => '/']) !!}
{!! Form::text('name', '', ['placeholder' => 'Name']) !!}
{!! Form::submit('Register') !!}
{!! Form::close() !!}
        -->

</body>
</html>