<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel + Vite</title>
    @vite('resources/js/app.js')
</head>
<body>
<div>
    <button onclick="submitData()">Click Me</button>
    <button onclick="submitNewData()">Click New</button>
</div>

    <h1>Submit Your Data</h1>

<!-- The Form -->
<form id="userForm">
    @csrf
    <div>
        <label for="userType">User Type:</label>
        <select id="userType" name="selected_attribute" required>
            <option value="">-- Select a User Type --</option>
            <option value="admin">Admin</option>
            <option value="editor">Editor</option>
            <option value="subscriber">Subscriber</option>
        </select>
    </div>
</form>

<!-- External Submit Button -->
<button id="externalSubmitBtn">Submit Selection</button>

<!-- Response Message -->
<div id="responseMessage"></div>

@section('scripts')
    <!-- Include Vite -->
    @vite('resources/js/axios/custom.js')
@endsection

</body>
</html>
