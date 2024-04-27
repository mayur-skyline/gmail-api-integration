<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gmail Integration Demo</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1 class="text-center">Gmail Integration Demo</h1>
        <h2 class="text-center">Welcome: {{ $email }}</h2>

        <!-- Display success or error messages -->
        @if(session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <br>

        <!-- Button to send an email -->
        <div class="text-center">
            <form action="{{ route('gmail.send') }}" method="post">
                @csrf
                <button type="submit" class="btn btn-primary">Send Email</button>
            </form>
        </div>

        <br>

        <!-- Button to fetch inbox messages -->
        <div class="text-center">
            <form action="{{ route('gmail.inbox') }}" method="get">
                @csrf
                <button type="submit" class="btn btn-info">Fetch Inbox</button>
            </form>
        </div>

        <br>

        <!-- Display inbox messages -->
        @if(isset($messages) && is_array($messages) && count($messages) > 0)
            <h3 class="mt-4">Inbox Messages:</h3>
            <ul class="list-group">
                @foreach($messages as $message)
                    <li class="list-group-item">
                        <strong>Subject:</strong> {{ $message['subject'] }}<br>
                        <strong>Date Time:</strong> {{ $message['date'] }}<br>
                        <strong>Mail content:</strong> {{ $message['content'] }}
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-alpha2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
