<h1>ユーザー新規作成</h1>

@foreach ($errors->all() as $error)
    <li>{{ $error }}</li>
@endforeach

<form action="{{ route('users.store') }}" method="POST">
    @csrf

    <div>
        <label for="name">名前</label>
        <input type="text" name="name" id="name" value="{{ old('name') }}" required>
    </div>

    <div>
        <label for="gender">性別</label>
        男性: <input type="radio" name="gender" id="gender" value="1" checked>
        女性: <input type="radio" name="gender" id="gender" value="2">
    </div>

    <div>
        <label for="age">年齢</label>
        <input type="number" name="age" id="age" min="0" max="120" value="{{ old('age') }}">
    </div>

    <div>
        <label for="address">住所</label>
        <input type="text" name="address" id="address" value="{{ old('address') }}">
    </div>

    <div>
        <label for="tel">電話番号</label>
        <input type="tel" name="tel" id="tel" value="{{ old('tel') }}">
    </div>

    <div>
        <label for="email">メールアドレス</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}" required>
    </div>

    <div>
        <label for="password">パスワード</label>
        <input type="password" name="password" id="password" required>
    </div>

    <div>
        <label for="password_confirmation">パスワード確認</label>
        <input type="password" name="password_confirmation" id="password_confirmation" required>
    </div>

    <button type="submit">登録</button>
</form>
<a href="{{ route('users.index') }}">{{ __('一覧へ戻る') }}</a>
