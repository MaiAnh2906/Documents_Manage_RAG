// const chatBody = document.getElementById("chatBody");

// const input = document.getElementById("question");

// document.getElementById("sendBtn").onclick = function(){

//     let text = input.value.trim();

//     if(text==="") return;

//     addUser(text);

//     input.value="";

//     loading();

//     setTimeout(()=>{

//         removeLoading();

//         addAI("Đây là câu trả lời từ AI.");

//     },1500);

// }

// function addUser(text){

//     chatBody.innerHTML+=`

//     <div class="message user">

//         <div class="bubble">

//             ${text}

//         </div>

//     </div>

//     `;

//     scrollBottom();

// }

// function addAI(text){

//     chatBody.innerHTML+=`

//     <div class="message ai">

//         <div class="avatar">

//             🤖

//         </div>

//         <div class="bubble">

//             ${text}

//         </div>

//     </div>

//     `;

//     scrollBottom();

// }

// function loading(){

//     chatBody.innerHTML+=`

//     <div class="message ai" id="loading">

//         <div class="avatar">

//             🤖

//         </div>

//         <div class="bubble">

//             Đang suy nghĩ...

//         </div>

//     </div>

//     `;

//     scrollBottom();

// }

// function removeLoading(){

//     document.getElementById("loading").remove();

// }

// function scrollBottom(){

//     chatBody.scrollTop=chatBody.scrollHeight;

// }

document.addEventListener("DOMContentLoaded", function () {
    const chatBody = document.getElementById("chatBody");
    const questionInput = document.getElementById("question");
    const sendBtn = document.getElementById("sendBtn");

    // Lấy ID không gian chat hiện tại từ URL (nếu có)
    let currentSpaceId = new URLSearchParams(window.location.search).get('id') || null;

    // Hàm cuộn xuống cuối khung chat
    function scrollToBottom() {
        chatBody.scrollTop = chatBody.scrollHeight;
    }
    scrollToBottom(); // Cuộn ngay khi vừa vào trang

    // Hàm in bong bóng chat ra màn hình
    function appendMessage(sender, text) {
        const messageDiv = document.createElement("div");
        messageDiv.className = `message ${sender}`;
        let avatar = sender === 'ai' ? '🤖' : '👤';
        messageDiv.innerHTML = `<div class="avatar">${avatar}</div><div class="bubble">${text.replace(/\n/g, '<br>')}</div>`;
        chatBody.appendChild(messageDiv);
        scrollToBottom();
    }

    // Hàm xử lý gửi tin nhắn
    async function handleSend() {
        const query = questionInput.value.trim();
        if (!query) return;

        // 1. In ngay câu hỏi ra màn hình và làm trống ô nhập
        appendMessage('user', query);
        questionInput.value = "";
        
        // 2. Hiện hiệu ứng chờ AI trả lời
        appendMessage('ai', "Đang phân tích tài liệu...");
        const aiMessages = document.querySelectorAll('.message.ai .bubble');
        const lastAiBubble = aiMessages[aiMessages.length - 1];

        try {
            // 3. Gọi API ngầm tới file PHP vừa tạo (Chỉnh lại đường dẫn nếu cần)
            const response = await fetch("qa.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ 
                    question: query,
                    space_id: currentSpaceId
                })
            });

            const data = await response.json();

            // 4. In kết quả thực tế đè lên chữ "Đang phân tích..."
            if (response.ok && data.success) {
                lastAiBubble.innerHTML = data.answer.replace(/\n/g, '<br>');
                
                if (!currentSpaceId && data.space_id) {
                    window.location.href = `qa.php?id=${data.space_id}`;
                }
            } else {
                lastAiBubble.innerHTML = "Lỗi: " + (data.message || "Không phản hồi.");
            }
        } catch (error) {
            lastAiBubble.innerHTML = "Lỗi mất kết nối máy chủ!";
            console.error(error);
        }
    }

    // Bắt sự kiện click nút gửi và phím Enter
    sendBtn.addEventListener("click", handleSend);
    questionInput.addEventListener("keypress", function (e) {
        if (e.key === "Enter" && !e.shiftKey) {
            e.preventDefault();
            handleSend();
        }
    });
});