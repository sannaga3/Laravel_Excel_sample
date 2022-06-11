<h1>ユーザー編集</h1>

@foreach ($errors->all() as $error)
    <li>{{ $error }}</li>
@endforeach

<form action="{{ route('users.update', $user->id) }}" method="POST">
    @csrf
    @method('patch')

    <div>
        <label for="name">名前</label>
        <input type="text" name="name" id="name" value="{{ old('name') ?? $user->name }}" required>
    </div>

    <div>
        <label for="gender">性別</label>
        男性: <input type="radio" name="gender" id="gender" value="1"
            {{ old('gender') == '1' || $user->gender == '1' ? 'checked' : '' }}>
        女性: <input type="radio" name="gender" id="gender" value="2"
            {{ old('gender') == '2' || $user->gender == '2' ? 'checked' : '' }}>
    </div>

    <div>
        <label for="age">年齢</label>
        <input type="number" name="age" id="age" min="0" max="120" value="{{ old('age') ?? $user->age }}">
    </div>

    <div>
        <label for="address">住所</label>
        <input type="text" name="address" id="address" value="{{ old('address') ?? $user->address }}">
    </div>

    <div>
        <label for="tel">電話番号</label>
        <input type="tel" name="tel" id="tel" value="{{ old('tel') ?? $user->tel }}">
    </div>

    <div>
        <label for="email">メールアドレス</label>
        <input type="email" name="email" id="email" value="{{ old('email') ?? $user->email }}" required>
    </div>

    <button type="submit">更新</button>
</form>
<a href="{{ route('users.index') }}">{{ __('一覧へ戻る') }}</a>
