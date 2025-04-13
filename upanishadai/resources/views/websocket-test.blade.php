<!DOCTYPE html>
<html>
<head>
    <title>WebSocket Test</title>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
</head>
<body>
    <h1>WebSocket Test</h1>
    <div id="messages" style="height: 300px; border: 1px solid #ccc; overflow-y: scroll; padding: 10px;"></div>
    <button id="test-button">Send Test Event</button>

    <script>
        // Enable Pusher logging
        Pusher.logToConsole = true;
        
        // Initialize Pusher
        const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            wsHost: window.location.hostname,
            wsPort: 6001,
            forceTLS: false
        });
        
        // Subscribe to test channel
        const channel = pusher.subscribe('test-channel');
        
        // Log connection state
        pusher.connection.bind('state_change', states => {
            console.log('Connection state:', states);
            document.getElementById('messages').innerHTML += 
                `<p>Connection state changed from ${states.previous} to ${states.current}</p>`;
        });
        
        // Listen for events on the test channel
        channel.bind('App\\Events\\TestEvent', data => {
            console.log('Received event:', data);
            document.getElementById('messages').innerHTML += 
                `<p>Received: ${data.message}</p>`;
        });
        
        // Set up test button
        document.getElementById('test-button').addEventListener('click', () => {
            fetch('/test-websocket')
                .then(response => response.text())
                .then(text => {
                    console.log(text);
                    document.getElementById('messages').innerHTML += 
                        `<p>Sent test event</p>`;
                });
        });
    </script>
</body>
</html>