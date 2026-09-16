from flask import Flask, request, jsonify
from rag.service.ingest_service import ingest_file
from rag.service.llm_service import generate_answer

app = Flask(__name__)
@app.post("/ingest")
def handle_ingest():
    try:
        data = request.json
        count = ingest_file(
            data["path"],
            data["document_id"],
            data["user_id"]
        )
        return {"success": True, "chunks": count}
    except Exception as e:
        return {"success": False, "message": str(e)}, 500

@app.post("/api/ask")
def handle_chat():
    try:
        data = request.get_json()

        if not data:
            return {
                "success": False,
                "message": "Request body không hợp lệ."
            }, 400

        query = data.get("query", "").strip()
        user_id = str(data.get("user_id", "")).strip()

        if not query:
            return {
                "success": False,
                "message": "Thiếu query."
            }, 400

        if not user_id:
            return {
                "success": False,
                "message": "Thiếu user_id."
            }, 400

        print(f"[ASK] user_id={user_id}")
        print(f"[ASK] query={query}")

        answer = generate_answer(query, user_id)

        print("[ASK] SUCCESS")

        return {
            "success": True,
            "answer": answer
        }, 200

    except Exception as e:
        import traceback

        print("\n========== /api/ask ERROR ==========")
        traceback.print_exc()
        print("====================================\n")

        return {
            "success": False,
            "message": str(e)
        }, 500
if __name__ == "__main__":
    app.run(host="127.0.0.1", port=8000, debug=False)