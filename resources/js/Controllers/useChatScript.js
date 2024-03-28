export function useChatScript({chatRoomId}){
    // Listen for new messages using Laravel Echo
    window.Echo.channel('chatroom.'+chatRoomId.toString())
        .listen('.message.sent', (e) => {
            console.log(e);
            // Append the new message to the chatroom
            const messages = document.getElementById('messages');
            const messageElement = document.createElement('p');
            messageElement.innerText = e.message;
            messages.appendChild(messageElement);
        });

    // Function to send a new message
    window.sendMessage = function() {
        const messageInput = document.getElementById('messageInput');
        const message = messageInput.value;
        axios.post('/chat/send-message', { message: message })
            .then(response => {
                console.log(response.data);
                // Clear the input field after sending
                messageInput.value = '';
            })
            .catch(error => console.error(error));

        console.log('sent message');
    };
}
