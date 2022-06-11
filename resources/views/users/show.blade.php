<h1>ユーザー詳細</h1>

<div>
    <div>ID</div>
    <td>{{ $user->id }}</td>
</div>
<div>
    <div>名前</div>
    <td>{{ $user->name }}</td>
</div>
<div>
    <div>性別</div>
    <td>{{ $user->gender }}</td>
</div>
<div>
    <div>年齢</div>
    <td>{{ $user->age }}</td>
</div>
<div>
    <div>住所</div>
    <td>{{ $user->address }}</td>
</div>
<div>
    <div>電話番号</div>
    <td>{{ $user->tel }}</td>
</div>
<div>
    <div>メールアドレス</div>
    <td>{{ $user->email }}</td>
</div>
<div>
    <a href="{{ route('users.edit', $user->id) }}">編集</a>
    <form action="{{ route('users.destroy', $user->id) }}" method="POST" id="delete_{{ $user->id }}">
        @csrf
        @method('delete')
        <input type="button" class="btn login-button" data-id="{{ $user->id }}" onclick="deletePost(this);"
            value="削除">
    </form>
</div>
<a href="{{ route('users.index') }}">{{ __('一覧へ戻る') }}</a>

<script>
    function deletePost(e) {
        if (!window.confirm('本当に削除しますか？')) {
            return false;
        }
        document.getElementById('delete_' + e.dataset.id).submit();
    };
</script>
