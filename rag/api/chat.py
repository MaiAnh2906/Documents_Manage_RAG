from flask import Flask, request, jsonify
# Import hàm xử lý AI từ file llm_service của bạn
from rag.service.llm_service import get_ai_answer

app = Flask(__name__)
@app.post("/api/ask")
def ask():

    try:
        data = request.json
        # Gọi hàm từ tầng service để lấy câu trả lời
        answer = get_ai_answer(
            data["query"],
            data["user_id"]
        )
        print("Chat thành công. Đã tạo câu trả lời.")

        return {
            "success": True,
            "answer": answer
        }

    except Exception as e:
        print("Lỗi:", e)

        return {
            "success": False,
            "message": str(e)
        }, 500

if __name__ == "__main__":
    app.run(
        host="127.0.0.1",
        port=8000,
        debug=True
    )
print("OK")